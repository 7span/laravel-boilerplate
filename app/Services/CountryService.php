<?php

namespace App\Services;

use App\Models\Country;
use App\Traits\PaginationTrait;

class CountryService
{
    use PaginationTrait;

    private Country $countryObj;

    public function __construct()
    {
        $this->countryObj = new Country;
    }

    public function collection(array $inputs)
    {
        $countries = $this->countryObj->getQB()->where('status', 'active');

        return $this->paginationAttribute($countries);
    }
}
