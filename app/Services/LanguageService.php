<?php

namespace App\Services;

class LanguageService
{
    public function collection()
    {
        $languages = config('language');

        return collect($languages)->values()->all();
    }
}
