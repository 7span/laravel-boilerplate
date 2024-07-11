<?php

namespace App\Services;

use App\Models\Country;
use App\Traits\BaseModel;
use App\Traits\PaginationTrait;

class CountryService
{
    use BaseModel, PaginationTrait;

    private Country $countryObj;

    public function __construct()
    {
        $this->countryObj = new Country;
    }

    public function collection(array $inputs)
    {
        $countries = $this->countryObj->getQB();

        return $this->paginationAttribute($countries);
    }
}
