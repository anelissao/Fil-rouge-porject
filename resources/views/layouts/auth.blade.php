<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Debriefing.com')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <div class="text-center">
                    <a href="{{ route('home') }}" class="inline-block">
                        <div class="text-4xl font-bold text-blue-500 mb-2">Debriefing</div>
                        <p class="text-gray-400 text-sm">Collaborative Learning Platform</p>
                    </a>
                </div>
            </div>
            
            <!-- Auth Card -->
            <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
                <div class="px-6 py-8 sm:px-10">
                    @yield('content')
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-900/50 text-center">
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} Debriefing.com. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Add any custom JavaScript here
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.classList.add('opacity-0');
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                });
            }, 5000);
        });
    </script>
</body>
</html> 