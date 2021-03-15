<?php

namespace App\Traits;

use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

trait BaseModel
{

    private function getQueryable()
    {
        return $this->queryable;
    }

    public function getQueryFields()
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

    public function getQueryFieldsWithRelationship()
    {
        $fields = $this->getQueryFields();
        $relationships  = $this->getRelationship();

        foreach ($relationships as $relationshipName =>  $relationship) {
            $relationshipObj  = new $relationship['model']();
            foreach ($relationshipObj->getFillable() as $field) {
                $fields[] =   $relationshipName . '.' . $field;
            }
            foreach ($relationshipObj->queryable as $field) {
                $fields[] = $relationshipName . '.' . $field;
            }
        }
        return $fields;
    }

    public function getRelationship()
    {
        return $this->relationship;
    }

    public function getIncludes()
    {
        return array_keys($this->relationship);
    }

    public function getQB()
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
}
