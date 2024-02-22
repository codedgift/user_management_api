<?php
namespace App\Services;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Laravel\Passport\Token;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UserNotVerifiedException;
use App\Exceptions\UserDeletedException;

class LoginService
{

    public function authenticate($credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw new InvalidCredentialsException('Invalid credentials provided');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->validateUser($user);
        $this->revokeExistingTokens($user);

        return $this->generateTokenResponse($user);
    }

    protected function validateUser(User $user)
    {
        if ($user->deleted_at !== null) {
            throw new UserDeletedException('Account with this email has been deleted');
        }

        if ($user->email_verified_at === null) {
            throw new UserNotVerifiedException('Please verify your account to proceed');
        }
    }

    protected function revokeExistingTokens(User $user)
    {
        Token::where('user_id', $user->id)->update(['revoked' => true]);
    }

    protected function createNewTokenForUser(User $user)
    {
        return $user->createToken('ApexNetworksAuthToken')->accessToken;
    }

    protected function generateTokenResponse(User $user): array
    {
        $tokenResult = $user->createToken('ApexNetworksAuthToken');
        $accessToken = $tokenResult->accessToken;
        $refreshToken = $tokenResult->token->id;

        return [
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

}