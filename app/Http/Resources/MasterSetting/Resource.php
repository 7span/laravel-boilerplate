<?php

namespace App\Http\Resources\MasterSetting;

use Illuminate\Http\Request;
use App\Models\MasterSetting;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = MasterSetting::class;

    public function toArray(Request $request): array
    {
        $data = $this->fields();

        return $data;
    }
}
