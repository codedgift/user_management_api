<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Services\RegistrationService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Exception;

class RegistrationController extends Controller
{
    use Response;

    /**
     * @var RegistrationService
     */
    protected RegistrationService $registrationService;

    /**
     * @param RegistrationService $registrationService
     */
    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * @param RegistrationRequest $request
     * @return JsonResponse
     */
    public function register (RegistrationRequest $request): \Illuminate\Http\JsonResponse
    {
        try {

            $validatedData = $request->validated();
            
            $user = $this->registrationService->register($validatedData);

            return $this->sendSuccess2(
                [
                    'message' => 'User registered successfully.',
                    'data' => $user
                ], HttpResponse::HTTP_CREATED
            );

        } catch (Exception $e) {

            return $this->sendError([
                    'message' => $e->getMessage(),
                    'status_code' => HttpResponse::HTTP_INTERNAL_SERVER_ERROR
                ]);
        }
    }
}
