<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\RedirectResponse;
use Throwable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

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
     * @param $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated. Please login.'], 401);
        }

        // For API-only applications, you might not use the redirect below
        // return redirect()->guest(route('login'));

        // If you don't have a web login route, just return a JSON response
        return response()->json(['error' => 'Unauthenticated. Access denied.'], 401);
    }

    /**
     * @param $request
     * @param Throwable $e
     * @return JsonResponse|RedirectResponse|\Illuminate\Http\Response|Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): \Illuminate\Http\Response|JsonResponse|Response|RedirectResponse
    {
        $wantsJson = $request->wantsJson() ||
                 $request->header('Content-Type') === 'application/json' ||
                 $request->header('Accept') === 'application/json';

        if ($wantsJson) {
            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Oops! Something went wrong.',
                    'errors' => $e->validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return parent::render($request, $e);
    }
}
