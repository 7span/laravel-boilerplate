<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHelper
{
    public static function notFoundHandler(NotFoundHttpException $e, Request $request)
    {
        if ($request->is('api/*') && $e->getPrevious() instanceof ModelNotFoundException) {
            if (method_exists($e->getPrevious(), 'getModel')) {
                $modelName = last(explode('\\', $e->getPrevious()->getModel()));
            } else {
                $modelName = 'Resource';
            }
            $modelName = preg_replace('/([a-z])([A-Z])/s', '$1 $2', $modelName);
            $modelName = strtolower($modelName);
            $modelName = ucfirst($modelName);
            throw new CustomException(__('entity.entityNotFound', ['entity' => "$modelName data"]));
        }
    }
}
