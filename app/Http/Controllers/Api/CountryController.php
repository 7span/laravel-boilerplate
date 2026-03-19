<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Resources\Country\Resource as CountryResource;

/**
 * @tags Countries
 */
#[Group('Countries', weight: 4)]
class CountryController extends Controller
{
    use ApiResponser;

    private CountryService $countryService;

    public function __construct()
    {
        $this->countryService = new CountryService;
    }

    /**
     * List countries.
     *
     * Returns a paginated list of all available countries. Supports filtering and sorting via query parameters.
     */
    public function __invoke(Request $request)
    {
        $countries = $this->countryService->collection($request->all());

        return CountryResource::collection($countries);
    }
}
