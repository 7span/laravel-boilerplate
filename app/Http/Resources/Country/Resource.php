<?php

declare(strict_types = 1);

namespace App\Http\Resources\Country;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Data\Response\CountryData;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Country::class;

    public function toArray(Request $request): array
    {
        $data = CountryData::fromModel($this->resource)->toArray();

        return $data;
    }
}
