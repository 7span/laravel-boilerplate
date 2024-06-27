<?php

namespace App\Http\Resources\Country;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Country::class;

    public function toArray(Request $request): array
    {
        $data = $this->fields();

        return $data;
    }
}
