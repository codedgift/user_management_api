<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
        // Prepare the validated data as it would be after the request validation
        $validatedData = [
            'name' => 'Gift Doe',
            'email' => 'gift@example.com',
            'password' => 'Password2024!',
            'role' => 'user',
        ];

        // Directly pass the validated data to the register method of the service
        $user = $this->registrationService->register($validatedData);

        // Assertions to ensure the user was registered correctly
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
