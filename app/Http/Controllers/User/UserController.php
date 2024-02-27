<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UpdateRequest;
use App\Services\UserService;
use App\Traits\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class UserController extends Controller
{
    use Response;

    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $users = $this->userService->allUsers();

            return $this->sendSuccess(
                [
                    'message' => 'All users fetched successfully.',
                    'data' => $users
                ], HttpResponse::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->sendError([
                'message' => $e->getMessage(),
                'status_code' => HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }


    /**
     * @param RegistrationRequest $request
     * @return JsonResponse
     */
    public function store(RegistrationRequest $request): JsonResponse
    {
        try {

            $validatedData = $request->validated();

            $user = $this->userService->create($validatedData);


            return $this->sendSuccess(
                [
                    'message' => 'User created successfully.',
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


    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {

            $user = $this->userService->show($id);


            return $this->sendSuccess(
                [
                    'message' => 'Successful',
                    'data' => $user
                ], HttpResponse::HTTP_OK
            );

        } catch (Exception $e) {

            return $this->sendError([
                    'message' => $e->getMessage(),
                    'status_code' => HttpResponse::HTTP_INTERNAL_SERVER_ERROR
                ]);
        }
    }


    /**
     * @param UpdateRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        try {

            $validatedData = $request->validated();

            $user = $this->userService->updateRecord($validatedData, $id);


            return $this->sendSuccess(
                [
                    'message' => 'User updated successfully',
                    'data' => $user
                ], HttpResponse::HTTP_PARTIAL_CONTENT
            );

        } catch (Exception $e) {

            return $this->sendError([
                    'message' => $e->getMessage(),
                    'status_code' => HttpResponse::HTTP_INTERNAL_SERVER_ERROR
                ]);
        }
    }


    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {

            $user = $this->userService->deleteRecord($id);


            return $this->sendSuccess(
                [
                    'message' => 'User deleted successfully',
                    'data' => null
                ], HttpResponse::HTTP_OK
            );

        } catch (Exception $e) {

            return $this->sendError([
                    'message' => $e->getMessage(),
                    'status_code' => HttpResponse::HTTP_INTERNAL_SERVER_ERROR
                ]);
        }
    }
}
