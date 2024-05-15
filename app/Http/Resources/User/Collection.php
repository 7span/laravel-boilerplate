<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    protected $model = Resource::class;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request)
    {
        return $this->collection;
    }
}
