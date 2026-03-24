<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use OpenApi\Attributes as OA;
use Dedoc\Scramble\Attributes\QueryParameter;
use App\Http\Requests\Setting\Request as SettingRequest;
use App\Http\Resources\Setting\Resource as SettingResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Admin / Settings
 */
#[Group('Admin / Settings', weight: 80)]
class SettingController extends Controller
{
    use ApiResponser;

    private SettingService $settingService;

    public function __construct()
    {
        $this->settingService = new SettingService;
    }

    /**
     * List.
     */
    #[OA\Get(
        path: '/api/v1/admin/settings',
        operationId: 'getSettings',
        tags: ['Admin / Settings'],
        summary: 'Get list of settings',
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    #[QueryParameter('appends')]
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var array<string, mixed> $allInputs */
        $allInputs = $request->all();
        $settings = $this->settingService->collection($allInputs);

        return SettingResource::collection($settings);
    }

    /**
     * Update.
     *
     * @response array{message: string}
     */
    public function update(SettingRequest $request): JsonResponse
    {
        $data = $this->settingService->update($request->validated());

        return $this->success($data);
    }
}
