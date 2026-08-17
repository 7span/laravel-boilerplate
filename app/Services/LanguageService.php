<?php

namespace App\Services;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\File;

class LanguageService
{
    /**
     * The languages the application ships, as declared in `config/language.php`.
     *
     * @return array{data: array<int, array<string, mixed>>}
     */
    public function collection(): array
    {
        $languages = array_values(config('language', []));

        if ($languages === []) {
            throw new CustomException(__('entity.entityNotFound', ['entity' => 'Languages']), 404);
        }

        return ['data' => $languages];
    }

    /**
     * The translation strings of a language, read from `lang/{locale}.json`.
     *
     * @return array<string, mixed>
     */
    public function resource(string $language): array
    {
        $path = lang_path("{$language}.json");

        if (! File::exists($path)) {
            throw new CustomException(__('entity.entityNotFound', ['entity' => 'Language file']), 404);
        }

        return json_decode(File::get($path), true) ?? [];
    }
}
