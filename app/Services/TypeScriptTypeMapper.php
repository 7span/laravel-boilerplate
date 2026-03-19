<?php

namespace App\Services;

use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TypeScriptTypeMapper
{
    /**
     * Map a FormRequest's rules to a TypeScript interface.
     */
    public function mapRequest(string $class): string
    {
        if (! class_exists($class)) {
            return '';
        }

        $ref = new ReflectionClass($class);
        $className = $ref->getShortName();
        if (! Str::endsWith($className, 'Request')) {
            $className .= 'Request';
        }
        $instance = $ref->newInstanceWithoutConstructor();

        $rules = $instance->rules();
        $properties = [];
        $nested = [];

        foreach ($rules as $field => $rule) {
            if (Str::contains($field, '.') || Str::contains($field, '*')) {
                $this->handleNestedRule($field, $rule, $nested);

                continue;
            }

            $type = $this->mapValidationRuleToTs($rule);
            $isOptional = ! $this->isRuleRequired($rule);
            $properties[$field] = "    $field" . ($isOptional ? '?' : '') . ": $type;";
        }

        foreach ($nested as $field => $data) {
            $type = $data['type'];
            $isOptional = $data['optional'];
            $properties[$field] = "    $field" . ($isOptional ? '?' : '') . ": $type;";
        }

        return "export interface $className {\n" . implode("\n", array_values($properties)) . "\n}";
    }

    public function mapModel(string $class): string
    {
        if (! class_exists($class) || ! is_subclass_of($class, Model::class)) {
            return '';
        }

        $ref = new ReflectionClass($class);
        $className = $ref->getShortName();
        $instance = new $class;

        $fillable = $instance->getFillable();
        $casts = $instance->getCasts();
        $appends = $instance->getAppends();

        // Discover Accessors via Reflection
        $discoveredAccessors = [];
        $accessorTypes = [];
        foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED) as $method) {
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            $methodName = $method->getName();
            $returnType = $method->getReturnType();
            $isAttribute = $returnType && $returnType instanceof \ReflectionNamedType && $returnType->getName() === 'Illuminate\Database\Eloquent\Casts\Attribute';

            if ($isAttribute) {
                $field = Str::snake($methodName);
                $discoveredAccessors[] = $field;
                // For Attribute methods, we can't easily get the underlying type without executing it,
                // but we might want to default to something better than 'any' if possible.
                // For now, we still default 'Attribute' to 'any' in mapPhpTypeToTs, but we can detect it.
                $accessorTypes[$field] = 'any';
            } elseif (preg_match('/^get(\w+)Attribute$/', $methodName, $matches)) {
                $field = Str::snake($matches[1]);
                $discoveredAccessors[] = $field;
                if ($returnType) {
                    $accessorTypes[$field] = $this->formatReflectionType($returnType);
                }
            }
        }

        $standardFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $allFields = array_unique(array_merge($standardFields, $fillable, $appends, $discoveredAccessors));
        $properties = [];

        foreach ($allFields as $field) {
            $phpType = $accessorTypes[$field] ?? ($casts[$field] ?? 'string');
            $tsType = $this->mapPhpTypeToTs($phpType);

            // Accessors and appends are typically optional in raw model data
            $isOptional = ! in_array($field, $fillable) && ! in_array($field, $standardFields);

            // Heuristic for accessors: if it's not a casted field and not fillable and no explicit type found
            if ($isOptional && ! isset($casts[$field]) && ! isset($accessorTypes[$field])) {
                $tsType = 'any';
            }

            $properties[$field] = "    $field" . ($isOptional ? '?' : '') . ": $tsType;";
        }

        return "export interface $className {\n" . implode("\n", array_values($properties)) . "\n}";
    }

    /**
     * Map a JsonResource to a TypeScript interface.
     */
    public function mapResource(string $class): string
    {
        if (! class_exists($class) || ! is_subclass_of($class, JsonResource::class)) {
            return '';
        }

        $ref = new ReflectionClass($class);
        $className = $ref->getShortName();
        if ($className === 'Resource') {
            $className = basename(dirname($ref->getFileName()));
        }
        $className .= 'Response';

        // Resources are tricky because toArray() often contains dynamic logic.
        $properties = [];

        // Check for $model property
        $modelClass = null;
        if ($ref->hasProperty('model')) {
            $prop = $ref->getProperty('model');
            $prop->setAccessible(true);
            $modelClass = $prop->getValue($ref->newInstanceWithoutConstructor());
        }

        if ($modelClass && class_exists($modelClass)) {
            $modelInstance = new $modelClass;
            $fields = array_unique(array_merge($modelInstance->getFillable(), $modelInstance->getAppends(), ['id', 'created_at', 'updated_at']));
            $casts = $modelInstance->getCasts();
            $modelRef = new ReflectionClass($modelClass);

            foreach ($fields as $field) {
                if (in_array($field, $modelInstance->getHidden())) {
                    continue;
                }
                $tsType = $this->mapPhpTypeToTs($casts[$field] ?? 'string');
                $properties[$field] = "    $field: $tsType;";
            }
        } else {
            $modelRef = null;
        }

        // Attempt to parse toArray() for manual additions
        $filePath = $ref->getFileName();
        if ($filePath && file_exists($filePath)) {
            $content = file_get_contents($filePath);

            // Extract imports to resolve aliases (e.g., use App\Http\Resources\User\Resource as UserResource)
            preg_match_all('/use\s+([^\s;]+)(?:\s+as\s+([^\s;]+))?;/i', $content, $importMatches);
            $imports = [];
            foreach ($importMatches[1] as $index => $fullClass) {
                $alias = $importMatches[2][$index] ?: class_basename($fullClass);
                $imports[$alias] = $fullClass;
            }

            // Regex to find $data['key'] = ... (skipping commented out lines)
            preg_match_all('/^\s*(?!\/\/|\*)\$data\[[\'"]([^\'"]+)[\'"]\]\s*=\s*(.+);/m', $content, $matches);
            foreach ($matches[1] as $index => $key) {
                $value = $matches[2][$index];
                $type = $this->inferTypeFromValue($value, $imports, $ref, $modelRef);

                $properties[$key] = "    $key: $type;";
            }
        }

        return "export interface $className {\n" . implode("\n", array_values($properties)) . "\n}";
    }

    protected function handleNestedRule(string $field, $rule, &$nested): void
    {
        $parts = explode('.', $field);
        $root = $parts[0];

        if (! isset($nested[$root])) {
            $nested[$root] = ['type' => 'any', 'optional' => true, 'fields' => []];
        }

        if (count($parts) > 1 && $parts[1] === '*') {
            // It's an array of items or primitive
            if (count($parts) === 2) {
                // profile.* => string[]
                $type = $this->mapValidationRuleToTs($rule);
                $nested[$root]['type'] = $type . '[]';
            } else {
                // profile.*.filename => { filename: string }[]
                $nested[$root]['type'] = 'any[]'; // Simplified for now
            }
        } else {
            // It's an object property: profile.filename
            $nested[$root]['type'] = 'any';
        }
    }

    protected function inferTypeFromValue(string $value, array $imports = [], ?ReflectionClass $resourceRef = null, ?ReflectionClass $modelRef = null): string
    {
        // 1. Check for explicit collection: UserResource::collection(...)
        if (preg_match('/(\w+Resource)::collection\(/', $value, $matches)) {
            $resourceAlias = $matches[1];

            return $this->resolveResourceType($resourceAlias, $imports) . '[]';
        }

        // 2. Check for Collection class: new UserResourceCollection(...)
        if (preg_match('/new\s+(\w+ResourceCollection)\(/', $value, $matches)) {
            $resourceAlias = Str::replaceLast('Collection', '', $matches[1]);

            return $this->resolveResourceType($resourceAlias, $imports) . '[]';
        }

        // 3. Check for single Resource: new UserResource(...)
        if (preg_match('/new\s+(\w+Resource)\(/', $value, $matches)) {
            $resourceAlias = $matches[1];

            return $this->resolveResourceType($resourceAlias, $imports);
        }

        // 4. Check for method calls: $this->someMethod()
        if (preg_match('/\$this->(\w+)\(/', $value, $matches)) {
            $methodName = $matches[1];

            // Try Resource first
            if ($resourceRef && $resourceRef->hasMethod($methodName)) {
                $method = $resourceRef->getMethod($methodName);
                if ($returnType = $method->getReturnType()) {
                    return $this->mapPhpTypeToTs($this->formatReflectionType($returnType));
                }
            }

            // Try Model
            if ($modelRef && $modelRef->hasMethod($methodName)) {
                $method = $modelRef->getMethod($methodName);
                if ($returnType = $method->getReturnType()) {
                    return $this->mapPhpTypeToTs($this->formatReflectionType($returnType));
                }
            }
        }

        // 5. Fallback for anonymous or collections without explicit Resource names in string
        if (Str::contains($value, 'Resource::collection')) {
            return 'any[]';
        }
        if (Str::contains($value, 'new ') && Str::contains($value, 'Resource')) {
            return 'any';
        }
        if (Str::contains($value, 'whenLoadedMedia')) {
            return 'any';
        }

        return 'any';
    }

    protected function formatReflectionType(\ReflectionType $type): string
    {
        if ($type instanceof \ReflectionNamedType) {
            $name = $type->getName();

            return ($type->allowsNull() ? '?' : '') . $name;
        }

        if ($type instanceof \ReflectionUnionType) {
            $types = array_map(function ($t) {
                return $this->formatReflectionType($t);
            }, $type->getTypes());

            // Simplify ?type | null to ?type
            if (in_array('null', $types) || in_array('?null', $types)) {
                $filtered = array_filter($types, fn ($t) => ! in_array($t, ['null', '?null']));

                return '?' . implode('|', $filtered);
            }

            return implode('|', $types);
        }

        if ($type instanceof \ReflectionIntersectionType) {
            $types = array_map(function ($t) {
                return $this->formatReflectionType($t);
            }, $type->getTypes());

            return implode('&', $types);
        }

        return 'any';
    }

    protected function resolveResourceType(string $alias, array $imports): string
    {
        if (isset($imports[$alias])) {
            $className = class_basename($imports[$alias]);
            if ($className === 'Resource') {
                $parts = explode('\\', $imports[$alias]);
                $folder = $parts[count($parts) - 2] ?? '';

                return $folder ? $folder . 'Response' : 'any';
            }

            return Str::replaceLast('Resource', '', $className) . 'Response';
        }

        return Str::replaceLast('Resource', '', $alias) . 'Response';
    }

    /**
     * Map Laravel validation rules to TypeScript types.
     */
    protected function mapValidationRuleToTs($rule): string
    {
        $rules = is_array($rule) ? $rule : explode('|', (string) $rule);
        $type = 'any';
        $isNullable = in_array('nullable', $rules);

        $stringHeuristics = ['max', 'min', 'unique', 'required', 'required_with', 'required_if', 'email', 'url', 'string'];
        $numberHeuristics = ['integer', 'numeric', 'digits'];
        $booleanHeuristics = ['boolean'];

        foreach ($rules as $r) {
            $ruleName = is_string($r) ? explode(':', $r)[0] : '';

            if (in_array($ruleName, $numberHeuristics)) {
                $type = 'number';
                break;
            } elseif (in_array($ruleName, $booleanHeuristics)) {
                $type = 'boolean';
                break;
            } elseif (in_array($ruleName, $stringHeuristics)) {
                $type = 'string';
            } elseif ($ruleName === 'array') {
                $type = 'any[]';
            } elseif (is_string($r) && Str::startsWith($r, 'in:')) {
                $options = explode(',', Str::after($r, 'in:'));
                $type = implode(' | ', array_map(fn ($o) => "'$o'", $options));
                break;
            } elseif ($r instanceof \Illuminate\Validation\Rules\Enum) {
                $type = 'string';
                break;
            }
        }

        return $type . ($isNullable ? ' | null' : '');
    }

    protected function isRuleRequired($rule): bool
    {
        $rules = is_array($rule) ? $rule : explode('|', (string) $rule);
        $requiredRules = ['required', 'accepted', 'present'];
        foreach ($rules as $r) {
            if (in_array($r, $requiredRules)) {
                return true;
            }
        }

        return false;
    }

    protected function mapPhpTypeToTs(string $phpType): string
    {
        // Handle Union Types: string|int
        if (Str::contains($phpType, '|')) {
            $parts = explode('|', $phpType);
            $tsParts = array_unique(array_map(fn ($p) => $this->mapPhpTypeToTs($p), $parts));

            return implode(' | ', $tsParts);
        }

        // Handle Intersection Types: A&B
        if (Str::contains($phpType, '&')) {
            $parts = explode('&', $phpType);
            $tsParts = array_unique(array_map(fn ($p) => $this->mapPhpTypeToTs($p), $parts));

            return implode(' & ', $tsParts);
        }

        $isNullable = Str::startsWith($phpType, '?');
        $baseType = $isNullable ? substr($phpType, 1) : $phpType;

        $tsType = match ($baseType) {
            'int', 'integer', 'timestamp' => 'number',
            'bool', 'boolean' => 'boolean',
            'float', 'double', 'decimal' => 'number',
            'array', 'json', 'object' => 'any',
            'string' => 'string',
            'void', 'null' => 'null',
            'mixed' => 'any',
            default => 'any',
        };

        return $tsType . ($isNullable ? ' | null' : '');
    }
}
