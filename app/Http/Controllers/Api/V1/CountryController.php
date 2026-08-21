<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Country\Resource as CountryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Country
 */
#[Group('Country', weight: 30)]
class CountryController extends Controller
{
    use ApiResponser;

    public function __construct(private readonly CountryService $countryService) {}

    /**
     * List countries.
     *
     * @unauthenticated
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<CountryResource>>
     */
    public function __invoke(): ResourceCollection
    {
        $data = $this->countryService->collection();

        return $this->collection(CountryResource::collection($data));
    }
}
