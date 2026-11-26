<?php

namespace App\Support\Scramble;

use Throwable;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use App\Traits\BaseModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Plank\Mediable\Mediable;
use Dedoc\Scramble\Support\RouteInfo;
use Illuminate\Database\Eloquent\Model;
use Dedoc\Scramble\Support\EnumTransformer;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\Types\Type;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Dedoc\Scramble\Infer\Reflector\MethodReflector;
use Dedoc\Scramble\Infer\Services\FileNameResolver;
use Dedoc\Scramble\Support\Generator\Types\NumberType;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\Generator\Types\BooleanType;
use Dedoc\Scramble\Support\Generator\Types\IntegerType;
use Dedoc\Scramble\Support\Generator\Types\UnknownType;
use Dedoc\Scramble\Support\OperationExtensions\ParameterExtractor\ParameterExtractor;
use Dedoc\Scramble\Support\OperationExtensions\RulesExtractor\ParametersExtractionResult;

/**
 * Documents the headers and the BaseModel::getQB() query parameters of a route.
 *
 * Scramble cannot statically follow controller → service → getQB(), so this
 * extractor recovers the model from the JsonResource the action returns and
 * contributes the parameters that would otherwise stay undocumented. Headers are
 * declared in `config/scramble.php`, so an application built on this boilerplate
 * documents one without touching this class.
 *
 * Anything already documented by an attribute or a form request wins.
 */
final class GetQBParameterExtractor implements ParameterExtractor
{
    /** @var array<class-string, Model|null> */
    private static array $modelCache = [];

    /**
     * @param  ParametersExtractionResult[]  $parameterExtractionResults
     * @return ParametersExtractionResult[]
     */
    public function handle(RouteInfo $routeInfo, array $parameterExtractionResults): array
    {
        $endpoint = $this->resolveEndpoint($routeInfo);

        $parameters = array_merge(
            $this->headerParameters($routeInfo),
            $endpoint ? $this->queryParameters($endpoint['model'], $endpoint['isCollection']) : [],
        );

        $parameters = $this->onlyUndocumented($parameters, $parameterExtractionResults);

        if ($parameters === []) {
            return $parameterExtractionResults;
        }

        return [...$parameterExtractionResults, new ParametersExtractionResult($parameters)];
    }

    /**
     * The model behind the resource the action returns, and whether the action
     * returns a collection of them.
     *
     * @return array{model: Model, isCollection: bool}|null
     */
    private function resolveEndpoint(RouteInfo $routeInfo): ?array
    {
        $source = $this->actionSource($routeInfo);
        if ($source === null) {
            return null;
        }

        preg_match('/(\w+)::collection\s*\(/', $source, $collection);
        preg_match('/new\s+(\w*Resource)\s*\(/', $source, $single);

        $resourceName = $collection[1] ?? $single[1] ?? null;
        if ($resourceName === null) {
            return null;
        }

        $model = $this->modelFromResource($routeInfo, $resourceName);
        if ($model === null || ! in_array(BaseModel::class, class_uses_recursive($model), true)) {
            return null;
        }

        return [
            'model' => $model,
            'isCollection' => isset($collection[1]),
        ];
    }

    private function actionSource(RouteInfo $routeInfo): ?string
    {
        try {
            $reflector = $routeInfo->getActionReflector();

            return $reflector instanceof MethodReflector
                ? $reflector->getMethodCode()
                : $reflector->getCode();
        } catch (Throwable) {
            return null;
        }
    }

