<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{

    public $collects = 'App\Http\Resources\User\Resource';

    public function toArray($request)
    {
        return $this->collection;
    }
}