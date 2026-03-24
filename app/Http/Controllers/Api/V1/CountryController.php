<?php

<<<<<<< HEAD:app/Http/Controllers/Api/CountryController.php
declare(strict_types=1);

namespace App\Http\Controllers\Api;
=======
namespace App\Http\Controllers\Api\V1;
>>>>>>> origin/master:app/Http/Controllers/Api/V1/CountryController.php

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
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

<<<<<<< HEAD:app/Http/Controllers/Api/CountryController.php
    #[OA\Get(
        path: '/api/countries',
        tags: ['Country'],
        operationId: 'countryList',
        summary: 'Country list',
        x: ['model' => Country::class]
    )]
    public function __invoke(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
=======
    /**
     * List.
     *
     * @unauthenticated
     */
    public function __invoke(Request $request)
>>>>>>> origin/master:app/Http/Controllers/Api/V1/CountryController.php
    {
        $countries = $this->countryService->collection($request->all());

        return CountryResource::collection($countries);
    }
}
