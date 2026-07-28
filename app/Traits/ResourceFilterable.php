<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\MissingAttributeException;
use Illuminate\Http\Resources\MissingValue;

trait ResourceFilterable
{
    protected static array $fieldMetaCache = [];

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
        $class = get_class($this->resource);

        
        // Cache model fields for current request
        $meta = static::$fieldMetaCache[$class] ??= [
            'columns' => $this->resource->getQueryFields(),
            'hidden' => array_flip($this->resource->getHidden()),
        ];

        $appends = $this->resource->getAppends();

        $data = [];
        // Columns actually returned by the DB query (not the full model schema)
        $loadedAttributes = $this->resource->getAttributes();

        // only touch columns that were actually selected
        foreach ($meta['columns'] as $field) {
            if (! isset($meta['hidden'][$field]) && array_key_exists($field, $loadedAttributes)) {
                $data[$field] = $this->$field;
            }
        }

        foreach ($appends as $field) {
            if (! isset($meta['hidden'][$field])) {
                try {
                    $data[$field] = $this->$field;
                } catch (MissingAttributeException) {
                    // Skip appends whose accessor depends on a column not selected via ?fields=
                }
            }
        }

        return $data;
    }

    protected function whenLoadedMedia(string $key, bool $isResource = false)
    {
        if (! method_exists($this->resource, 'getMedia')) { // @phpstan-ignore function.alreadyNarrowedType
            return new MissingValue;
        }

        $mediaInput = request()->input('media');
        if (! empty($mediaInput)) {
            $mediaInput = explode(',', $mediaInput);
            if (in_array($key, $mediaInput)) {
                if ($isResource) {
                    return $this->resource->getMedia($key)->first();
                }

                return $this->resource->getMedia($key);
            } else {
                return new MissingValue;
            }
        } else {
            return new MissingValue;
        }
    }
}
