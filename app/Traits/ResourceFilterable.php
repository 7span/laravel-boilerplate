<?php

namespace App\Traits;

trait ResourceFilterable
{
  /**
   * Filter null inputs.
   *
   * @return array
   */
  protected function fields(): array
  {

    return collect($this->prepareResponse())
          ->only(array_keys($this->resource->getAttributes()))
          ->toArray();
  }

  protected function prepareResponse()
  {
    $data = [];
    $class  = 'App\\Models\\' . $this->model;
    $classObj = new $class();
    $fields = $classObj->getQueryFields();
    $hiddenFields = $classObj->getHidden();
    $casts = $classObj->getCasts();
    foreach ($fields as $field) {
      if (!in_array($field, $hiddenFields)) {
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
