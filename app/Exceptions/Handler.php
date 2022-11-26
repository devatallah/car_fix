<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
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
     * @return void
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $guard = Arr::get($exception->guards(), 0);
        switch ($guard) {
            case 'admin':
                $login = '/admin/login';
//                return $request->wantsJson()
//                    ? mainResponse(false, __('api.unauthenticated'), [], [], 401) :
                redirect(url(locale() . $login));
            default:
                $login = '/login';
                break;
        }
//        return $request->wantsJson()
//            ? mainResponse(false, __('api.unauthenticated'), [], [], 401) :
        redirect()->guest(url(locale() . $login));
    }
}
