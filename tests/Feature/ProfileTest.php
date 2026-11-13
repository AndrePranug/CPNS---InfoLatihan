<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_shows_user_data(): void
    {
        $user = User::factory()->create(['name' => 'Peserta Satu']);

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/Edit')
                ->where('profile.name', 'Peserta Satu')
            );
    }

    public function test_name_and_email_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Nama Baru',
            'email' => 'baru@example.com',
        ])->assertSessionHas('success');

        $this->assertSame('Nama Baru', $user->fresh()->name);
        $this->assertSame('baru@example.com', $user->fresh()->email);
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'dipakai@example.com']);
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Nama',
            'email' => 'dipakai@example.com',
        ])->assertSessionHasErrors('email');
    }

    public function test_password_can_be_changed_with_current_password(): void
    {
        $user = User::factory()->create(['password' => 'lama-sekali']);

        $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'lama-sekali',
            'password' => 'baru-sekali',
            'password_confirmation' => 'baru-sekali',
        ])->assertSessionHas('success');

        $this->assertTrue(Hash::check('baru-sekali', $user->fresh()->password));
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => 'lama-sekali']);

        $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'salah',
            'password' => 'baru-sekali',
            'password_confirmation' => 'baru-sekali',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('lama-sekali', $user->fresh()->password));
    }
}
