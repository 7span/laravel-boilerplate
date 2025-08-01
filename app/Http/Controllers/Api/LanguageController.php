<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    use ApiResponser;

    private LanguageService $langService;

    public function __construct()
    {
        $this->langService = new LanguageService;
    }

    #[OA\Get(
        path: '/api/languages',
        operationId: 'getLanguages',
        tags: ['Languages'],
        summary: 'Get list of languages',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
        ],
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
        ],
    )]
    public function index(Request $request): JsonResponse
    {
        $data = $this->langService->collection();

        return $this->success($data, 200);
    }

    #[OA\Get(
        path: '/api/languages/{language_id}',
        operationId: 'getLanguageId',
        tags: ['Languages'],
        summary: 'Get detail of languages',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
        ],
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
            new OA\Parameter(
                name: 'language_id',
                in: 'path',
                required: true,
                description: 'ID of the language',
                schema: new OA\Schema(
                    type: 'string'
                )
            ),
        ],
    )]
    public function show(string $language): JsonResponse
    {
        $data = $this->langService->resource($language);

        return $this->success($data, 200);
    }
}
