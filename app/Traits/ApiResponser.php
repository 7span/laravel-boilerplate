<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponser
{
    private function success(array|JsonResource $data, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
        ], $code);
    }

    private function error(string|array $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error'   => $message,
        ], $code);
    }

    private function resource(JsonResource $resource, int $code = 200): JsonResponse
    {
        return $this->success($resource, $code);
    }

    private function collection($collection, $code = 200) : JsonResponse
    {
        return $this->success($collection, $code);
    }

}
