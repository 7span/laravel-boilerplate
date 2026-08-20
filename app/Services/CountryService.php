<?php

namespace App\Services;

use App\Models\Country;
use App\Enums\CountryStatus;
use App\Traits\PaginationTrait;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CountryService
{
    use PaginationTrait;

    private Country $countryObj;

    public function __construct()
    {
        $this->countryObj = new Country;
    }

    /**
     * Fetch the active countries through the query builder so `filter`, `sort`
     * and `fields` apply.
     *
     * @return LengthAwarePaginator<int, Country>|Collection<int, Country>
     */
    public function collection(): LengthAwarePaginator|Collection
    {
        $countries = $this->countryObj->getQB()
            ->where('status', CountryStatus::ACTIVE);

        return $this->paginationAttribute($countries);
    }
}
