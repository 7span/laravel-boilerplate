<?php

namespace App\Support\Scramble;

use Throwable;
use ReflectionClass;
use App\Traits\BaseModel;
use Dedoc\Scramble\Support\RouteInfo;
use Illuminate\Database\Eloquent\Model;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\OperationExtensions\ParameterExtractor\ParameterExtractor;
use Dedoc\Scramble\Support\OperationExtensions\RulesExtractor\ParametersExtractionResult;

/**
 * Custom Scramble parameter extractor for controllers that use services
 * with BaseModel::getQB(). Scramble Pro's built-in QueryBuilder extension
 * cannot trace through the service → getQB() chain statically, so this
 * extractor bridges the gap by reading the model's BaseModel properties
 * (scopedFilters, exactFilters, fillable, relationship, defaultSort)
 * directly at documentation generation time.
 */
class GetQBParameterExtractor implements ParameterExtractor
{
    /**
     * @param  ParametersExtractionResult[]  $parameterExtractionResults
     * @return ParametersExtractionResult[]
     */
    public function handle(RouteInfo $routeInfo, array $parameterExtractionResults): array
    {
        // Skip if Scramble Pro's QueryBuilder extension already documented query params
        $alreadyHandled = collect($parameterExtractionResults)
            ->flatMap(fn ($r) => $r->parameters)
            ->some(fn ($p) => $p->getAttribute('isInQuery'));

        if ($alreadyHandled) {
            return $parameterExtractionResults;
        }

        $modelClass = $this->resolveModelClass($routeInfo);

        if (! $modelClass || ! class_exists($modelClass)) {
            return $parameterExtractionResults;
        }

        $model = new $modelClass;

        if (! in_array(BaseModel::class, class_uses_recursive($model))) {
            return $parameterExtractionResults;
        }

        $parameters = $this->buildParameters($model);

        if (empty($parameters)) {
            return $parameterExtractionResults;
        }

        return [...$parameterExtractionResults, new ParametersExtractionResult($parameters)];
    }

    /**
     * Resolve the Eloquent model class from the controller action source by finding
     * the JsonResource being returned and reading its $model property.
     */
    private function resolveModelClass(RouteInfo $routeInfo): ?string
    {
        try {
            $reflectionMethod = $routeInfo->reflectionMethod();

            if (! $reflectionMethod) {
                return null;
            }

            $startLine = $reflectionMethod->getStartLine();
            $endLine = $reflectionMethod->getEndLine();
            $lines = file($reflectionMethod->getFileName());
            $methodSource = implode('', array_slice($lines, $startLine - 1, $endLine - $startLine + 1));

            // Find patterns like: SomeResource::collection( or new SomeResource(
            if (! preg_match('/(\w+)::collection\s*\(/', $methodSource, $matches)) {
                if (! preg_match('/new\s+(\w+Resource)\s*\(/', $methodSource, $matches)) {
                    return null;
                }
            }

            $resourceShortName = $matches[1];
            $controllerSource = file_get_contents($reflectionMethod->getFileName());

            // Resolve fully-qualified class from use statements (handles aliases like `use Foo as Bar`)
            $resourceClass = $this->resolveClassFromUseStatements($controllerSource, $resourceShortName);

            if (! $resourceClass || ! class_exists($resourceClass)) {
                return null;
            }

            $reflection = new ReflectionClass($resourceClass);

            if (! $reflection->hasProperty('model')) {
                return null;
            }

            $prop = $reflection->getProperty('model');
            $prop->setAccessible(true);

            return $prop->getValue($reflection->newInstanceWithoutConstructor());
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Parse use statements from a PHP file to resolve a short class name to its FQCN.
     */
    private function resolveClassFromUseStatements(string $source, string $shortName): ?string
    {
        // Match: use Foo\Bar\ClassName; or use Foo\Bar\ClassName as Alias;
        if (preg_match(
            '/use\s+([\w\\\\]+\\\\' . preg_quote($shortName, '/') . ')\s*(?:as\s+\w+)?\s*;/',
            $source,
            $matches
        )) {
            return $matches[1];
        }

        // Match aliased: use Foo\Bar\RealName as ShortName;
        if (preg_match(
            '/use\s+([\w\\\\]+)\s+as\s+' . preg_quote($shortName, '/') . '\s*;/',
            $source,
            $matches
        )) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Build OpenAPI query parameters from the model's BaseModel configuration.
     *
     * @return Parameter[]
     */
    private function buildParameters(Model $model): array
    {
        $parameters = [];
        $fillable = $model->getFillable();
        $scopedFilters = $model->scopedFilters ?? [];
        $exactFilters = $model->exactFilters ?? [];
        $defaultSort = $model->defaultSort ?? null;

        // Regular filters (all fillable fields not handled by scope/exact)
        foreach ($fillable as $field) {
            if (in_array($field, $scopedFilters) || in_array($field, $exactFilters)) {
                continue;
            }

            $parameters[] = $this->makeFilterParam($field, "Filter by {$field}.");
        }

        // Scope filters
        foreach ($scopedFilters as $filter) {
            $parameters[] = $this->makeFilterParam($filter, "Filter by {$filter} (scoped).");
        }

        // Exact filters
        foreach ($exactFilters as $filter) {
            $parameters[] = $this->makeFilterParam($filter, "Filter by exact {$filter}.");
        }

        // Sort parameter
        $sortableFields = array_merge(['id'], $fillable);
        $sortsList = implode(', ', array_map(fn ($s) => "`{$s}`", $sortableFields));
        $sortDescription = "Available sorts: {$sortsList}. Prefix with `-` for descending order.";

        if ($defaultSort) {
            $sortDescription .= " Default: `{$defaultSort}`.";
        }

        $sortParam = (new Parameter('sort', 'query'))
            ->description($sortDescription)
            ->setSchema(Schema::fromType(new StringType));
        $sortParam->setAttribute('isInQuery', true);
        $parameters[] = $sortParam;

        // Include parameter (from $relationship)
        if (method_exists($model, 'getRelationship')) {
            $relationship = $model->getRelationship();

            if (! empty($relationship)) {
                $includesList = implode(', ', array_map(fn ($i) => "`{$i}`", array_keys($relationship)));
                $includeParam = (new Parameter('include', 'query'))
                    ->description("Available includes: {$includesList}. Separate multiple with a comma.")
                    ->setSchema(Schema::fromType(new StringType));
                $includeParam->setAttribute('isInQuery', true);
                $parameters[] = $includeParam;
            }
        }

        foreach ($parameters as $param) {
            $param->setAttribute('isInQuery', true);
        }

        return $parameters;
    }

    private function makeFilterParam(string $field, string $description): Parameter
    {
        $param = (new Parameter("filter[{$field}]", 'query'))
            ->description($description)
            ->setSchema(Schema::fromType(new StringType));
        $param->setAttribute('isFlat', true);
        $param->setAttribute('isInQuery', true);

        return $param;
    }
}
