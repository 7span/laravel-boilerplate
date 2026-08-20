<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\MissingAttributeException;

trait ResourceFilterable
{
    /**
     * Build the response payload limited to the attributes actually loaded on the model.
     *
     * @return array<string, mixed>
     */
    protected function fields(): array
    {
        return collect($this->prepareResponse())
            ->only(array_merge(array_keys($this->resource->getAttributes()), $this->resource->getAppends()))
            ->toArray();
    }

    /** @return array<string, mixed> */
    protected function prepareResponse(): array
    {
        $data = [];
        $modelClass = $this->model;
        $model = new $modelClass;

        $fields = array_merge($model->getQueryFields(), $model->getAppends());
        $hiddenFields = $model->getHidden();
        $casts = $model->getCasts();

        foreach ($fields as $field) {
            if (in_array($field, $hiddenFields, true)) {
                continue;
            }

            try {
                $value = $this->$field;
            } catch (MissingAttributeException) {
                // `fields[...]` did not select this column.
                continue;
            }

            $data[$field] = match ($casts[$field] ?? null) {
                'datetime' => $value?->format('d-m-Y H:i:s'),
                'date' => $value?->format('d-m-Y'),
                default => $value,
            };
        }

        return $data;
    }

    /**
     * @return Model|Collection<int, Model>|MissingValue|null
     */
    protected function whenLoadedMedia(string $tag, bool $isSingle = false): Model|Collection|MissingValue|null
    {
        if (! method_exists($this->resource, 'getMedia')) { // @phpstan-ignore function.alreadyNarrowedType
            return new MissingValue;
        }

        $mediaInput = request()->input('media');

        if (empty($mediaInput) || ! in_array($tag, explode(',', (string) $mediaInput), true)) {
            return new MissingValue;
        }

        $media = $this->resource->getMedia($tag);

        return $isSingle ? $media->first() : $media;
    }
}
