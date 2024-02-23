<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Token;

class PassportAuthMiddleware
{
    /**
     * @var TokenRepository
     */
    protected TokenRepository $tokenRepository;

    /**
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Check if the request has a bearer token
        if (!$request->bearerToken()) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Bearer token is missing.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Attempt to authenticate the user with the api guard
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Unauthorized Access or Invalid Token.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        /** @var \Laravel\Passport\Authenticatable $user */
        $user = Auth::guard('api')->user();

        // Now static analysis tools understand $user as an instance that has the token() method
        $token = $user->token();

        if ($token->expires_at && Carbon::now()->isAfter($token->expires_at)) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Token has expired.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
