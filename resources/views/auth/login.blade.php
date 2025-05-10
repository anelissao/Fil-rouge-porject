@extends('layouts.auth')

@section('title', 'Login - Debriefing.com')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center text-white">Sign In to Debriefing</h2>

    @if ($errors->any())
        <div class="bg-red-500/75 text-white p-4 rounded-lg mb-6">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6">
        <a href="{{ route('google.login') }}" class="flex items-center justify-center w-full bg-white hover:bg-gray-100 text-gray-800 font-medium py-3 px-4 rounded-lg transition duration-200 shadow-sm">
            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
            </svg>
            Continue with Google
        </a>
    </div>

    <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-600"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-3 bg-gray-800 text-gray-400">Or continue with email</span>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-5">
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required autofocus>
        </div>

        <div class="mb-5">
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
            <input type="password" id="password" name="password" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
        </div>

        <div class="flex items-center mb-5">
            <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-600 rounded bg-gray-700">
            <label for="remember" class="ml-2 block text-sm text-gray-300">Remember me</label>
        </div>

        <div class="flex items-center justify-between mb-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Sign In
            </button>
            <a href="{{ route('password.request') }}" class="text-sm text-blue-400 hover:text-blue-300 transition duration-200">
                Forgot Password?
            </a>
        </div>

        <div class="text-center text-gray-400">
            <p class="text-sm">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-medium transition duration-200">Create Account</a>
            </p>
        </div>
    </form>
@endsection 