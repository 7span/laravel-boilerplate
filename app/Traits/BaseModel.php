<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;

/**
 * Shared query-related helpers for Eloquent models.
 *
 * The following dynamic properties are expected to be defined
 * on models that use this trait when needed:
 *
 * @property array<string, array<string, class-string>> $relationship Relationships configuration.
 * @property array<int, string> $scopedFilters List of filter names treated as scoped filters.
 * @property array<int, string> $exactFilters List of filter names treated as exact filters.
 * @property string|null $defaultSort Default sort field (e.g. "-created_at").
 * @property array<int, string> $queryable Additional queryable fields.
 * @property array<int, string> $fillable
 * @property array<int, string> $appends
 */
trait BaseModel
{
<<<<<<< HEAD
    /** @return array<int, string> */
=======
    use HasTranslations;

>>>>>>> origin/master
    public function getQueryFields(): array
    {
        /** @var $this $model */
        $model = $this;
        $fields = [];

        $default = $this->getQueryable();
        foreach ($default as $field) {
            $fields[] = $field;
        }

        foreach ($model->getFillable() as $field) {
            $fields[] = $field;
        }

        return $fields;
    }

    /** @return array<int, string> */
    public function getQueryFieldsWithRelationship(): array
    {
        $fields = $this->getQueryFields();
        $relationships = $this->getRelationship();

        foreach ($relationships as $relationship) {
            /** @var \Illuminate\Database\Eloquent\Model&object{queryable?:array<int,string>} $relationshipObj */
            $relationshipObj = new $relationship['model'];
            $tableName = $relationshipObj->getTable();
            foreach ($relationshipObj->getFillable() as $field) {
                $fields[] = $tableName . '.' . $field;
            }
            /** @phpstan-ignore-next-line */
            if (isset($relationshipObj->queryable)) {
                /** @phpstan-ignore-next-line */
                foreach ($relationshipObj->queryable as $field) {
                    $fields[] = $tableName . '.' . $field;
                }
            }
        }

        return $fields;
    }

    /** @return array<string, array<string, class-string>> */
    public function getRelationship(): array
    {
        /** @var array<string, array<string, class-string>> $relationship */
        /** @phpstan-ignore-next-line */
        $relationship = $this->relationship ?? [];

        // Always add 'media' relationship if not present
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
     * Generate allowedIncludes array using snake_case keys as API aliases and camelCase as relationship methods.
     *
     * @return array<int, AllowedInclude>
     */
    public function getAllowedIncludes(): array
    {
        /** @var array<int, AllowedInclude> $includes */
        $includes = [];
        foreach ($this->getRelationship() as $alias => $rel) {
            // Convert snake_case alias to camelCase method name
            $camel = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $alias))));
            $includes[] = AllowedInclude::relationship($alias, $camel);
        }

        /** @var array<int, \Spatie\QueryBuilder\AllowedInclude> $finalIncludes */
        $finalIncludes = $includes;
        return $finalIncludes;
    }

    /** @return QueryBuilder<$this> */
    public function getQB(): QueryBuilder
    {
        $this->addMediaToIncludes();

        /** @phpstan-ignore-next-line */
        $queryBuilder = QueryBuilder::for($this::class)
            ->allowedFields($this->getQueryFieldsWithRelationship())
            ->allowedIncludes($this->getAllowedIncludes());

        /** @var array<int, string> $filters */
        $filters = $this->getQueryFields();
<<<<<<< HEAD
        /** @var array<int, string> $scopedFilters */
        /** @phpstan-ignore-next-line */
        $scopedFilters = $this->scopedFilters ?? [];
        foreach ($scopedFilters as $value) {
            // remove plain filter if scoped filter exists
            $filters = array_filter($filters, fn ($v) => $v !== $value);
            array_push($filters, AllowedFilter::scope($value));
        }
        /** @var array<int, string> $exactFilters */
        /** @phpstan-ignore-next-line */
        $exactFilters = $this->exactFilters ?? [];
        foreach ($exactFilters as $value) {
            array_push($filters, AllowedFilter::exact($value));
        }
        $queryBuilder->allowedFilters($filters);

        /** @phpstan-ignore-next-line */
        $defaultSort = $this->defaultSort ?? null;
        /** @phpstan-ignore-next-line */
        if (is_string($defaultSort)) {
            $queryBuilder->defaultSort($defaultSort);
=======
        if (isset($this->scopedFilters)) { // @phpstan-ignore isset.property, function.alreadyNarrowedType
            foreach ($this->scopedFilters as $key => $value) {
                // remove plain filter if scoped filter exists
                $filters = array_filter($filters, fn ($v) => $v !== $value);
                array_push($filters, AllowedFilter::scope($value));
            }
        }
        if (isset($this->exactFilters)) { // @phpstan-ignore isset.property, function.alreadyNarrowedType
            foreach ($this->exactFilters as $key => $value) {
                array_push($filters, AllowedFilter::exact($value));
            }
        }
        $queryBuilder->allowedFilters($filters);

        if (isset($this->defaultSort)) { // @phpstan-ignore isset.property, function.alreadyNarrowedType
            $queryBuilder->defaultSort($this->defaultSort);
>>>>>>> origin/master
        }

        $queryBuilder->allowedSorts($this->getQueryFields());

        /** @var QueryBuilder<$this> $queryBuilder */
        return $queryBuilder;
    }

    /** @return array<int, string> */
    public function getAppends(): array
    {
        $appendParam = request()->get('appends', '');
        $appendArray = is_string($appendParam) ? explode(',', $appendParam) : [];

        $allowedAppends = array_filter($appendArray, function ($value) {
            return ! empty($value) && $this->hasAttribute($value);
        });

        /** @var array<int, string> $appends */
        /** @phpstan-ignore-next-line */
        $appends = $this->appends ?? [];
        return array_merge($allowedAppends, $appends);
    }

    protected function addMediaToIncludes(): void
    {
        $request = request();
        $includes = explode(',', (string) $request->query('include', ''));

        if ($request->filled('media') && ! in_array('media', $includes, true)) {
            $includes[] = 'media';
            $request->merge(['include' => implode(',', $includes)]);
        }
    }

    /** @return array<int, string> */
    private function getQueryable(): array
    {
        /** @var array<int, string> $queryable */
        $queryable = $this->queryable ?? [];
        return ! empty($queryable) ? $queryable : ['id'];
    }
}
