<?php

namespace App\Http\Resources\Country;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Country $resource
 */
#[SchemaName('Country')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Country::class;

    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier of the country.
             */
            'id' => $this->id,
            /**
             * The full name of the country.
             */
            'name' => $this->name,
            /**
             * ISO 3166-1 alpha-2 country code (e.g. US).
             */
            'iso' => $this->iso,
            /**
             * ISO 3166-1 alpha-3 country code (e.g. USA).
             */
            'iso3' => $this->iso3,
            /**
             * Numeric ISO country code.
             */
            'iso_code' => $this->iso_code,
            /**
             * International dialing code (e.g. +1).
             */
            'calling_code' => $this->calling_code,
            /**
             * The currency code for the country (e.g. USD).
             */
            'currency' => $this->currency,
            /**
             * URL or path to the country flag icon.
             */
            'icon' => $this->icon,
            /**
             * Active/inactive status of the country record.
             */
            'status' => $this->status,
        ];
    }
}
