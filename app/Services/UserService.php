<?php
namespace App\Services;

use App\Exceptions\UserNotFound;
use App\Models\User;

class UserService
{
    public function allUsers ()
    {
        return User::paginate(10);
    }

    public function create ($request)
    {
        $validatedData = $request->validated();

        $validatedData['password'] = bcrypt($validatedData['password']);

        $validatedData['email_verified_at'] = now();

        return User::create($validatedData);
    }

    public function show ($id)
    {
       return User::findOrFail($id);
    }

    public function updateRecord ($request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validated();

        $validatedData = array_filter($validatedData, function($value, $key) {
            if (in_array($key, ['name', 'role'])) {
                return !is_null($value) && trim($value) !== '';
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);

        $user->update($validatedData);

        return $user;

    }

    public function deleteRecord ($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return true;
    }
}