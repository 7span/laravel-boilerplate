<?php

namespace App\Traits;

use App\Models\Media;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;

/**
 * Shared query-related helpers for Eloquent models.
 *
 * Models may optionally declare `$relationship`, `$scopedFilters`, `$exactFilters`,
 * `$defaultSort` and `$queryable`. Never declare them on this trait: a model
 * redeclaring a trait property with a different value is a fatal error.
 *
 * @property array<int, string> $queryable Additional queryable fields.
 */
trait BaseModel
{
    use HasTranslations;

    /** @return array<int, string> */
    public function getQueryFields(): array
    {
        return array_merge($this->getQueryable(), $this->getFillable());
    }

    /** @return array<int, string> */
    public function getQueryFieldsWithRelationship(): array
    {
        $fields = $this->getQueryFields();

        foreach ($this->getRelationship() as $relationship) {
            $relatedModel = new $relationship['model'];
            $tableName = $relatedModel->getTable();

            foreach ($relatedModel->getFillable() as $field) {
                $fields[] = "{$tableName}.{$field}";
            }

            if (isset($relatedModel->queryable)) {
                foreach ($relatedModel->queryable as $field) {
                    $fields[] = "{$tableName}.{$field}";
                }
            }
        }

        return $fields;
    }

    /** @return array<string, array{model: class-string}> */
    public function getRelationship(): array
    {
        $relationship = property_exists($this, 'relationship') ? $this->relationship : [];

        if (! array_key_exists('media', $relationship)) {
            $relationship['media'] = [
                'model' => Media::class,
            ];
        }

        return $relationship;
    }

    /** @return array<int, string> */
    public function getIncludes(): array
    {
        return array_keys($this->getRelationship());
    }

    /**
     * Build allowedIncludes using the snake_case key as the API alias and camelCase as the relationship method.
     *
     * @return array<int, AllowedInclude>
     */
    public function getAllowedIncludes(): array
    {
        $includes = [];

        foreach (array_keys($this->getRelationship()) as $alias) {
            $relationName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $alias))));
            $includes[] = AllowedInclude::relationship($alias, $relationName);
        }

        return $includes;
    }

    /** @return QueryBuilder<static> */
    public function getQB(): QueryBuilder
    {
        $this->addMediaToIncludes();

        $queryBuilder = QueryBuilder::for(static::class)
            ->allowedFields(...$this->getQueryFieldsWithRelationship())
            ->allowedIncludes(...$this->getAllowedIncludes());

        $filters = $this->getQueryFields();

        if (property_exists($this, 'scopedFilters')) {
            foreach ($this->scopedFilters as $scopedFilter) {
                $filters = array_filter($filters, fn (string $filter): bool => $filter !== $scopedFilter);
                $filters[] = AllowedFilter::scope($scopedFilter);
            }
        }

        if (property_exists($this, 'exactFilters')) {
            foreach ($this->exactFilters as $exactFilter) {
                $filters[] = AllowedFilter::exact($exactFilter);
            }
        }

        $queryBuilder->allowedFilters(...$filters);

        if (property_exists($this, 'defaultSort')) {
            $queryBuilder->defaultSort($this->defaultSort);
        }

        $queryBuilder->allowedSorts(...$this->getQueryFields());

        return $queryBuilder;
    }

    /**
     * GET /users?appends=display_status,display_mobile_no
     * Appends the requested attributes on top of the model's own `$appends`.
     *
     * @return array<int, string>
     */
    public function getAppends(): array
    {
        $appendParam = request()->get('appends', '');
        $requestedAppends = is_string($appendParam) ? explode(',', $appendParam) : [];

        $allowedAppends = array_filter(
            $requestedAppends,
            fn (string $append): bool => $append !== '' && $this->hasAttribute($append),
        );

        return array_merge($allowedAppends, $this->appends);
    }

    /**
     * Example: GET /api/v1/users?media=profile
     *
     * Adds the 'media' relationship to the 'include' query parameter when the
     * 'media' parameter is present, which prevents an N+1 query.
     */
    protected function addMediaToIncludes(): void
    {
        $request = request();
        $includes = explode(',', (string) $request->query('include', ''));

        if (! $request->filled('media')) {
            return;
        }

        if (in_array('media', $includes, true)) {
            return;
        }

        $includes[] = 'media';
        $request->merge(['include' => implode(',', $includes)]);
    }

    /** @return array<int, string> */
    private function getQueryable(): array
    {
        return ! empty($this->queryable) ? $this->queryable : ['id'];
    }
}
