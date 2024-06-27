<?php

use Illuminate\Http\Request;
use App\Helpers\ExceptionHelper;
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
                ->prefix('api/v1')
                ->group(base_path('routes/api-v1.php'));

            Route::middleware('api')
                ->as('admin.')
                ->prefix('api/v1/admin')
                ->group(base_path('routes/admin-v1.php'));

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
        $exceptions->renderable(fn (NotFoundHttpException $e, Request $request) => ExceptionHelper::notFoundHandler($e, $request));
    })->create();