    private function modelFromResource(RouteInfo $routeInfo, string $resourceName): ?Model
    {
        try {
            $fileName = $routeInfo->reflectionMethod()?->getFileName();
            if (! $fileName) {
                return null;
            }

            // Scramble's resolver understands aliased, grouped and relative imports.
            $resourceClass = (FileNameResolver::createForFile($fileName))($resourceName);
            if (! class_exists($resourceClass)) {
                return null;
            }

            if (array_key_exists($resourceClass, self::$modelCache)) {
                return self::$modelCache[$resourceClass];
            }

            $reflection = new ReflectionClass($resourceClass);
            if (! $reflection->hasProperty('model')) {
                return self::$modelCache[$resourceClass] = null;
            }

            // The resource is never booted, its `$model` default is all that matters.
            $modelClass = $reflection->getProperty('model')
                ->getValue($reflection->newInstanceWithoutConstructor());

            return self::$modelCache[$resourceClass] = is_string($modelClass) && is_subclass_of($modelClass, Model::class)
                ? new $modelClass
                : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return Parameter[]
     */
    private function headerParameters(RouteInfo $routeInfo): array
    {
        $uri = $routeInfo->route->uri();

        return collect(Arr::wrap(config('scramble.headers', [])))
            ->filter(fn (mixed $header): bool => is_array($header) && isset($header['name']))
            ->filter(function (array $header) use ($uri): bool {
                $include = Arr::wrap($header['include'] ?? '*');
                $exclude = Arr::wrap($header['exclude'] ?? []);

                return Str::is($include, $uri) && ! Str::is($exclude, $uri);
            })
            ->map(function (array $header): Parameter {
                $parameter = Parameter::make((string) $header['name'], 'header')
                    ->description((string) ($header['description'] ?? ''))
                    ->setSchema(Schema::fromType(new StringType));

                $parameter->setAttribute('required', (bool) ($header['required'] ?? false));

                if (isset($header['example'])) {
                    $parameter->example($header['example']);
                }

                return $parameter;
            })
            ->values()
            ->all();
    }

    /**
     * Filtering, sorting and pagination only apply to a collection; the shaping
     * parameters apply to a single resource too, because getQB() builds both.
     *
     * @return Parameter[]
     */
    private function queryParameters(Model $model, bool $isCollection): array
    {
        $reflection = new ReflectionClass($model);
        $casts = $model->getCasts();
        $fields = $this->stringList($model->getQueryFields()); // @phpstan-ignore method.notFound

        return array_merge(
            $isCollection ? $this->filterParameters($model, $reflection, $casts, $fields) : [],
            $isCollection ? $this->sortAndPaginationParameters($model, $reflection, $fields) : [],
            $this->includeParameters($model),
            $this->sparseFieldParameters($model, $fields),
            $this->appendParameters($model, $reflection),
            $this->mediaParameters($model, $reflection),
        );
    }

    /**
     * @param  ReflectionClass<Model>  $reflection
     * @param  array<string, string>  $casts
     * @param  array<int, string>  $fields
     * @return Parameter[]
     */
    private function filterParameters(Model $model, ReflectionClass $reflection, array $casts, array $fields): array
    {
        // Keyed by field, so a field that is both fillable and a filter is documented once.
        $filters = array_merge(
            array_fill_keys($fields, 'partial'),
            array_fill_keys($this->modelProperty($reflection, $model, 'scopedFilters'), 'scope'),
            array_fill_keys($this->modelProperty($reflection, $model, 'exactFilters'), 'exact'),
        );

        $name = $this->parameterName('filter');
        $parameters = [];

        foreach ($filters as $field => $kind) {
            $description = match ($kind) {
                'scope' => "Filter by the `{$field}` scope.",
                'exact' => "Filter by exact `{$field}`.",
                default => "Filter by `{$field}` (partial match).",
            };

            $parameters[] = $this->queryParameter(
                "{$name}[{$field}]",
                $description,
                $this->fieldType($field, $casts)
            );
        }

        return $parameters;
    }

    /**
     * @param  ReflectionClass<Model>  $reflection
     * @param  array<int, string>  $fields
     * @return Parameter[]
     */
    private function sortAndPaginationParameters(Model $model, ReflectionClass $reflection, array $fields): array
    {
        $defaultSort = implode(',', $this->modelProperty($reflection, $model, 'defaultSort'));

        return [
            $this->queryParameter(
                $this->parameterName('sort'),
                'Available sorts: ' . $this->backticks($fields) . '. Prefix with `-` for descending order.'
                    . ($defaultSort ? " Default: `{$defaultSort}`." : '')
            ),
            $this->queryParameter('limit', 'Records per page, `-1` returns every record.', new IntegerType)
                ->example(config('site.pagination_limit')),
            $this->queryParameter('page', 'Page number of the paginated result.', new IntegerType)
                ->example(1),
        ];
    }

    /**
     * @return Parameter[]
     */
    private function includeParameters(Model $model): array
    {
        $includes = $this->stringList($model->getIncludes()); // @phpstan-ignore method.notFound

        if ($includes === []) {
            return [];
        }

        return [
            $this->queryParameter(
                $this->parameterName('include'),
                'Available includes: ' . $this->backticks($includes) . '. Separate multiple with a comma.'
            ),
        ];
    }

    /**
     * @param  array<int, string>  $fields
     * @return Parameter[]
     */
    private function sparseFieldParameters(Model $model, array $fields): array
    {
        $name = $this->parameterName('fields');

        $parameters = [
            $this->queryParameter(
                "{$name}[{$model->getTable()}]",
                'Comma separated subset of ' . $this->backticks($fields) . ' to return.'
            ),
        ];

        foreach ($this->relatedFields($model) as $alias => $relatedFields) {
            $parameters[] = $this->queryParameter(
                "{$name}[{$alias}]",
                'Comma separated subset of ' . $this->backticks($relatedFields)
                    . " to return for the `{$alias}` include, which must be requested as well."
            );
        }

        return $parameters;
    }

    /**
     * The selectable fields of every include, keyed by the alias the API uses.
     *
     * @return array<string, array<int, string>>
     */
    private function relatedFields(Model $model): array
    {
        $relationship = $model->getRelationship(); // @phpstan-ignore method.notFound
        $relations = [];

        if (! is_array($relationship)) {
            return $relations;
        }

        foreach ($relationship as $alias => $relation) {
            $relatedClass = is_array($relation) ? ($relation['model'] ?? null) : null;

            if (! is_string($relatedClass) || ! is_subclass_of($relatedClass, Model::class)) {
                continue;
            }

            $related = new $relatedClass;

            $fields = method_exists($related, 'getQueryFields')
                ? $related->getQueryFields()
                : $related->getFillable();

            $relations[(string) $alias] = $this->stringList([$related->getKeyName(), ...$fields]);
        }

        return $relations;
    }

    /**
     * @param  ReflectionClass<Model>  $reflection
     * @return Parameter[]
     */
    private function appendParameters(Model $model, ReflectionClass $reflection): array
    {
        $appends = array_values(array_unique([
            ...$this->modelProperty($reflection, $model, 'appends'),
            ...$this->accessors($reflection, $model),
        ]));

        if ($appends === []) {
            return [];
        }

        return [
            $this->queryParameter(
                'appends',
                'Comma separated accessors to append: ' . $this->backticks($appends) . '.'
            ),
        ];
    }

    /**
     * @param  ReflectionClass<Model>  $reflection
     * @return Parameter[]
     */
    private function mediaParameters(Model $model, ReflectionClass $reflection): array
    {
        if (! in_array(Mediable::class, class_uses_recursive($model), true)) {
            return [];
        }

        $tags = $this->modelProperty($reflection, $model, 'mediaTags');

        return [
            $this->queryParameter(
                'media',
                $tags
                    ? 'Comma separated media tags to include: ' . $this->backticks($tags) . '.'
                    : 'Comma separated media tags to include.'
            ),
        ];
    }

    /**
     * @param  Parameter[]  $parameters
     * @param  ParametersExtractionResult[]  $results
     * @return Parameter[]
     */
    private function onlyUndocumented(array $parameters, array $results): array
    {
        $documented = collect($results)
            ->flatMap(fn (ParametersExtractionResult $result): array => $result->parameters)
            ->map(fn (Parameter $parameter): string => $parameter->name)
            ->all();

        return array_values(array_filter(
            $parameters,
            fn (Parameter $parameter): bool => ! in_array($parameter->name, $documented, true)
        ));
    }

    /**
     * Public methods returning an Eloquent Attribute, declared on the model
     * itself or on one of its own traits.
     *
     * @param  ReflectionClass<Model>  $reflection
     * @return array<int, string>
     */
    private function accessors(ReflectionClass $reflection, Model $model): array
    {
        $declaring = [$model::class, ...array_keys($reflection->getTraits())];
        $accessors = [];

        // Accessors are conventionally protected, e.g. `protected function name(): Attribute`.
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED) as $method) {
            if (! in_array($method->getDeclaringClass()->getName(), $declaring, true)) {
                continue;
            }

            $returnType = $method->getReturnType();
            if ($returnType instanceof ReflectionNamedType && $returnType->getName() === Attribute::class) {
                $accessors[] = Str::snake($method->getName());
            }
        }

        return $accessors;
    }

