<?php

use Illuminate\Support\Str;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Middleware\MarkNotificationsAsRead;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            'notification-read' => MarkNotificationsAsRead::class,
        ]);
        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $e,$request) {

            if ($request->is('api/*') && $e instanceof NotFoundHttpException &&  $e->getPrevious() instanceof ModelNotFoundException) {
                $modelName = Str::headline(class_basename($e->getPrevious()->getModel()));
                throw new CustomException(__('entity.entityNotFound', ['entity' => "$modelName data"]));
            }

            if($request->is('api/*') && $e instanceof NotFoundHttpException) {
               $route = $request->path();
               throw new CustomException(__('entity.entityNotFound', ['entity' => "route $route"]));
            }

            return null;
        });
    })->create();
