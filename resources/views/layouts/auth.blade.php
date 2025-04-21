<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Debriefing.com')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1E90FF;
            --secondary-color: #FFFFFF;
            --background-color: #1C2526;
            --accent-color: #E5E7EB;
            --highlight-color: #0A3D62;
        }
        body {
            background-color: var(--background-color);
            color: var(--secondary-color);
        }
        .auth-container {
            background-color: var(--highlight-color);
            border-radius: 0.5rem;
            padding: 2rem;
            max-width: 400px;
            margin: 2rem auto;
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: var(--highlight-color);
        }
        .form-input {
            background-color: var(--accent-color);
            border: 1px solid var(--primary-color);
            border-radius: 0.25rem;
            padding: 0.5rem;
            width: 100%;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center">
        <div class="auth-container">
            @yield('content')
        </div>
    </div>
</body>
</html> 