<?php

namespace App\Services;

use App\Models\Country;
use App\Traits\BaseModel;

class CountryService
{
    use BaseModel;

    private $countryObj;

    public function __construct()
    {
        $this->countryObj = new Country();
    }

    public function collection(array $inputs)
    {
        $countries = $this->countryObj->getQB();

        $inputs['limit'] = isset($inputs['limit']) ? $inputs['limit'] : config('site.paginationLimit');

        return (isset($inputs['limit']) && $inputs['limit'] == '-1') ? $countries->get() : $countries->paginate($inputs['limit']);
    }
}
