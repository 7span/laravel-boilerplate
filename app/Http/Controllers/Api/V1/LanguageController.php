<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    use ApiResponser;

    public function __construct(private LanguageService $langService)
    {
        //
    }

    #[OA\Get(
        path: '/api/v1/languages',
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
        path: '/api/v1/languages/{language_id}',
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

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
