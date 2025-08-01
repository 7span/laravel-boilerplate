<?php

namespace App\Traits;

use App\Models\Media;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;

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
            $relationshipObj = new $relationship['model'];
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
        $relationship = $this->relationship ?? [];

        // Always add 'media' relationship if not present
        if (! array_key_exists('media', $relationship)) {
            $relationship['media'] = [
                'model' => Media::class,
            ];
        }

        return $relationship;
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

        $queryBuilder = QueryBuilder::for(self::class)
            ->allowedFields($this->getQueryFieldsWithRelationship())
            ->allowedIncludes($this->getAllowedIncludes());

        $filters = $this->getQueryFields();
        if (isset($this->scopedFilters)) {
            foreach ($this->scopedFilters as $key => $value) {
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
     * Example: GET /api/users?media=profile_image
     *
     * Dynamically adds the 'media' relationship to the 'include' query parameter
     * if the 'media' parameter is present in the request that prevent from n+1 query.
     */
    protected function addMediaToIncludes(): void
    {
        $request = request();
        $includes = explode(',', $request->query('include', ''));

        if ($request->filled('media') && ! in_array('media', $includes)) {
            $includes[] = 'media';
            $request->merge(['include' => implode(',', $includes)]);
        }
    }

    private function getQueryable()
    {
        return ! empty($this->queryable) ? $this->queryable : ['id'];
    }
}
