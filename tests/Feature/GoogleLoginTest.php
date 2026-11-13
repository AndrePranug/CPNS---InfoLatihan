<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'test-client',
            'services.google.client_secret' => 'test-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);
    }

    public function test_callback_creates_user_from_google_account(): void
    {
        $googleUser = (new SocialiteUser)->map([
            'id' => 'google-123',
            'name' => 'Budi Google',
            'email' => 'budi@gmail.com',
        ]);

        Socialite::shouldReceive('driver->user')->andReturn($googleUser);

        $this->get('/auth/google/callback')->assertRedirect('/dashboard');

        $user = User::sole();
        $this->assertSame('budi@gmail.com', $user->email);
        $this->assertSame('google-123', $user->google_id);
        $this->assertAuthenticatedAs($user);
    }

    public function test_callback_links_existing_email_and_reuses_account(): void
    {
        $existing = User::factory()->create(['email' => 'budi@gmail.com', 'google_id' => null]);

        $googleUser = (new SocialiteUser)->map([
            'id' => 'google-123',
            'name' => 'Budi',
            'email' => 'budi@gmail.com',
        ]);

        Socialite::shouldReceive('driver->user')->andReturn($googleUser);

        $this->get('/auth/google/callback')->assertRedirect('/dashboard');

        $this->assertSame(1, User::count());
        $this->assertSame('google-123', $existing->refresh()->google_id);
    }

    public function test_redirect_is_unavailable_without_credentials(): void
    {
        config(['services.google.client_id' => null]);

        $this->get('/auth/google/redirect')->assertNotFound();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
