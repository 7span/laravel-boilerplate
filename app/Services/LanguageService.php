<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class LanguageService
{
    public function collection()
    {
        $languages = config('language');
        $langListArr['data'] = collect($languages)->values()->all();
        if (empty($langListArr['data'])) {
            $data['errors']['message'] = __('entity.entityNotFound', ['entity' => 'Data']);

            return $data;
        } else {
            return $langListArr;
        }
    }

    public function resource($input = null)
    {
        $filePath = base_path("lang/{$input}.json");

        if (File::exists($filePath)) {
            $filePathContent = File::get($filePath);
            $jsonData = json_decode($filePathContent, true);

            return $jsonData;
        } else {
            $data['errors']['message'] = __('entity.entityNotFound', ['entity' => 'File']);

            return $data;
        }
    }
}
