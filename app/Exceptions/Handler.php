<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $guard = Arr::get($exception->guards(), 0);
        return $this->shouldReturnJson($request, $exception)
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()->guest($exception->redirectTo() ?? ($guard == 'admin' ? route('/admin/login') : route('user/login')));
    }

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            // convert validation errors to json response
            if ($e instanceof ValidationException) {
                $error = $e->validator->errors()->first();
                return response()->json([
                    "success" => false,
                    "message" => $error,
                    "code" => 422
                ]);
            }

            // when model nonexistent
            if ($e instanceof ModelNotFoundException) {
                $modelName = strtolower(class_basename($e->getModel()));
                return response()->json([
                    "success" => false,
                    "message" => 'Does not exists any' . $modelName . 'with the spicified identificator',
                    "code" => 404
                ]);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    "success" => false,
                    "message" => 'Unauthenticated',
                    "code" => 401
                ]);
            }

            if ($e instanceof AuthorizationException) {
                return response()->json([
                    "success" => false,
                    "message" => $e->getMessage(),
                    "code" => 403
                ]);
            }

            // when write nonexistent URL
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    "success" => false,
                    "message" => 'The specified URL connot be found.',
                    "code" => 404
                ]);
            }

            // when try excepted resource api methods or nonexistent method
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    "success" => false,
                    "message" => 'The specified methode for the request is invaild.',
                    "code" => 404
                ]);
            }

            // general http exception
            if ($e instanceof HttpException) {
                return response()->json([
                    "success" => false,
                    "message" => $e->getMessage(),
                    "code" => $e->getStatusCode()
                ]);
            }

            // when have error in query like delete an instance which has a relation with other models
            if ($e instanceof QueryException) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1451) {
                    return response()->json([
                        "success" => false,
                        "message" => 'Cannot remove this resource permanently. It is related with any other resource',
                        "code" => 409
                    ]);
                }
            }

            return response()->json([
                "success" => false,
                "message" => 'Unexpected Exception. Try later.',
                "code" => 500
            ]);
        }

        // if we turn on the debugbar
        if (config('app.debug')) {
            return parent::render($request, $e);
        }
    }
}