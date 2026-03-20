<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use App\Http\Resources\Country\Resource as CountryResource;

/**
 * @tags Country
 */
#[Group('Country', weight: 30)]
class CountryController extends Controller
{
    use ApiResponser;

    private CountryService $countryService;

    public function __construct()
    {
        $this->countryService = new CountryService;
    }

    /**
     * List.
     *
     * @unauthenticated
     */
    #[QueryParameter('appends')]
    public function __invoke(Request $request)
    {
        $countries = $this->countryService->collection($request->all());

        return CountryResource::collection($countries);
    }
}
