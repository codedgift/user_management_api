<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException; // Add this line
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // You can add reporting logic here if needed
        });
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated. Please login.'], 401);
        }

        // For API-only applications, you might not use the redirect below
        // return redirect()->guest(route('login'));

        // If you don't have a web login route, just return a JSON response
        return response()->json(['error' => 'Unauthenticated. Access denied.'], 401);
    }

}