    /**
     * Map the field's cast to the matching OpenAPI type, so that
     * `filter[is_active]` is a boolean and `filter[status]` lists its enum values.
     *
     * @param  array<string, string>  $casts
     */
    private function fieldType(string $field, array $casts): Type
    {
        $cast = $casts[$field] ?? null;
        if ($cast === null) {
            return new StringType;
        }

        if (enum_exists($cast)) {
            $type = EnumTransformer::make($cast)->transform();

            // Pure, non backed, enums cannot be filtered by value.
            return $type instanceof UnknownType ? new StringType : $type;
        }

        // Casts may carry a modifier, e.g. `decimal:2` or `encrypted:array`.
        return match (strtolower(explode(':', $cast)[0])) {
            'int', 'integer' => new IntegerType,
            'float', 'double', 'real', 'decimal' => new NumberType,
            'bool', 'boolean' => new BooleanType,
            default => new StringType,
        };
    }

    /**
     * Read a BaseModel configuration property, declared protected on the model.
     *
     * @param  ReflectionClass<Model>  $reflection
     * @return array<int, string>
     */
    private function modelProperty(ReflectionClass $reflection, object $instance, string $property): array
    {
        if (! $reflection->hasProperty($property)) {
            return [];
        }

        return $this->stringList($reflection->getProperty($property)->getValue($instance));
    }

    /**
     * @return array<int, string>
     */
    private function stringList(mixed $values): array
    {
        return array_values(array_unique(array_filter(Arr::wrap($values), is_string(...))));
    }

    /**
     * Spatie's query parameter names are configurable, so never hardcode them.
     */
    private function parameterName(string $parameter): string
    {
        return (string) config("query-builder.parameters.{$parameter}", $parameter);
    }

    private function queryParameter(string $name, string $description, ?Type $type = null): Parameter
    {
        $parameter = Parameter::make($name, 'query')
            ->description($description)
            ->setSchema(Schema::fromType($type ?? new StringType));

        $parameter->setAttribute('isFlat', str_contains($name, '['));
        $parameter->setAttribute('isInQuery', true);

        return $parameter;
    }

    /**
     * @param  array<int, string>  $values
     */
    private function backticks(array $values): string
    {
        return '`' . implode('`, `', $values) . '`';
    }
}
