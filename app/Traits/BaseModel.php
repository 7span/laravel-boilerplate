<?php

namespace App\Traits;

use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

trait BaseModel
{
    public function getQueryFields(): array
    {
        $_this = new self();
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
            $relationshipObj = new $relationship['model']();
            $tableName = $relationshipObj->getTable();
            foreach ($relationshipObj->getFillable() as $field) {
                $fields[] = $tableName . '.' . $field;
            }
            foreach ($relationshipObj->queryable as $field) {
                $fields[] = $tableName . '.' . $field;
            }
        }

        return $fields;
    }

    public function getRelationship(): array
    {
        $relationship = $this->relationship;

        return $relationship ? $relationship : [];
    }

    public function getIncludes(): array
    {
        return array_keys($this->getRelationship());
    }

    public function getQB(): QueryBuilder
    {
        $queryBuilder = QueryBuilder::for(self::class)
            ->allowedFields($this->getQueryFieldsWithRelationship())
            ->allowedIncludes($this->getIncludes());
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

        return $queryBuilder;
    }

    private function getQueryable()
    {
        return ! empty($this->queryable) ? $this->queryable : ['id'];
    }
}
