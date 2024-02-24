<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\RegistrationService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterFeatureTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function it_registers_a_user_successfully()
   {
        $userData = [
            'name' => 'gift Doe',
            'email' => 'gift@example.com',
            'password' => '@Password2024',
            'role' => 'user'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
             ->assertJson([
                 'status' => true, 
                 'message' => 'User registered successfully.',
                 'data' => [
                     'name' => 'gift Doe',
                     'email' => 'gift@example.com',
                 ],
             ]);

        $this->assertDatabaseHas('users', [
            'email' => 'gift@example.com',
        ]);
   }

   /** @test */
   public function it_fails_when_email_is_already_taken()
   {
       User::create([
           'name' => 'Existing User',
           'email' => 'existing@example.com',
           'password' => bcrypt('@Password2024'),
           'role' => 'user'
       ]);

       $userData = [
           'name' => 'gift Doe',
           'email' => 'existing@example.com',
           'password' => '@Password2024',
           'role' => 'user'
       ];

       $response = $this->postJson('/api/v1/auth/register', $userData);

       $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
   }

   /** @test */
   public function it_fails_with_invalid_email_format()
   {
       $userData = [
           'name' => 'gift Doe',
           'email' => 'invalid-email',
           'password' => '@Password2024',
           'role' => 'user'
       ];

       $response = $this->postJson('/api/v1/auth/register', $userData);

       $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
   }

   /** @test */
   public function it_handles_unexpected_errors_gracefully()
   {
       $this->mock(RegistrationService::class, function ($mock) {
           $mock->shouldReceive('register')->andThrow(new Exception('Unexpected error'));
       });

       $userData = [
           'name' => 'gift Doe',
           'email' => 'gift@example.com',
           'password' => '@Password2024',
           'role' => 'user'
       ];

       $response = $this->postJson('/api/v1/auth/register', $userData);

       $response->assertStatus(500)
                ->assertJson([
                    'status' => false,
                    'message' => 'Unexpected error',
                ]);
   }

   /** @test */
   public function name_email_password_role_are_required_for_registration()
   {
       $response = $this->postJson(
            '/api/v1/auth/register', 
            [
                'name' => null,
                'email' => null, 
                'password' => null,
                'role' => null
            ]
        );

       $response->assertStatus(422);

       $response->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
   }

   /** @test */
   public function registration_succeeds_with_valid_password()
   {
       $userData = [
          'name' => 'New User',
          'email' => 'new@example.com',
          'role' => 'user',
          'password' => 'ValidPassword1@',
       ];

       $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true, 
                'message' => 'User registered successfully.',
                'data' => [
                    'name' => 'New User', 
                    'email' => 'new@example.com', 
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com'
        ]);
   }

   /** @test */
   public function registration_fails_without_uppercase_letter_in_password()
   {
       $userData = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'role' => 'user',
            'password' => 'validpassword1@',
       ];

       $response = $this->postJson('/api/v1/auth/register', $userData);

       $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
   }

   /** @test */
   public function registration_fails_without_lowercase_letter_in_password()
   {
       $userData = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'role' => 'user',
            'password' => 'VALIDPASSWORD1@',
       ];

       $response = $this->postJson('/api/v1/auth/register', $userData);

       $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
   }

   /** @test */
   public function registration_fails_without_numeric_digit_in_password()
   {
        $userData = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'role' => 'user',
            'password' => 'ValidPassword@',
        ];

       $response = $this->postJson('/api/v1/auth/register', $userData);

       $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
   }

   /** @test */
   public function registration_fails_without_special_character_in_password()
   {
       $userData = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'role' => 'user',
            'password' => 'ValidPassword1',
       ];

       $response = $this->postJson('/api/v1/auth/register', $userData);

       $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
   }

   /** @test */
    public function registration_fails_without_role()
    {
        $userData = [
            'name' => 'gift Doe',
            'email' => 'gift@example.com',
            'password' => 'ValidPassword1@'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['role']);
    }

    /** @test */
    public function registration_fails_with_invalid_role_value()
    {
        $userData = [
            'name' => 'gift Doe',
            'email' => 'gift@example.com',
            'password' => 'ValidPassword1@',
            'role' => 'invalid_role', 
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['role']);
    }

    /** @test */
    public function registration_succeeds_with_valid_role()
    {
        $userData = [
            'name' => 'gift Doe',
            'email' => 'gift@example.com',
            'password' => 'ValidPassword1@',
            'role' => 'user',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => true, 
                    'message' => 'User registered successfully.',
                    'data' => [
                        'name' => 'gift Doe',
                        'email' => 'gift@example.com',
                        'role' => 'user'
                    ],
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'gift@example.com',
            'role' => 'user',
        ]);
    }

    /** @test */
    public function registration_fails_if_password_is_shorter_than_8_characters()
    {
        $userData = [
            'name' => 'Gift Doe',
            'email' => 'gift@example.com',
            'password' => 'Short1!',
            'role' => 'user',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function registration_succeeds_if_password_is_at_least_8_characters_long()
    {
        $userDataExact = [
            'name' => 'Gift Doe',
            'email' => 'gift@gmail.com',
            'password' => 'Valid1@3',
            'role' => 'user',
        ];

        $responseExact = $this->postJson('/api/v1/auth/register', $userDataExact);

        $responseExact->assertStatus(201);

        $userDataLong = [
            'name' => 'Gift Doe',
            'email' => 'gift2@gmail.com',
            'password' => 'LongerValid1@3',
            'role' => 'user',
        ];

        $responseLong = $this->postJson('/api/v1/auth/register', $userDataLong);

        $responseLong->assertStatus(201);
    }


}
