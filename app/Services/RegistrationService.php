<?php
namespace App\Services;

use App\Models\User;

class RegistrationService
{

    public function register ($request)
    {
        $validatedData = $request->validated();

        $validatedData['password'] = bcrypt($validatedData['password']);

        $validatedData['email_verified_at'] = now();

        return User::create($validatedData);
    }
}