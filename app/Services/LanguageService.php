<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\File;

class LanguageService
{
    /**
     * @return array<string, mixed>
     */
    public function collection(): array
    {
        /** @var array<int|string, mixed> $configData */
        $configData = config('language') ?? [];
        /** @var array<int|string, mixed> $all */
        $all = collect($configData)->values()->all();
        $languages['data'] = $all;
        if (empty($languages['data'])) {
            throw new CustomException(__('entity.entityNotFound', ['entity' => 'Languages']), 404);
        }

        return $languages;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function resource(?string $input = null): array
    {
        $path = base_path("lang/$input.json");

        if (! File::exists($path)) {
            throw new CustomException(__('entity.entityNotFound', ['entity' => 'Language file']), 404);
        }

        /** @var array<int|string, mixed>|null $decoded */
        $decoded = json_decode(File::get($path), true);

        return $decoded ?? [];
    }
}
