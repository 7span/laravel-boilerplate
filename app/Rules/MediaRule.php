<?php

declare(strict_types=1);

namespace App\Rules;

class MediaRule
{
    /**
     * Get media validation rules.
     *
     * @param  string  $fieldName  The name of the media field.
     * @param  bool  $isNullable  If the field is nullable.
     * @param  array<int, string>  $tags  The tags of media to validate against (e.g., ['image']).
     * @return array<string, string>
     */
    public static function rules(string $fieldName, bool $isNullable = true, array $tags = ['image'], bool $multiple = false): array
    {
        $itemPrefix = $multiple ? "{$fieldName}.*" : $fieldName;

        $baseRules = [
            $fieldName => ($isNullable ? 'nullable' : 'required') . '|array',
            "{$itemPrefix}.filename" => "required_with:{$fieldName}|string|max:255",
            "{$itemPrefix}.directory" => "required_with:{$fieldName}|string|max:255",
            "{$itemPrefix}.size" => "required_with:{$fieldName}|integer",
            "{$itemPrefix}.mime_type" => "required_with:{$fieldName}",
        ];

        /** @var array<int, string> $mimeTypes */
        $mimeTypes = [];
        foreach ($tags as $tag) {
            /** @var array<int, string>|null $typeMimes */
            $typeMimes = config('media.aggregate_types.' . $tag);
            if ($typeMimes) {
                $mimeTypes = array_merge($mimeTypes, $typeMimes);
            }
        }

        if (! empty($mimeTypes)) {
            $baseRules["{$itemPrefix}.mime_type"] .= '|in:' . implode(',', $mimeTypes);
        }

        return $baseRules;
    }
}
