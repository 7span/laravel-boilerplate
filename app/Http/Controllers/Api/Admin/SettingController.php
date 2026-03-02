<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Setting;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\Request as SettingRequest;
use App\Http\Resources\Setting\Resource as SettingResource;

class SettingController extends Controller
{
    use ApiResponser;

    private SettingService $settingService;

    public function __construct()
    {
        $this->settingService = new SettingService;
    }

    #[OA\Get(
        path: '/api/admin/settings',
        operationId: 'getSettings',
        tags: ['Admin / Settings'],
        summary: 'Get list of settings',
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function index(Request $request)
    {
        $settings = $this->settingService->collection($request->all());

        return SettingResource::collection($settings);
    }

    #[OA\Put(
        path: '/api/admin/settings',
        operationId: 'updateSettings',
        tags: ['Admin / Settings'],
        summary: 'Update Settings',
        description: 'Updates multiple settings using a key-value payload.',
        x: ['model' => Setting::class],
        security: [['bearerAuth' => []]]
    )]
    public function update(SettingRequest $request): JsonResponse
    {
        $data = $this->settingService->update($request->validated());

        return $this->success($data);
    }
}
