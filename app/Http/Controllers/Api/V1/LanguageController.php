<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
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

    private LanguageService $langService;

    public function __construct()
    {
        $this->langService = new LanguageService;
    }

    /**
     * List.
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
    public function index(Request $request): JsonResponse
    {
        $data = $this->langService->collection();

        return $this->success($data, 200);
    }

    /**
     * Show.
     *
     * @unauthenticated
     *
     * @response array<string, mixed>
     */
    public function show(string $language): JsonResponse
    {
        $data = $this->langService->resource($language);

        return $this->success($data, 200);
    }
}
