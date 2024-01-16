<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException as ValidationUnauthorizedException;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Set error response structure for `Validation of request`
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $errors = $exception->errors();
            $errorMessage = isset(array_values($errors)[0][0]) ? array_values($errors)[0][0] : null;

            return $this->error([
                'message' => $errorMessage,
                // 'errors' => $errors
            ], 400);
        }

        // Set common error message if any model not found in system.
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $modelName = last(explode('\\', $exception->getModel()));
            $modelKey = strtolower(preg_replace('/([a-z])([A-Z])/s', '$1_$2', $modelName));
            $modelName = preg_replace('/([a-z])([A-Z])/s', '$1 $2', $modelName);
            $modelName = strtolower($modelName);
            $modelName = ucfirst($modelName);
            // $exception->getModel();
            $error['message'] = __('entity.entityNotFound', ['entity' => "$modelName data"]);

            return $this->error($error, 404);
        }

        // Set an error message for any invalid url fired on server.
        if ($exception instanceof NotFoundHttpException) {
            return $this->error(
                [
                    'message' => __('message.invalidUrl'),
                ],
                404
            );
        }

        // Set an error message for unauthorization errors.
        if ($exception instanceof AuthorizationException || $exception instanceof ValidationUnauthorizedException) {
            return $this->error(
                [
                    'message' => __('message.unauthorizedAccess'),
                ],
                401
            );
        }

        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return $this->error(
                [
                    'message' => __('message.unauthorizedAccess'),
                ],
                401
            );
        }

        return parent::render($request, $exception);
    }
}
