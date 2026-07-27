<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Resources\Country\Resource as CountryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Country
 */
#[Group('Country', weight: 30)]
class CountryController extends Controller
{
    use ApiResponser;

    public function __construct(private CountryService $countryService) {}

    /**
     * List.
     *
     * @unauthenticated
     */
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $countries = $this->countryService->collection($request->all());

        return CountryResource::collection($countries);
    }
}
