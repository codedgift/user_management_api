<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use App\Traits\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exceptions\UserNotVerifiedException;
use App\Exceptions\UserDeletedException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use App\Exceptions\InvalidCredentialsException;

class LoginController extends Controller
{
    use Response;

    protected LoginService $loginService;

    /**
     * @param LoginService $loginService
     */
    public function __construct (LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login (LoginRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {

            $loginData = $this->loginService->authenticate($request->only(['email', 'password']));

            return $this->sendSuccess(
                    ['message' => 'Login successful','data' => $loginData],
                    HttpResponse::HTTP_OK
                );

        } catch (InvalidCredentialsException | UserNotVerifiedException | UserDeletedException $e) {

            $status = match (get_class($e)) {
                InvalidCredentialsException::class => HttpResponse::HTTP_UNAUTHORIZED,
                UserNotVerifiedException::class => HttpResponse::HTTP_FORBIDDEN,
                UserDeletedException::class => HttpResponse::HTTP_NOT_FOUND,
                default => HttpResponse::HTTP_BAD_REQUEST,
            };

            return $this->sendError(['message' => $e->getMessage(), 'status_code' => $status]);

        } catch (Exception $e) {

            return $this->sendError([
                    'message' => $e->getMessage(),
                    'status_code' => HttpResponse::HTTP_INTERNAL_SERVER_ERROR
                ]);
        }
    }
}
