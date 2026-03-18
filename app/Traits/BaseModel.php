<?php

declare(strict_types = 1);

namespace App\Traits;

use App\Models\Media;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;

/**
 * Shared query-related helpers for Eloquent models.
 *
 * The following dynamic properties are expected to be defined
 * on models that use this trait when needed:
 *
 * @property array<string, array<string, class-string>>|null $relationship Relationships configuration.
 * @property array<int, string> $scopedFilters List of filter names treated as scoped filters.
 * @property array<int, string> $exactFilters List of filter names treated as exact filters.
 * @property string|null $defaultSort Default sort field (e.g. "-created_at").
 * @property array<int, string> $queryable Additional queryable fields.
 */
trait BaseModel
{
    public function getQueryFields(): array
    {
        $_this = new self;
        $fields = [];

        $default = $this->getQueryable();
        foreach ($default as $field) {
            $fields[] = $field;
        }

        foreach ($_this->getFillable() as $field) {
            $fields[] = $field;
        }

        return $fields;
    }

    public function getQueryFieldsWithRelationship(): array
    {
        $fields = $this->getQueryFields();
        $relationships = $this->getRelationship();

        foreach ($relationships as $relationship) {
            $relationshipObj = new ($relationship['model'])();
            $tableName = $relationshipObj->getTable();
            foreach ($relationshipObj->getFillable() as $field) {
                $fields[] = $tableName . '.' . $field;
            }
            if (isset($relationshipObj->queryable)) {
                foreach ($relationshipObj->queryable as $field) {
                    $fields[] = $tableName . '.' . $field;
                }
            }
        }

        return $fields;
    }

    public function getRelationship(): array
    {
        return $this->relationship ?? [];
    }

    public function getIncludes(): array
    {
        return array_keys($this->getRelationship());
    }

    /**
     * Generate allowedIncludes array using snake_case keys as API aliases and camelCase as relationship methods.
     */
    public function getAllowedIncludes(): array
    {
        $includes = [];
        foreach ($this->getRelationship() as $alias => $rel) {
            // Convert snake_case alias to camelCase method name
            $camel = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $alias))));
            $includes[] = AllowedInclude::relationship($alias, $camel);
        }

        return $includes;
    }

    public function getQB(): QueryBuilder
    {
        $this->addMediaToIncludes();

        $queryBuilder = QueryBuilder::for(self::class)->allowedFields($this->getQueryFieldsWithRelationship())->allowedIncludes($this->getAllowedIncludes());

        $filters = $this->getQueryFields();
        if (isset($this->scopedFilters)) {
            foreach ($this->scopedFilters as $key => $value) {
                // remove plain filter if scoped filter exists
                $filters = array_filter($filters, fn ($v) => $v !== $value);
                array_push($filters, AllowedFilter::scope($value));
            }
        }
        if (isset($this->exactFilters)) {
            foreach ($this->exactFilters as $key => $value) {
                array_push($filters, AllowedFilter::exact($value));
            }
        }
        $queryBuilder->allowedFilters($filters);

        if (isset($this->defaultSort)) {
            $queryBuilder->defaultSort($this->defaultSort);
        }

        $queryBuilder->allowedSorts($this->getQueryFields());

        return $queryBuilder;
    }

    /**
     * GET /users?append=display_status,display_name
     * This will append this attributes to the response.
     *
     * If you define a protected property in model : protected $appends = ['display_status'];
     * Then 'display_status' will be appended to the response by default.
     */
    public function getAppends(): array
    {
        $appendParam = request()->get('appends', '');
        $appendArray = is_string($appendParam) ? explode(',', $appendParam) : [];

        $allowedAppends = array_filter($appendArray, function ($value) {
            return ! empty($value) && $this->hasAttribute($value);
        });

        return array_merge($allowedAppends, $this->appends);
    }

    /**
     * Merges model-defined relationships into the 'include' query parameter automatically:
     *
     * 1. Media: added to includes only when 'media' exists in $relationship and request has a 'media' param.
     * 2. Nested relations: any dotted key (e.g. 'comments.media') in $relationship is always eager-loaded.
     */
    protected function addMediaToIncludes(): void
    {
        $request = request();
        $includes = array_filter(explode(',', $request->query('include', '')));
        $relationships = $this->getRelationship();

        if ($request->filled('media') && isset($relationships['media'])) {
            $includes[] = 'media';
        }

        foreach (array_keys($relationships) as $relation) {
            $parent = substr($relation, 0, -6); // strips '.media'

            if (str_ends_with($relation, '.media') && in_array($parent, $includes)) {
                $includes[] = $relation;
            }
        }

        $request->merge(['include' => implode(',', array_unique($includes))]);
    }

    private function getQueryable()
    {
        return ! empty($this->queryable) ? $this->queryable : ['id'];
    }
}
