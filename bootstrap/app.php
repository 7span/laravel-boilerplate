<?php

use Illuminate\Support\Str;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DeveloperAuth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function (): void {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api-v1.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->prefix('developer')
                ->group(base_path('routes/developer.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'developer' => DeveloperAuth::class,
        ]);
        $middleware->group('api', [
            'throttle:api',
            SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $e, $request) {
            if ($request->is('api/*') && $e instanceof NotFoundHttpException && $e->getPrevious() instanceof ModelNotFoundException) {
                $modelName = Str::headline(class_basename($e->getPrevious()->getModel()));
                throw new CustomException(__('entity.entityNotFound', ['entity' => "$modelName data"]));
            }

            if ($request->is('api/*') && $e instanceof NotFoundHttpException) {
                $route = $request->path();
                throw new CustomException(__('entity.entityNotFound', ['entity' => "route $route"]));
            }
        });
    })->create();
