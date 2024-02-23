<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserDeletedException;
use App\Exceptions\UserNotVerifiedException;
use Illuminate\Support\Facades\Auth;
// use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\User;
use App\Services\LoginService;
use Tests\TestCase;

class LoginFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The instance of LoginService used for testing.
     *
     * @var LoginService
     */
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new LoginService();
    }

    /** @test */
    public function throws_exception_for_unverified_user()
    {
        $user = User::factory()->unverified()->create();

        Auth::shouldReceive('attempt')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        $this->expectException(UserNotVerifiedException::class);

        $this->service->authenticate(['email' => $user->email, 'password' => '@Password2024']);
    }
  
    /** @test */
    public function throws_exception_for_soft_deleted_user()
    {
        $user = User::factory()->softDeleted()->create();

        Auth::shouldReceive('attempt')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        $this->expectException(UserDeletedException::class);

        $this->service->authenticate(['email' => $user->email, 'password' => '@Password2024']);
    }

    /** @test */
    public function email_and_password_are_required_for_login()
    {
        $response = $this->postJson('/api/v1/auth/login', ['email' => null, 'password' => null]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email', 'password']);
    }

     /** @test */
     public function email_must_be_a_valid_email_address()
     {
         $response = $this->postJson('/api/v1/auth/login', [
             'email' => 'not-a-valid-email',
             'password' => 'password123',
         ]);
 
         $response->assertStatus(422);

         $response->assertJsonValidationErrors(['email']);
     }

     /** @test */
    public function email_and_password_must_be_strings()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 123,
            'password' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }
}
