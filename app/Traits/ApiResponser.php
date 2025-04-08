<?php

namespace App\Traits;

trait ApiResponser
{
    private function responseJson($data, $code = 200)
    {
        return response()->json($data, $code);
    }

    private function success($data, $code = 200)
    {
        return $this->responseJson($data, $code);
    }

    private function error($data, $code = 400)
    {
        return $this->responseJson($data, $code);
    }

    private function resource($resource, $code = 200)
    {
        return $this->success($resource, $code);
    }

    private function collection($collection)
    {
        return $collection;
    }
}
