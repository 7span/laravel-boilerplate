<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Support\Str;
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

    /**
     * Fields selectable through `fields[...]`, for this model and its includes.
     * Related fields use the relation method as prefix, e.g. `userDevices.id`,
     * because that is the key Spatie matches them against.
     *
     * @return array<int, string>
     */
    public function getQueryFieldsWithRelationship(): array
    {
        $fields = $this->getQueryFields();

        foreach ($this->getRelationship() as $alias => $relationship) {
            $relatedModel = new $relationship['model'];
            $relationName = Str::camel($alias);

            $relatedFields = method_exists($relatedModel, 'getQueryFields')
                ? $relatedModel->getQueryFields()
                : $relatedModel->getFillable();

            // The key is always selectable, a relation without it never loads.
            $relatedFields = array_merge([$relatedModel->getKeyName()], $relatedFields);

            foreach ($relatedFields as $field) {
                $fields[] = "{$relationName}.{$field}";
            }
        }

        return array_values(array_unique($fields));
    }

    /** @return array<string, array{model: class-string}> */
    public function getRelationship(): array
    {
        $relationship = property_exists($this, 'relationship') ? $this->relationship : [];

        if (! array_key_exists('media', $relationship) && method_exists($this, 'getMedia')) {
            $relationship['media'] = ['model' => Media::class];
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

        foreach ($this->getIncludes() as $alias) {
            $includes[] = AllowedInclude::relationship($alias, Str::camel($alias));
        }

        return $includes;
    }

    /**
     * Every query field filters partially, except the ones the model declares as
     * a scope or an exact filter.
     *
     * @return array<int, string|AllowedFilter>
     */
    public function getAllowedFilters(): array
    {
        $scopedFilters = property_exists($this, 'scopedFilters') ? $this->scopedFilters : [];
        $exactFilters = property_exists($this, 'exactFilters') ? $this->exactFilters : [];

        $filters = array_values(array_diff($this->getQueryFields(), $scopedFilters, $exactFilters));

        foreach ($scopedFilters as $scopedFilter) {
            $filters[] = AllowedFilter::scope($scopedFilter);
        }

        foreach ($exactFilters as $exactFilter) {
            $filters[] = AllowedFilter::exact($exactFilter);
        }

        return $filters;
    }

    /** @return QueryBuilder<static> */
    public function getQB(): QueryBuilder
    {
        $this->addMediaToIncludes();
        $this->renameRelationFieldKeys();

        $queryBuilder = QueryBuilder::for(static::class)
            ->allowedFields(...$this->getQueryFieldsWithRelationship())
            ->allowedIncludes(...$this->getAllowedIncludes())
            ->allowedFilters(...$this->getAllowedFilters());

        if (property_exists($this, 'defaultSort')) {
            $queryBuilder->defaultSort($this->defaultSort);
        }

        return $queryBuilder->allowedSorts(...$this->getQueryFields());
    }

    /**
     * GET /users?appends=display_status,display_mobile_no
     * Appends the requested attributes on top of the model's own `$appends`.
     *
     * @return array<int, string>
     */
    public function getAppends(): array
    {
        $requestedAppends = explode(',', (string) request()->query('appends', ''));

        $allowedAppends = array_filter(
            $requestedAppends,
            fn (string $append): bool => $append !== '' && $this->hasAttribute($append),
        );

        return array_merge($allowedAppends, $this->appends);
    }

    /**
     * GET /users?media=profile
     * Adds `media` to the `include` parameter, which prevents an N+1 query.
     */
    protected function addMediaToIncludes(): void
    {
        $request = request();

        if (! $request->filled('media')) {
            return;
        }

        $includes = explode(',', (string) $request->query('include', ''));

        if (in_array('media', $includes, true)) {
            return;
        }

        $includes[] = 'media';
        $request->merge(['include' => implode(',', $includes)]);
    }

    /**
     * The API takes `fields[user_devices]`, Spatie reads it as `fields[userDevices]`.
     * Only include keys are renamed, this model's own table key stays as it is.
     */
    protected function renameRelationFieldKeys(): void
    {
        $request = request();
        $parameter = (string) config('query-builder.parameters.fields', 'fields');
        $fields = $request->input($parameter);

        if (! is_array($fields)) {
            return;
        }

        $includes = $this->getIncludes();
        $renamedFields = [];

        foreach ($fields as $key => $value) {
            $isInclude = in_array((string) $key, $includes, true);
            $renamedFields[$isInclude ? Str::camel((string) $key) : $key] = $value;
        }

        $request->merge([$parameter => $renamedFields]);
    }

    /** @return array<int, string> */
    private function getQueryable(): array
    {
        return ! empty($this->queryable) ? $this->queryable : ['id'];
    }
}
