<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponser
{
    private function success(mixed $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    private function error(mixed $data, int $code = 400): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * @param JsonResource|array<int|string, mixed> $resource
     */
    private function resource(JsonResource|array $resource, int $code = 200): JsonResponse
    {
        return $this->success($resource, $code);
    }

    private function collection(ResourceCollection|JsonResponse $collection, int $code = 200): ResourceCollection|JsonResponse
    {
        return $collection;
    }
}
