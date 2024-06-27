<?php

namespace App\Http\Resources\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    protected $model = Resource::class;

    public function toArray(Request $request)
    {
        return $this->collection;
    }
}
