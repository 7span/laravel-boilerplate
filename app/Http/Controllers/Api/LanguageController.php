<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;

/**
 * @tags Languages
 */
#[Group('Languages', weight: 5)]
class LanguageController extends Controller
{
    use ApiResponser;

    private LanguageService $langService;

    public function __construct()
    {
        $this->langService = new LanguageService;
    }

    /**
     * List languages.
     *
     * Returns all available application locales.
     *
     * @response array{data: array<int, array{id: string, name: string, lable: string, rtl: bool}>}
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->langService->collection();

        return $this->success($data, 200);
    }

    /**
     * Show language.
     *
     * Returns all translation key-value pairs for the given locale.
     *
     * @response array<string, string>
     */
    public function show(string $language): JsonResponse
    {
        $data = $this->langService->resource($language);

        return $this->success($data, 200);
    }
}
