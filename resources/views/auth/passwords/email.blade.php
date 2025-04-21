@extends('layouts.auth')

@section('title', 'Reset Password - Debriefing.com')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>

    @if (session('status'))
        <div class="bg-green-500 text-white p-4 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block mb-2">Email</label>
            <input type="email" id="email" name="email" class="form-input" required autofocus>
        </div>

        <div class="flex items-center justify-between mb-4">
            <button type="submit" class="btn-primary">
                Send Password Reset Link
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