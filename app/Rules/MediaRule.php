<?php

namespace App\Rules;

class MediaRule
{
    /**
     * Build the validation rules for a media payload.
     *
     * The client uploads to storage first (see the signed-url flow) and then sends
     * the resulting file metadata, so these rules validate metadata, not a file.
     *
     * @param  string  $field  The media field name, e.g. config('media.tags.profile').
     * @param  bool  $nullable  Whether the field itself may be omitted.
     * @param  array<int, string>  $tags  Aggregate types to accept, e.g. ['image'].
     * @param  bool  $multiple  Whether the field holds a list of media.
     * @return array<string, array<int, string>>
     */
    public static function rules(
        string $field,
        bool $nullable = true,
        array $tags = ['image'],
        bool $multiple = false,
    ): array {
        $item = $multiple ? "{$field}.*" : $field;

        $rules = [
            $field => [$nullable ? 'nullable' : 'required', 'array'],
            "{$item}.filename" => ["required_with:{$field}", 'string', 'max:255'],
            "{$item}.directory" => ["required_with:{$field}", 'string', 'max:255'],
            "{$item}.size" => ["required_with:{$field}", 'integer', 'min:1'],
            "{$item}.mime_type" => ["required_with:{$field}", 'string'],
        ];

        $mimeTypes = collect($tags)
            ->flatMap(fn (string $tag): array => (array) config("media.aggregate_types.{$tag}", []))
            ->unique();

        if ($mimeTypes->isNotEmpty()) {
            $rules["{$item}.mime_type"][] = 'in:' . $mimeTypes->implode(',');
        }

        return $rules;
    }
}
