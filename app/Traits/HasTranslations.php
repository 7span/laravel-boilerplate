<?php

namespace App\Traits;

trait HasTranslations
{
    
    public function getTranslated($field)
    {
        $lang = app()->getLocale();

        $column = $field . '_' . $lang;

        return $this->$column ?? $this->{$field . '_en'} ?? null;
    }

    /**
     * Handle calls like getNameAttribute() when a translatable field is appended.
     *
     */
    public function __call($method, $parameters)
    {
        $method = (string) $method;

        if (str_starts_with($method, 'get') && str_ends_with($method, 'Attribute')) {
            $studly = \Illuminate\Support\Str::after($method, 'get');
            $studly = \Illuminate\Support\Str::before($studly, 'Attribute');
            $field = \Illuminate\Support\Str::snake($studly);

            if (property_exists($this, 'translatable') && is_array($this->translatable) && in_array($field, $this->translatable, true)) {
                return $this->getTranslated($field);
            }
        }

        return parent::__call($method, $parameters);
    }
}