<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Country\Index;
use App\Http\Resources\Country\Collection as CountryCollection;

class CountryController extends Controller
{
    use ApiResponser;

    public function __construct(private CountryService $countryService)
    {
    }

    #[OA\Get(
        path: '/api/v1/countries',
        tags: ['Country'],
        operationId: 'countryList',
        summary: 'Country list',
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
                name: 'filter[iso]',
                in: 'query',
            ),
            new OA\Parameter(
                name: 'filter[iso3]',
                in: 'query',
            ),
            new OA\Parameter(
                name: 'filter[name]',
                in: 'query',
            ),
            new OA\Parameter(
                name: 'filter[numcode]',
                in: 'query',
            ),
            new OA\Parameter(
                name: 'filter[phonecode]',
                in: 'query',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
        ],
    )]
    public function index(Index $request)
    {
        $countries = $this->countryService->collection($request->all());

        return $this->collection(new CountryCollection($countries));
    }
}
