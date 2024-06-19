<?php

namespace App\Services;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\File;

class LanguageService
{
    public function collection(): array
    {
        $languages['data'] = collect(config('language'))->values()->all();
        if (empty($languages['data'])) {
            throw new CustomException(__('entity.entityNotFound', ['entity' => 'Languages']), 404);
        }

        return $languages;
    }

    public function resource(?string $input = null): array
    {
        $path = base_path("lang/$input.json");

        if (! File::exists($path)) {
            throw new CustomException(__('entity.entityNotFound', ['entity' => 'Language file']), 404);
        }

        return json_decode(File::get($path), true);
    }
}
