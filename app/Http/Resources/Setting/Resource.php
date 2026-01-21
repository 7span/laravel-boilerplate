<?php

namespace App\Http\Resources\Setting;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Setting::class;

    public function toArray(Request $request): array
    {
        $data = $this->fields();

        return $data;
    }
}
