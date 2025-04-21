@extends('layouts.auth')

@section('title', 'Reset Password - Debriefing.com')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label for="email" class="block mb-2">Email</label>
            <input type="email" id="email" name="email" class="form-input" required autofocus>
        </div>

        <div class="mb-4">
            <label for="password" class="block mb-2">New Password</label>
            <input type="password" id="password" name="password" class="form-input" required>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block mb-2">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
        </div>

        <div class="flex items-center justify-between mb-4">
            <button type="submit" class="btn-primary">
                Reset Password
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm">
                Remember your password?
                <a href="{{ route('login') }}" class="text-blue-300 hover:text-blue-200">Login</a>
            </p>
        </div>
    </form>
@endsection 