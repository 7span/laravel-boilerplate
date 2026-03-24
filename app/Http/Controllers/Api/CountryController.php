<?php

declare(strict_types=1);

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
        x: ['model' => Country::class]
    )]
    public function __invoke(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $countries = $this->countryService->collection($request->all());

        return CountryResource::collection($countries);
    }
}
