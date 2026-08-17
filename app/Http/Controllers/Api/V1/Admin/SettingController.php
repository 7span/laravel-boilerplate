<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Traits\ApiResponser;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\Setting\UpdateSettingRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Setting\Resource as SettingResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Settings
 */
#[Group('Settings', weight: 80)]
class SettingController extends Controller
{
    use ApiResponser;

    public function __construct(private readonly SettingService $settingService) {}

    /**
     * List settings.
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<SettingResource>>
     */
    public function index(): ResourceCollection
    {
        $data = $this->settingService->collection();

        return $this->collection(SettingResource::collection($data));
    }

    /**
     * Update settings.
     *
     * Accepts every key listed in `config/site.php` under `setting_keys`.
     *
     * @response array{message: string}
     */
    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $data = $this->settingService->update($request->validated());

        return $this->success($data);
    }
}
