<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Country;
use App\Traits\PaginationTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CountryService
{
    use PaginationTrait;

    private Country $countryObj;

    public function __construct()
    {
        $this->countryObj = new Country;
    }

    /**
     * @param array<string, mixed> $inputs
     * @return LengthAwarePaginator<int, Model>|Collection<int, Model>
     */
    public function collection(array $inputs): LengthAwarePaginator|Collection
    {
        $countries = $this->countryObj->getQB()->where('status', 'active');

        /** @phpstan-ignore-next-line */
        return $this->paginationAttribute($countries);
    }
}
