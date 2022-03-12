<?php

namespace App\Exceptions;

use Throwable;
use App\Library\Helper;
use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException as ValidationUnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Set error response structure for `Validation of request`
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->error([
                'errors' => $exception->errors()
            ], 400);
        }

        // Set common error message if any model not found in system.
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->error(
                [
                    'message' =>  __('entity.entityNotFound')
                ],
                404
            );
        }

        // Set an error message for any invalid url fired on server.
        if ($exception instanceof NotFoundHttpException) {
            return $this->error(
                [
                    'message' =>  __('message.invalidUrl')
                ],
                404
            );
        }

        // Set an error message for unauthorization errors.
        if ($exception instanceof AuthorizationException || $exception instanceof ValidationUnauthorizedException) {
            return $this->error(
                [
                    'message' =>  __('message.unauthorizedAccess')
                ],
                401
            );
        }
        return parent::render($request, $exception);
    }
}
