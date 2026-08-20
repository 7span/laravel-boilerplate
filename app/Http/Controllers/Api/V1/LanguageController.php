<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;

/**
 * @tags Languages
 */
#[Group('Languages', weight: 20)]
class LanguageController extends Controller
{
    use ApiResponser;

    public function __construct(private readonly LanguageService $languageService) {}

    /**
     * List languages.
     *
     * @unauthenticated
     *
     * @response array{
     *     data: array<int, array{
     *         id: string,
     *         name: string,
     *         lable: string,
     *         rtl: bool
     *     }>
     * }
     */
    public function index(): JsonResponse
    {
        $data = $this->languageService->collection();

        return $this->success($data);
    }

    /**
     * Show translations.
     *
     * Returns the translation strings of `lang/{language}.json`.
     *
     * @unauthenticated
     *
     * @response array<string, mixed>
     */
    public function show(string $language): JsonResponse
    {
        $data = $this->languageService->resource($language);

        return $this->success($data);
    }
}
