<?php

namespace App\Swagger\Processors;

use ReflectionClass;
use OpenApi\Analysis;
use ReflectionMethod;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\OpenApi\Attributes\SuccessResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SuccessResponsesProcessor
{
    public function __invoke(Analysis $analysis)
    {
        foreach ($analysis->annotations as $annotation) {
            if (!in_array(get_class($annotation), [OA\Get::class, OA\Post::class, OA\Put::class, OA\Delete::class, OA\Patch::class])) {
                continue;
            }
            
            $this->processHeaders($annotation);
            $this->processParameters($annotation, $analysis);
            $this->processRequestBody($annotation, $analysis);
            $this->processUrlParameters($annotation,$analysis);
            $this->processResponse($annotation,$analysis);
        }
    }

    protected function processParameters($annotation, $analysis)
    {
        if (!in_array(get_class($annotation), [OA\Get::class])) {
            return;
        }

        $modelName = $this->getModelName($annotation);

        Log::info("Model Name: ", ['modelName' => $modelName]);
        $this->processMedia($annotation, $modelName );

        $modelClass = $modelName ? "App\\Models\\$modelName" : null;
        Log::info("Model Class: ", ['modelClass' => $modelClass]);

        if (!$modelClass || !class_exists($modelClass)) {
            return;
        }
        $ref = new \ReflectionClass($modelClass);
        $schemaName = $ref->getShortName();
        $modelInstance = new $modelClass();

        // Get model properties, ensuring arrays or empty arrays
        $scopedFilters = property_exists($modelInstance, 'scopedFilters') && is_array($modelInstance->scopedFilters) ? $modelInstance->scopedFilters : [];
        $exactFilters = property_exists($modelInstance, 'exactFilters') && is_array($modelInstance->exactFilters) ? $modelInstance->exactFilters : [];
        $allowedSorts = property_exists($modelInstance, 'allowedSorts') && is_array($modelInstance->allowedSorts) ? $modelInstance->allowedSorts : [];
        $accessors = $this->getModelAccessors($ref);
        $relations = property_exists($modelInstance, 'relationship') && is_array($modelInstance->relationship) ? $modelInstance->relationship : [];

        $this->processFilters($annotation, array_merge($scopedFilters, $exactFilters));
        $this->processRelation($annotation, $relations);
        $this->processPaginationSort($annotation, $allowedSorts);
        $this->processAppends($annotation, $accessors);
    }

    protected function getModelName($annotation)
    {
        $model = $annotation->x['model'] ?? null;
        if (!$model) {
            return null;
        }
        return class_basename($model);
        // $context = $annotation->_context ?? null;
        // // dd($context?->class, $context?->method);
        // if (!$context?->class || !$context?->method) {
        //     return;
        // }
        // $possibleNamespaces = [
        //     "App\\Http\\Controllers\\",
        //     "App\\Http\\Controllers\\Api\\",
        //     "App\\Http\\Controllers\\Api\\Admin\\",
        // ];
        
        // $foundController = null;
        // foreach ($possibleNamespaces as $ns) {
        //     $fqcn = $ns . class_basename($context->class);
        //     if (class_exists($fqcn)) {
        //         $foundController = $fqcn;
        //         break;
        //     }
        // }
        
        // if (!$foundController) {
        //     \Log::warning("Controller class not found for: {$context->class}");
        //     return;
        // }

        // // ✅ Use reflection to get the controller method attributes
        // try {
        //     $refMethod = new \ReflectionMethod($foundController, $context->method);
        // } catch (\ReflectionException $e) {
        //     return;
        // }

        // $attributes = $refMethod->getAttributes(\App\Swagger\Attributes\ApiModel::class);
        // $apiModelAttr = $attributes ? $attributes[0]->newInstance() : null;

        // if (!$apiModelAttr) {
        //     return; // skip if no ApiModel attribute defined
        // }

        // $modelClass = $apiModelAttr->model;
        // $modelName = class_basename($modelClass);
        return $modelName;
    }

    protected function processHeaders($annotation)
    {
        if (!is_array($annotation->parameters)) {
            $annotation->parameters = [];
        }
        
        $paramName = 'X-Requested-With';
        $exists = collect($annotation->parameters ?? [])->contains(function ($p) use ($paramName) {
            return is_object($p) && $p instanceof OA\Parameter && $p->name === $paramName;
        });
        
        if (!$exists) {
            $annotation->parameters[] = new OA\Parameter(
                name: $paramName,
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            );
        }
    }

    protected function getModelAccessors(ReflectionClass $ref): array
    {
        $modelClass = $ref->getName();
        $traitMethods = collect(class_uses_recursive($modelClass))
            ->flatMap(fn($trait) => (new ReflectionClass($trait))->getMethods())
            ->pluck('name')
            ->all();
            
        return collect($ref->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED))
            ->filter(
                fn($method) =>
                $method->getDeclaringClass()->getName() === $modelClass &&
                    !in_array($method->name, $traitMethods) &&
                    !$method->isStatic() &&
                    !$method->isAbstract() &&
                    !Str::startsWith($method->name, '__') &&
                    !Str::startsWith($method->name, 'scope')
            )
            ->map(function ($method) {
                $name = $method->name;
                
                if (Str::startsWith($name, 'get') && Str::endsWith($name, 'Attribute')) {
                    return Str::snake(Str::between($name, 'get', 'Attribute'));
                }

                if ($method->getReturnType()?->getName() === \Illuminate\Database\Eloquent\Casts\Attribute::class) {
                    return Str::snake($name);
                }

                return $name;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function processPaginationSort($annotation, array $allowedSorts)
    {
        $checkParamExist = $this->checkParamExist($annotation);
        if ($checkParamExist) {
            return;
        }
        $parameters = [
            'limit' => 'Pagination limit, -1 for all',
            'page'  => 'The page of results to return.',
            'sort'  => 'Sort by field' . ($allowedSorts ? ': ' . implode(', ', array_map(fn($field) => "$field (asc), -$field (desc)", $allowedSorts)) : '')
        ];

        foreach ($parameters as $param => $desc) {
            $exists = collect($annotation->parameters ?? [])->contains(function ($p) use ($param) {
                return is_object($p) && $p instanceof OA\Parameter && $p->name === $param;
            });
            if (!$exists) {
                $annotation->parameters[] = new OA\Parameter(
                    name: $param,
                    in: 'query',
                    description: $desc,
                    schema: new OA\Schema(type: 'string')
                );
            }
        }
    }

    protected function processRelation($annotation, $relations)
    {
        if (!empty($relations)) {
            $paramName = "include";
            $exists = collect($annotation->parameters ?? [])->contains(function ($p) use ($paramName) {
                return is_object($p) && $p instanceof OA\Parameter && $p->name === $paramName;
            });
            $relation_keys = implode(',', array_keys($relations));

            if (!$exists) {
                $annotation->parameters[] = new OA\Parameter(
                    name: 'include',
                    in: 'query',
                    description: "Include: `$relation_keys`",
                    schema: new OA\Schema(type: 'string')
                );
            }
        }
    }

    protected function processFilters($annotation, array $filters)
    {
        $checkParamExist = $this->checkParamExist($annotation);
        if ($checkParamExist) {
            return;
        }
        if (!is_array($annotation->parameters)) {
            $annotation->parameters = [];
        }

        if(!empty($filters)){
            foreach ($filters as $field) {
                $paramName = "filter[$field]";
                $exists = collect($annotation->parameters ?? [])->contains(function ($p) use ($paramName) {
                    return is_object($p) && $p instanceof OA\Parameter && $p->name === $paramName;
                });
                Log::info("exists:" . $exists);
                if (!$exists) {
                    $annotation->parameters[] = new OA\Parameter(
                        name: $paramName,
                        in: 'query',
                        description: "Filter by $field",
                        schema: new OA\Schema(type: 'string')
                    );
                }
            }
        }
    }

    protected function checkParamExist($annotation)
    {
        $path = $annotation->path ?? '';
        preg_match_all('/\{([^}]+)\}/', $path, $matches);
        $routeParams = $matches[1] ?? [];
        
        if (!empty($routeParams)) {
            return true;
        }
        return false;
    }

    protected function processAppends($annotation, array $accessors)
    {
        if (!empty($accessors)) {
            $paramName = "appends";
            $exists = collect($annotation->parameters ?? [])->contains(function ($p) use ($paramName) {
                return is_object($p) && $p instanceof OA\Parameter && $p->name === $paramName;
            });
            $accessorKeys = implode(',', $accessors);

            if (!$exists) {
                $annotation->parameters[] = new OA\Parameter(
                    name: 'appends',
                    in: 'query',
                    description: "Append attributes: `$accessorKeys`",
                    schema: new OA\Schema(type: 'string')
                );
            }
        }
    }

    protected function processMedia($annotation, $modelName)
    {
        if (!$modelName) {
            return;
        }

        // Convert plural tag to singular (e.g., "Products" -> "Product")
        $singularTag = rtrim($modelName, 's');
        if ($singularTag === $modelName && !str_ends_with($modelName, 's')) {
            // If it's already singular or doesn't end with 's', try both
            $singularTag = $modelName . 's';
        }

        // Try to find the resource class (try singular first, then plural)
        $resourceClass = "App\\Http\\Resources\\{$singularTag}\\Resource";
        if (!class_exists($resourceClass)) {
            $resourceClass = "App\\Http\\Resources\\{$modelName}\\Resource";
            if (!class_exists($resourceClass)) {
                $resourceClass = "App\\Http\\Resources\\{$singularTag}";
                if (!class_exists($resourceClass)) {
                    $resourceClass = "App\\Http\\Resources\\{$modelName}";
                    if (!class_exists($resourceClass)) {
                        return;
                    }
                }
            }
        }

        // Get media keys by scanning the resource source file
        $mediaKeys = [];
        try {
            $reflection = new ReflectionClass($resourceClass);
            $filePath = $reflection->getFileName();
            if ($filePath && file_exists($filePath)) {
                $content = file_get_contents($filePath);
                // whenLoadedMedia('key') or whenLoadedMedia("key")
                preg_match_all('/whenLoadedMedia\([\'\"]([^\'\"]+)[\'\"]\s*,?/i', $content, $matches);
                if (!empty($matches[1])) {
                    $mediaKeys = array_merge($mediaKeys, $matches[1]);
                }
                // whenLoadedMedia(config('media.tags.key'), ...)
                preg_match_all('/whenLoadedMedia\(config\([\'\"]media\\.tags\\.([^\'\"]+)[\'\"]\)\s*,?/i', $content, $configMatches);
                if (!empty($configMatches[1])) {
                    $mediaKeys = array_merge($mediaKeys, $configMatches[1]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error scanning resource for media keys: " . $e->getMessage());
        }

        $mediaKeys = array_values(array_unique($mediaKeys));
        Log::info("mediaKeys: ", ['mediaKeys' => $mediaKeys, 'resourceClass' => $resourceClass, 'modelName' => $modelName]);

        if (!empty($mediaKeys)) {
            $paramName = "media";
            $exists = collect($annotation->parameters ?? [])->contains(function ($p) use ($paramName) {
                return is_object($p) && $p instanceof OA\Parameter && $p->name === $paramName;
            });

            $mediaKeysStr = implode(',', $mediaKeys);
            $description = "Pass this keys to include perticular media : `{$mediaKeysStr}`";

            if (!$exists) {
                $annotation->parameters[] = new OA\Parameter(
                    name: 'media',
                    in: 'query',
                    description: $description,
                    schema: new OA\Schema(
                        type: 'string',
                        example: $mediaKeysStr
                    )
                );
            }
        }
    }

    protected function processRequestBody($annotation, $analysis)
    {
        if (!in_array(get_class($annotation), [OA\Post::class, OA\Put::class,OA\Patch::class])) {
            return;
        }

        $controllerInfo = $this->getControllerAndMethod($analysis, $annotation);
        
        if (!$controllerInfo || !($formRequestClass = $this->getFormRequestFromMethod($controllerInfo['method']))) {
            return;
        }

        $formRequest = new $formRequestClass();
        $rules = $formRequest->rules();
        $properties = [];
        $required = [];

        foreach ($rules as $field => $rule) {
            if($rule === 'nullable|array') {
                continue;
            }
            if ($this->isNestedField($field)) {
                $this->processNestedField($field, $rule, $properties, $required);
            } else {
                $schema = $this->mapRuleToSchema($rule);
                $properties[] = new OA\Property(
                    property: $field,
                    type: $schema['type'],
                    format: $schema['format'] ?? null,
                    items: $schema['items'] ?? null
                );
                if ($this->isFieldRequired($rule)) {
                    $required[] = $field;
                }
            }
        }

        $schema = new OA\Schema(
            type: 'object',
            properties: $properties,
            required: $required
        );

        $annotation->requestBody = new OA\RequestBody(
            required: true,
            content: [
                'application/json' => new OA\MediaType(
                    mediaType: 'application/json',
                    schema: $schema
                )
            ]
        );
    }

    protected function processUrlParameters($annotation, $analysis)
    {
        if (in_array(get_class($annotation), [OA\Post::class, OA\Put::class,OA\Get::class,OA\Delete::class,OA\Patch::class])) {

            $path = $annotation->path ?? '';
        
            // Extract all {params} from the path
            preg_match_all('/\{([^}]+)\}/', $path, $matches);
            $routeParams = $matches[1] ?? [];
        
            if (!empty($routeParams)) {
                if (!is_array($annotation->parameters)) {
                    $annotation->parameters = [];
                }
        
                foreach ($routeParams as $param) {
                    // Detect data type (simple heuristic)
                    $type = 'string';
                    if (str_contains($param, 'id') || str_ends_with($param, '_id')) {
                        $type = 'integer';
                    }
        
                    // Skip if already exists
                    $exists = collect($annotation->parameters)->contains(function ($p) use ($param) {
                        return is_object($p) && $p instanceof OA\Parameter && $p->name === $param;
                    });
        
                    if (!$exists) {
                        $annotation->parameters[] = new OA\Parameter(
                            name: $param,
                            in: 'path',
                            required: true,
                            description: "Parameter: {$param} for action",
                            // schema: new OA\Schema(type: $type)
                        );
                    }
                }
            }
        }
    }

    protected function processResponse($annotation, $analysis)
    {
        
        if (!is_array($annotation->responses)) {
            $annotation->responses = [];
        }

        // Get existing response codes
        $existingCodes = collect($annotation->responses)
            ->filter(fn($r) => $r instanceof OA\Response)
            ->pluck('response')
            ->toArray();

        // Common responses
        $commonResponses = [
            [
                'code' => '200',
                'desc' => 'Success.',
            ],
            [
                'code' => '400',
                'desc' => 'Bad Request',
            ],
            [
                'code' => '401',
                'desc' => 'Unauthorized access!',
            ],
            [
                'code' => '404',
                'desc' => 'Not found!',
            ],
        ];

        // Add only missing ones
        foreach ($commonResponses as $res) {
            if (!in_array($res['code'], $existingCodes)) {
                $annotation->responses[] = new OA\Response(
                    response: $res['code'],
                    description: $res['desc'],
                    // content: [
                    //     'application/json' => new OA\MediaType(
                    //         mediaType: 'application/json',
                    //         schema: new OA\Schema(
                    //             type: 'object',
                    //             properties: [
                    //                 new OA\Property(property: 'code', type: 'integer', example: (int)$res['code']),
                    //                 new OA\Property(property: 'message', type: 'string', example: $res['desc']),
                    //             ]
                    //         )
                    //     )
                    // ]
                );
            }
        }
        
    }

    protected function isNestedField(string $field): bool
    {
        return Str::contains($field, '.') || Str::contains($field, '*');
    }

    protected function getFormRequestFromMethod(ReflectionMethod $method): ?string
    {
        foreach ($method->getParameters() as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin() && (new ReflectionClass($type->getName()))->isSubclassOf(FormRequest::class)) {
                return $type->getName();
            }
        }
        return null;
    }

    protected function getControllerAndMethod(Analysis $analysis, $annotation): ?array
    {
        $context = $annotation->_context ?? null;
        $tag = $context->class;
        $methodName = $context->method;

        $tag = class_basename($tag); // ensures only "CategoryController"

        $possibleNamespaces = [
            "App\\Http\\Controllers\\",
            "App\\Http\\Controllers\\Api\\",
            "App\\Http\\Controllers\\Api\\Admin\\",
        ];

        // Loop through namespaces and find matching controller
        foreach ($possibleNamespaces as $namespace) {
            $controllerClass = $namespace . $tag;

            if (class_exists($controllerClass) && method_exists($controllerClass, $methodName)) {
                return [
                    'controller' => $controllerClass,
                    'method' => new ReflectionMethod($controllerClass, $methodName),
                ];
            }
        }

        return null;
    }

    protected function processNestedField(string $field, $rule, array &$properties, array &$required): void
    {
        
        $segments = explode('.', $field);
        $currentProperties = &$properties;

        foreach ($segments as $index => $segment) {
            $isLast = $index === count($segments) - 1;
            $isArrayField = $segment === '*';

            if ($isArrayField) {
                continue;
            }

            $existing = null;
            foreach ($currentProperties as &$prop) {
                if ($prop instanceof OA\Property && $prop->property === $segment) {
                    $existing = $prop;
                    break;
                }
            }
            unset($prop);

            if ($isLast) {
                $schema = $this->mapRuleToSchema($rule);

                if ($schema['type'] === 'array') {
                    $items = $schema['items'] ?? new OA\Items(type: 'string');
                    $currentProperties[] = new OA\Property(
                        property: $segment,
                        type: 'array',
                        items: $items
                    );
                } elseif ($schema['type'] === 'object' && isset($schema['properties'])) {
                    $currentProperties[] = new OA\Property(
                        property: $segment,
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            properties: $schema['properties'],
                            required: $schema['required'] ?? []
                        )
                    );
                } else {
                    $currentProperties[] = new OA\Property(
                        property: $segment,
                        type: $schema['type'],
                        format: $schema['format'] ?? null
                    );
                }

                if ($this->isFieldRequired($rule) && !in_array($segment, $required)) {
                    $required[] = $segment;
                }
            } else {
                if (!$existing) {
                    $existing = new OA\Property(
                        property: $segment,
                        type: 'object',
                        properties: []
                    );
                    $currentProperties[] = $existing;
                }

                if (!isset($existing->properties) || !is_array($existing->properties)) {
                    $existing->properties = [];
                }

                $currentProperties = &$existing->properties;
            }
        }
    }

    protected function mapRuleToSchema($rule): array
    {
        $rules = is_array($rule) ? $rule : explode('|', $rule);
        $rules = array_map(function ($r) {
            if (is_string($r)) {
                return $r;
            }
            if (is_object($r)) {
                // Enum rule → treat as string type
                if ($r instanceof \Illuminate\Validation\Rules\Enum) {
                    return 'string';
                }
    
                // If rule is Rule::in([...])
                if ($r instanceof \Illuminate\Validation\Rules\In) {
                    return 'string';
                }
    
                // Unknown object → skip string checks
                return null;
            }
            return null;
        }, $rules);
    
        // Remove null values
        $rules = array_filter($rules);

        $schema = ['type' => 'string'];
        foreach ($rules as $r) {
            if (str_contains($r, 'array')) {
                $schema = [
                    'type' => 'array',
                    'items' => new OA\Items(type: 'string')
                ];

                if (is_string($r) && str_contains($r, ':')) {
                    [$ruleName, $itemType] = explode(':', $r);
                    if ($ruleName === 'array') {
                        if ($itemType === 'integer' || $itemType === 'numeric') {
                            $schema['items'] = new OA\Items(type: 'integer');
                        } elseif ($itemType === 'boolean') {
                            $schema['items'] = new OA\Items(type: 'boolean');
                        } elseif ($itemType === 'object') {
                            $schema['items'] = new OA\Items(type: 'object', properties: []);
                        }
                    }
                }
            } elseif (str_contains($r, 'integer') || str_contains($r, 'numeric')) {
                $schema['type'] = 'integer';
            } elseif (str_contains($r, 'boolean')) {
                $schema['type'] = 'boolean';
            } elseif (str_contains($r, 'email')) {
                $schema['format'] = 'email';
            } elseif (str_contains($r, 'date')) {
                $schema['format'] = 'date';
            }
        }

        if (is_array($rule)) {
            $nestedProperties = [];
            $nestedRequired = [];

            foreach ($rule as $nestedField => $nestedRule) {
                if (str_starts_with($nestedField, 'items.*.')) {
                    $nestedFieldName = explode('.', $nestedField)[2];
                    $nestedSchema = $this->mapRuleToSchema($nestedRule);
                    $nestedProperties[] = new OA\Property(
                        property: $nestedFieldName,
                        type: $nestedSchema['type'],
                        format: $nestedSchema['format'] ?? null,
                        items: $nestedSchema['items'] ?? null
                    );
                    if ($this->isFieldRequired($nestedRule)) {
                        $nestedRequired[] = $nestedFieldName;
                    }
                }
            }

            if (!empty($nestedProperties)) {
                $schema['items'] = new OA\Items(
                    type: 'object',
                    properties: $nestedProperties,
                    required: $nestedRequired
                );
            }
        }

        return $schema;
    }

    protected function isFieldRequired($rule): bool
    {
        $rules = is_array($rule) ? $rule : explode('|', $rule);
        return in_array('required', $rules);
    }
}