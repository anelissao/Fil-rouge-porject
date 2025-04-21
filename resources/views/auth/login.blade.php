@extends('layouts.auth')

@section('title', 'Login - Debriefing.com')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center">Login to Debriefing.com</h2>

    @if ($errors->any())
        <div class="bg-red-500 text-white p-4 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6">
        <a href="{{ route('google.login') }}" class="flex items-center justify-center w-full bg-white text-gray-700 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-100">
            <img src="https://www.google.com/favicon.ico" alt="Google" class="w-5 h-5 mr-2">
            Continue with Google
        </a>
    </div>

    <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-gray-800 text-gray-300">Or continue with email</span>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block mb-2">Email</label>
            <input type="email" id="email" name="email" class="form-input" required autofocus>
        </div>

        <div class="mb-4">
            <label for="password" class="block mb-2">Password</label>
            <input type="password" id="password" name="password" class="form-input" required>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="mr-2">
                <span>Remember Me</span>
            </label>
        </div>

        <div class="flex items-center justify-between mb-4">
            <button type="submit" class="btn-primary">
                Login
            </button>
            <a href="{{ route('password.request') }}" class="text-sm text-blue-300 hover:text-blue-200">
                Forgot Password?
            </a>
        </div>

        <div class="text-center">
            <p class="text-sm">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-300 hover:text-blue-200">Register</a>
            </p>
        </div>
    </form>
@endsection 