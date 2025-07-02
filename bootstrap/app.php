<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        using: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('api')
                ->as('admin.')
                ->prefix('api/admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->prefix('developer')
                ->group(base_path('routes/developer.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'developer' => Spatie\LittleGateKeeper\AuthMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e) {
            $message = $e->getMessage();

            return response()->json([
                'message' => $message,
            ], 404);
        });
    })->create();
