<?php

namespace Tests\Unit\Auth;

// use PHPUnit\Framework\TestCase;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Services\LoginService;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class LoginUnitTest extends TestCase
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

        $this->createPassportPersonalAccessClient();
    }

    protected function createPassportPersonalAccessClient()
    {
        $client = Client::create([
            'name' => 'Personal Access Client',
            'secret' => Str::random(40),
            'redirect' => 'http://localhost::8008',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
        ]);

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function it_throws_invalid_credentials_exception_for_wrong_credentials()
    {
        Auth::shouldReceive('attempt')->once()->with([
            'email' => 'wrong@example.com', 'password' => 'incorrect'
        ])->andReturn(false);

        $service = new LoginService();

        $this->expectException(InvalidCredentialsException::class);

        $service->authenticate(['email' => 'wrong@example.com', 'password' => 'incorrect']);
    }

    /** @test */
    public function it_authenticates_successfully_with_valid_credentials()
    {
        Auth::shouldReceive('attempt')
            ->once()
            ->with(['email' => 'admin@example.com', 'password' => '@Password2024'])
            ->andReturn(true);

        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->id = Str::uuid();
        $mockUser->name = 'Admin';
        $mockUser->email = 'admin@example.com';
        $mockUser->role = 'admin';
        $mockUser->password = bcrypt('@Password2024');
        $mockUser->email_verified_at = now();

        Auth::shouldReceive('user')->once()->andReturn($mockUser);

        $result = $this->service->authenticate(['email' => 'admin@example.com', 'password' => '@Password2024']);

        $this->assertIsArray($result);

        $this->assertArrayHasKey('access_token', $result);

        $this->assertArrayHasKey('user', $result);

        $this->assertEquals('admin@example.com', $result['user']['email']);
    }

}
