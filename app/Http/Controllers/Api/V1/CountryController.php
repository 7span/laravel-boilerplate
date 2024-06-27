<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Country\Collection as CountryCollection;

class CountryController extends Controller
{
    use ApiResponser;

    private $countryService;

    public function __construct()
    {
        $this->countryService = new CountryService;
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
                name: 'page',
                in: 'query',
                description: 'The page of results to return.'
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
    public function __invoke(Request $request)
    {
        $countries = $this->countryService->collection($request->all());

        return $this->collection(new CountryCollection($countries));
    }
}
