<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class LanguageService
{
    public function collection()
    {
        $languages['data'] = collect(config('language'))->values()->all();

        if (empty($languages['data'])) {
            $data['errors']['message'] = __('entity.entityNotFound', ['entity' => 'Languages']);

            return $data;
        }

        return $languages;
    }

    public function resource($input = null)
    {
        $path = base_path("lang/$input.json");

        if (! File::exists($path)) {
            $data['errors']['message'] = __('entity.entityNotFound', ['entity' => 'Language file']);

            return $data;
        }

        return json_decode(File::get($path), true);
    }
}
