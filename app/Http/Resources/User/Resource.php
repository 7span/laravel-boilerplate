<?php

namespace App\Http\Resources\User;

use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;
    protected $model = 'User';

    public function toArray($request)
    {
        $data =  $this->fields();
        return $data;
    }
}
