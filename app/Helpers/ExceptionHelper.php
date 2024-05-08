<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHelper
{
    public static function notFoundHandler(NotFoundHttpException $e, Request $request): ?JsonResponse
    {
        if ($request->is('api/*')) {
            $modelName = last(explode('\\', $e->getPrevious()->getModel()));
            $modelKey = strtolower(preg_replace('/([a-z])([A-Z])/s', '$1_$2', $modelName));
            $modelName = preg_replace('/([a-z])([A-Z])/s', '$1 $2', $modelName);
            $modelName = strtolower($modelName);
            $modelName = ucfirst($modelName);
            $error['errors'][$modelKey][] = __('entity.entityNotFound', ['entity' => "$modelName data"]);
            return response()->json($error, 404);
        }
    }
}
