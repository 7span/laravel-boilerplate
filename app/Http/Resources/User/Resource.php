<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = 'User';

    public function toArray(Request $request): array
    {
        $data = $this->fields();
        return $data;
    }
}
