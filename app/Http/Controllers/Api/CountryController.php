<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Country\Collection as CountryCollection;
use App\Models\Country;


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

    )]
    public function __invoke(Request $request)
    {
        $countries = $this->countryService->collection($request->all());

        return $this->collection(new CountryCollection($countries));
    }
}
