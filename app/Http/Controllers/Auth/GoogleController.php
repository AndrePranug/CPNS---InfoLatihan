<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

class GoogleController
{
    public function redirect(): SymfonyRedirect
    {
        abort_if(empty(config('services.google.client_id')), 404, 'Login Google belum dikonfigurasi.');

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $google = Socialite::driver('google')->user();

        $user = User::query()->where('google_id', $google->getId())->first()
            ?? User::query()->where('email', $google->getEmail())->first();

        if ($user === null) {
            $user = User::create([
                'name' => $google->getName() ?? $google->getNickname() ?? 'Pengguna Google',
                'email' => $google->getEmail(),
                'password' => Str::random(40),
                'google_id' => $google->getId(),
                'email_verified_at' => now(),
            ]);
        } elseif ($user->google_id === null) {
            $user->update(['google_id' => $google->getId()]);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors(['email' => 'Akun dinonaktifkan.']);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
