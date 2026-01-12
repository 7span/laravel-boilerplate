<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Setting\Resource;
use App\Http\Resources\Setting\Collection;

class SettingController extends Controller
{
    use ApiResponser;

    private SettingService $settingService;

    public function __construct()
    {
        $this->settingService = new SettingService;
    }

    #[OA\Get(
        path: '/api/settings',
        operationId: 'getSettings',
        tags: ['Settings'],
        summary: 'Get list of settings',
        x: ['model' => Setting::class],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function index(Request $request)
    {
        $settings = $this->settingService->collection($request->all());

        return $this->collection(new Collection($settings));
    }

    #[OA\Get(
        path: '/api/settings/{setting_id}',
        operationId: 'getSettingDetail',
        tags: ['Settings'],
        summary: 'Get detail of settings',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
        ],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function show($id)
    {
        $settingObj = $this->settingService->resource($id);

        return $this->resource(new Resource($settingObj));
    }
}
