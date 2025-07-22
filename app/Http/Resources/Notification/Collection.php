<?php

namespace App\Http\Resources\Notification;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    public $collects = Resource::class;

    public function toArray($request)
    {
        return $this->collection;
    }
}
