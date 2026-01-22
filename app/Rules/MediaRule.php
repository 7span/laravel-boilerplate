<?php

namespace App\Rules;

class MediaRule
{
    /**
     * Get media validation rules.
     *
     * @param  string  $fieldName  The name of the media field.
     * @param  bool  $isNullable  If the field is nullable.
     * @param  array  $tags  The tags of media to validate against (e.g., ['image']).
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

        $mimeTypes = [];
        foreach ($tags as $tag) {
            if (config('media.aggregate_types.' . $tag)) {
                $mimeTypes = array_merge($mimeTypes, config('media.aggregate_types.' . $tag));
            }
        }

        if (! empty($mimeTypes)) {
            $baseRules["{$itemPrefix}.mime_type"] .= '|in:' . implode(',', $mimeTypes);
        }

        return $baseRules;
    }
}
