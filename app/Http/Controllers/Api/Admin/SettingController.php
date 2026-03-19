<?php

namespace App\Http\Controllers\Api\Admin;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\Setting\Request as SettingRequest;
use App\Http\Resources\Setting\Resource as SettingResource;

/**
 * @tags Admin / Settings
 */
#[Group('Admin / Settings', weight: 2)]
class SettingController extends Controller
{
    use ApiResponser;

    private SettingService $settingService;

    public function __construct()
    {
        $this->settingService = new SettingService;
    }

    /**
     * List settings.
     *
     * Returns a paginated list of application settings. Requires admin privileges.
     */
    public function index(Request $request)
    {
        $settings = $this->settingService->collection($request->all());

        return SettingResource::collection($settings);
    }

    /**
     * Update settings.
     *
     * Bulk-updates application settings using a key-value payload. Requires admin privileges.
     *
     * @response array{message: string}
     */
    public function update(SettingRequest $request): JsonResponse
    {
        $data = $this->settingService->update($request->validated());

        return $this->success($data);
    }
}
