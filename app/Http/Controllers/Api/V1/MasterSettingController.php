<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
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
        path: '/api/v1/settings',
        operationId: 'getMasterSettings',
        tags: ['MasterSettings'],
        summary: 'Get list of settings',
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
                name: 'limit',
                in: 'query',
                description: "Pagination limit, '-1' to get all data."
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: 'The page of results to return.'
            ),
        ],
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
        path: '/api/v1/versions/{masterSetting_id}',
        operationId: 'getVersionDetail',
        tags: ['MasterSettings'],
        summary: 'Get detail of settings',
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
                name: 'masterSetting_id',
                in: 'path',
                required: true,
                description: 'Id of the setting',
                schema: new OA\Schema(
                    type: 'string'
                )
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
