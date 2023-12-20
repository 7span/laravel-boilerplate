<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Country\Index;
use App\Http\Resources\Country\Collection as CountryCollection;

class CountryController extends Controller
{
    use ApiResponser;

    public function __construct(private CountryService $countryService)
    {
        //
    }

    /**
     * Country List
     */
    public function index(Index $request)
    {
        $countries = $this->countryService->collection($request->all());

        return $this->collection(new CountryCollection($countries));
    }
}
