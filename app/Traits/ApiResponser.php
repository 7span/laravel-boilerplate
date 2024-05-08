<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    private function success($data, $code): JsonResponse
    {
        return response()->json($data, $code);
    }

    private function error($data, $code = 400): JsonResponse
    {
        return response()->json($data, $code);
    }

    private function resource($resource, $code = 200): JsonResponse
    {
        return $this->success($resource, $code);
    }

    private function collection($collection, $code = 200): JsonResponse
    {
        return $collection;
    }
}
