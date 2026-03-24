<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Resources\MissingValue;
use Plank\Mediable\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * @property mixed $resource
 * @property class-string<Model> $model
 */
trait ResourceFilterable
{
    /**
     * @return array<string, mixed>
     */
    protected function fields(): array
    {
        /** @var Model $res */
        /** @phpstan-ignore-next-line */
        $res = $this->resource;

        $prepared = $this->prepareResponse($res);
        $keys = array_merge(array_keys($res->getAttributes()), $res->getAppends());

        $filtered = [];
        foreach ($keys as $key) {
            /** @var string $key */
            if (array_key_exists($key, $prepared)) {
                $filtered[$key] = $prepared[$key];
            }
        }

        return $filtered;
    }

    /**
     * @param Model $res
     * @return array<string, mixed>
     */
    protected function prepareResponse(Model $res): array
    {
        $data = [];
        /** @var class-string<Model> $class */
        $class = $this->model;
        /** @var Model $classObj */
        $classObj = new $class;
        /** @var array<int, string> $fields */
        /** @phpstan-ignore-next-line */
        $fields = array_merge($classObj->getQueryFields(), $classObj->getAppends());
        /** @var array<int, string> $hiddenFields */
        $hiddenFields = $classObj->getHidden();
        /** @var array<string, string> $casts */
        $casts = $classObj->getCasts();

        foreach ($fields as $field) {
            if (! in_array($field, $hiddenFields, true)) {
                if (isset($casts[$field])) {
                    $val = $res->getAttribute($field);
                    switch ($casts[$field]) {
                        case 'datetime':
                            $data[$field] = ($val instanceof Carbon) ? $val->format('d-m-Y H:i:s') : $val;
                            break;
                        case 'date':
                            $data[$field] = ($val instanceof Carbon) ? $val->format('d-m-Y') : $val;
                            break;
                        default:
                            $data[$field] = $res->getAttribute($field);
                    }
                } else {
                    $data[$field] = $res->getAttribute($field);
                }
            }
        }

        return $data;
    }

    /**
     * @return Media|Collection<int, Media>|MissingValue
     */
    protected function whenLoadedMedia(string $key, bool $isResource = false): Media|Collection|MissingValue
    {
<<<<<<< HEAD
        /** @var string|null $mediaQuery */
        $mediaQuery = request()->input('media');
        if (! empty($mediaQuery)) {
            /** @var array<int, string> $mediaInput */
            $mediaInput = explode(',', $mediaQuery);
            if (in_array($key, $mediaInput, true)) {
                /** @var Model $res */
                /** @phpstan-ignore-next-line */
                $res = $this->resource;
=======
        if (! method_exists($this->resource, 'getMedia')) { // @phpstan-ignore function.alreadyNarrowedType
            return new MissingValue;
        }

        $mediaInput = request()->input('media');
        if (! empty($mediaInput)) {
            $mediaInput = explode(',', $mediaInput);
            if (in_array($key, $mediaInput)) {
>>>>>>> origin/master
                if ($isResource) {
                    /** @phpstan-ignore-next-line */
                    return $res->getMedia($key)->first() ?? new MissingValue;
                }

                /** @phpstan-ignore-next-line */
                return $res->getMedia($key);
            }
        }

        return new MissingValue;
    }
}
