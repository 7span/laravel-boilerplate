<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    protected $model = 'App\Http\Resources\User\Resource';

    public function toArray(Request $request): array
    {
        return $this->collection;
    }
}
