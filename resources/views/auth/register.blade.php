@extends('layouts.auth')

@section('title', 'Register - Debriefing.com')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>

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
            <span class="px-2 bg-gray-800 text-gray-300">Or register with email</span>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-4">
            <label for="username" class="block mb-2">Username</label>
            <input type="text" id="username" name="username" class="form-input" required autofocus>
        </div>

        <div class="mb-4">
            <label for="email" class="block mb-2">Email</label>
            <input type="email" id="email" name="email" class="form-input" required>
        </div>

        <div class="mb-4">
            <label for="first_name" class="block mb-2">First Name</label>
            <input type="text" id="first_name" name="first_name" class="form-input" required>
        </div>

        <div class="mb-4">
            <label for="last_name" class="block mb-2">Last Name</label>
            <input type="text" id="last_name" name="last_name" class="form-input" required>
        </div>

        <div class="mb-4">
            <label for="password" class="block mb-2">Password</label>
            <input type="password" id="password" name="password" class="form-input" required>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block mb-2">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
        </div>

        <div class="mb-4">
            <label for="role" class="block mb-2">Role</label>
            <select id="role" name="role" class="form-input" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>

        <div class="flex items-center justify-between mb-4">
            <button type="submit" class="btn-primary">
                Register
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm">
                Already have an account?
                <a href="{{ route('login') }}" class="text-blue-300 hover:text-blue-200">Login</a>
            </p>
        </div>
    </form>
@endsection 