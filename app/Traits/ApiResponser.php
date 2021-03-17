<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponser
{

    private function success($data, $code)
    {
        return response()->json($data, $code);
    }

    private function error($data, $code = 400)
    {
        return response()->json($data, $code);
    }

    private function resource(JsonResource $resource, $code = 200)
    {
        return $this->success($resource, $code);
    }

    private function collection(ResourceCollection $collection, $code = 200)
    {
        return $collection;
    }
}
