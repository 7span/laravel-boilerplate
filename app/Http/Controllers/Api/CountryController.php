<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Country\Resource as CountryResource;

class CountryController extends Controller
{
    use ApiResponser;

    private CountryService $countryService;

    public function __construct()
    {
        $this->countryService = new CountryService;
    }

    #[OA\Get(
        path: '/api/countries',
        tags: ['Country'],
        operationId: 'countryList',
        summary: 'Country list',
        // x: ['model' => Country::class]
        parameters: [
            new OA\Parameter(
                name: 'include',
                in: 'query',
                required: false,
                description: 'Include related data',
            ),
            new OA\Parameter(
                name: 'media',
                in: 'query',
                required: false,
                description: 'Media types to include: `profile`',
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Page number',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Country list',
            ),
        ],
    )]
    public function __invoke(Request $request)
    {
        $countries = $this->countryService->collection($request->all());

        return CountryResource::collection($countries);
    }
}
