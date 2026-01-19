<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Models\MasterSetting;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use App\Services\MasterSettingService;
use App\Http\Resources\MasterSetting\Resource;
use App\Http\Resources\MasterSetting\Collection;

class MasterSettingController extends Controller
{
    use ApiResponser;

    private MasterSettingService $masterSettingService;

    public function __construct()
    {
        $this->masterSettingService = new MasterSettingService;
    }

    #[OA\Get(
        path: '/api/settings',
        operationId: 'getMasterSettings',
        tags: ['MasterSettings'],
        summary: 'Get list of settings',
        x: ['model' => MasterSetting::class],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function index(Request $request)
    {
        $masterSettings = $this->masterSettingService->collection($request->all());

        return $this->collection(new Collection($masterSettings));
    }

    #[OA\Get(
        path: '/api/settings/{masterSetting_id}',
        operationId: 'getVersionDetail',
        tags: ['MasterSettings'],
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
        $masterSettingObj = $this->masterSettingService->resource($id);

        return $this->resource(new Resource($masterSettingObj));
    }
}
