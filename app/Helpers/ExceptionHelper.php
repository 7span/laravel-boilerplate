<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHelper
{
    public static function notFoundHandler(NotFoundHttpException $e, Request $request)
    {
        if ($request->is('api/*')) {
            if (method_exists($e->getPrevious(), 'getModel')) {
                $modelName = last(explode('\\', $e->getPrevious()->getModel()));
                // ... rest of your code
            } else {
                // Handle the case where there's no getModel() method
                $modelName = 'Resource'; // Or a default value
            }

            $modelKey = strtolower(preg_replace('/([a-z])([A-Z])/s', '$1_$2', $modelName));
            $modelName = preg_replace('/([a-z])([A-Z])/s', '$1 $2', $modelName);
            $modelName = strtolower($modelName);
            $modelName = ucfirst($modelName);
            $error['errors'][$modelKey][] = __('entity.entityNotFound', ['entity' => "$modelName data"]);

            return response()->json($error, 404);
        }
    }
}
