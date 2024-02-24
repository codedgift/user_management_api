<?php

namespace Tests\Unit\Auth;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Request;

class RegistrationUnitTest extends TestCase
{
    use RefreshDatabase;

    private $registrationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrationService = new RegistrationService();
    }

    /** @test */
    public function it_registers_a_user_successfully()
    {
        $mockRequest = $this->mock(RegistrationRequest::class, function ($mock) {
            $mock->shouldReceive('validated')
                 ->andReturn([
                     'name' => 'Gift Doe',
                     'email' => 'gift@example.com',
                     'password' => 'Password2024!',
                     'role' => 'user',
                 ]);
        });

        $user = $this->registrationService->register($mockRequest);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('gift@example.com', $user->email);
        $this->assertDatabaseHas('users', ['email' => 'gift@example.com']);
    }

    /** @test */
    public function registration_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'pass',
            'role' => 'invalid-role',
        ]);

        $response->assertStatus(422); 
        $response->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }
}
