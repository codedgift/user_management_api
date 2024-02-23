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

    /**
     * @param $credentials
     * @return array
     * @throws InvalidCredentialsException
     * @throws UserDeletedException
     * @throws UserNotVerifiedException
     */
    public function authenticate($credentials): array
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

    /**
     * @param User $user
     * @return void
     * @throws UserDeletedException
     * @throws UserNotVerifiedException
     */
    protected function validateUser(User $user): void
    {
        if ($user->deleted_at !== null) {
            throw new UserDeletedException('Account with this email has been deleted');
        }

        if ($user->email_verified_at === null) {
            throw new UserNotVerifiedException('Please verify your account to proceed');
        }
    }

    /**
     * @param User $user
     * @return void
     */
    protected function revokeExistingTokens(User $user): void
    {
        Token::where('user_id', $user->id)->update(['revoked' => true]);
    }

    /**
     * @param User $user
     * @return string
     */
    protected function createNewTokenForUser(User $user): string
    {
        return $user->createToken('ApexNetworksAuthToken')->accessToken;
    }

    /**
     * @param User $user
     * @return array
     */
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
