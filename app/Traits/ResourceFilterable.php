<?php

namespace App\Traits;

trait ResourceFilterable
{
    /**
     * Filter null inputs.
     */
    protected function fields(): array
    {
        return collect($this->prepareResponse())
            ->only(array_merge(array_keys($this->resource->getAttributes()), $this->resource->getAppends()))
            ->toArray();
    }

    protected function prepareResponse(): array
    {
        $data = [];
        $class = $this->model;
        $classObj = new $class();
        $fields = array_merge($classObj->getQueryFields(), $classObj->getAppends());
        $hiddenFields = $classObj->getHidden();
        $casts = $classObj->getCasts();
        foreach ($fields as $field) {
            if (! in_array($field, $hiddenFields)) {
                if (isset($casts[$field])) {
                    switch ($casts[$field]) {
                        case 'datetime':
                            $data[$field] = optional($this->$field)->format('d-m-Y H:i:s');
                            break;
                        case 'date':
                            $data[$field] = optional($this->$field)->format('d-m-Y');
                            break;
                        default: // Used for id
                            $data[$field] = $this->$field;
                    }
                } else {
                    $data[$field] = $this->$field;
                }
            }
        }
        return $data;
    }
}