<?php

namespace App\Rules;

class MediaRule
{
    /**
     * Get media validation rules.
     *
     * @param  string  $fieldName  The name of the media field.
     * @param  bool  $isNullable  If the field is nullable.
     * @param  string  $type  The type of media to validate against (e.g., 'image').
     */
    public static function rules(string $fieldName, bool $isNullable = true, array $tags = ['image']): array
    {
        $baseRules = [
            $fieldName => ($isNullable ? 'nullable' : 'required') . '|array',
            "{$fieldName}.filename" => "required_with:{$fieldName}|string|max:255",
            "{$fieldName}.directory" => "required_with:{$fieldName}|string|max:255",
            "{$fieldName}.size" => "required_with:{$fieldName}|integer",
            "{$fieldName}.mime_type" => "required_with:{$fieldName}",
        ];

        $mimeTypes = [];
        foreach ($tags as $tag) {
            if (config('media.aggregate_types.' . $tag)) {
                $mimeTypes = array_merge($mimeTypes, config('media.aggregate_types.' . $tag));
            }
        }

        if (! empty($mimeTypes)) {
            $baseRules["{$fieldName}.mime_type"] .= '|in:' . implode(',', $mimeTypes);
        }

        return $baseRules;
    }
}
