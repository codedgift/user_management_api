<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        // Check if the request expects a JSON response
        if ($request->expectsJson()) {
            // Return a JSON response indicating unauthenticated
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // For non-API requests, redirect to the login route
        // Ensure you have a named 'login' route defined if you use this
        // return redirect()->guest(route('login'));

        // Alternatively, for applications without a web frontend or a specific login route,
        // you might choose to return a simple response or handle differently
        return response('Unauthenticated.', 401);
    }

}
