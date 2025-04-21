<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();
                
                if (!$user) {
                    $user = User::create([
                        'username' => $googleUser->getEmail(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'first_name' => $googleUser->user['given_name'] ?? '',
                        'last_name' => $googleUser->user['family_name'] ?? '',
                        'role' => 'student',
                        'password' => bcrypt(rand(100000, 999999)),
                    ]);
                } else {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }

            Auth::login($user);
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed. Please try again.');
        }
    }
} 