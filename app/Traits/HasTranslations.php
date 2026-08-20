<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasTranslations
{
    public function getTranslated(string $field): ?string
    {
        $locale = app()->getLocale();

        $column = "{$field}_{$locale}";

        return $this->$column ?? $this->{"{$field}_en"} ?? null;
    }

    /**
     * Handle calls like getNameAttribute() when a translatable field is appended.
     *
     * @param  string  $method
     * @param  array<array-key, mixed>  $parameters
     */
    public function __call($method, $parameters): mixed
    {
        if (! str_starts_with($method, 'get') || ! str_ends_with($method, 'Attribute')) {
            return parent::__call($method, $parameters);
        }

        $field = Str::snake(Str::before(Str::after($method, 'get'), 'Attribute'));

        if (! property_exists($this, 'translatable')) {
            return parent::__call($method, $parameters);
        }

        if (! in_array($field, $this->translatable, true)) {
            return parent::__call($method, $parameters);
        }

        return $this->getTranslated($field);
    }
}
