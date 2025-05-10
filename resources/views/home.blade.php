@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-gray-900 overflow-hidden">
        <div class="absolute inset-0">
            <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 opacity-80 absolute inset-0"></div>
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center mix-blend-overlay"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                    Welcome to Debriefing.com
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-10 max-w-3xl mx-auto">
                    A collaborative learning platform where students and teachers connect, share, and grow together
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    @guest
                        <a href="{{ route('login') }}" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 shadow-lg hover:shadow-xl">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-8 py-3 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 shadow-lg hover:shadow-xl border border-gray-700">
                            Create Account
                        </a>
                    @else
                        <a href="{{ auth()->user()->isTeacher() ? route('teacher.briefs.create') : route('briefs.index') }}" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 shadow-lg hover:shadow-xl">
                            {{ auth()->user()->isTeacher() ? 'Create Brief' : 'View Briefs' }}
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-gray-900 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white mb-4">How Debriefing.com Works</h2>
                <div class="h-1 w-24 bg-blue-600 mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-800 rounded-xl p-6 transition duration-300 hover:transform hover:-translate-y-2 hover:shadow-xl">
                    <div class="w-16 h-16 bg-blue-600/20 rounded-lg flex items-center justify-center mb-6 text-blue-500">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Briefs Creation</h3>
                    <p class="text-gray-300">Teachers create comprehensive project briefs with detailed criteria and tasks for students.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-gray-800 rounded-xl p-6 transition duration-300 hover:transform hover:-translate-y-2 hover:shadow-xl">
                    <div class="w-16 h-16 bg-green-600/20 rounded-lg flex items-center justify-center mb-6 text-green-500">
                        <i class="fas fa-upload text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Student Submissions</h3>
                    <p class="text-gray-300">Students submit their work before deadlines with descriptions and attachments.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-gray-800 rounded-xl p-6 transition duration-300 hover:transform hover:-translate-y-2 hover:shadow-xl">
                    <div class="w-16 h-16 bg-purple-600/20 rounded-lg flex items-center justify-center mb-6 text-purple-500">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Peer Evaluations</h3>
                    <p class="text-gray-300">Students evaluate each other's work based on predefined criteria set by teachers.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-gray-800 rounded-xl p-6 transition duration-300 hover:transform hover:-translate-y-2 hover:shadow-xl">
                    <div class="w-16 h-16 bg-yellow-600/20 rounded-lg flex items-center justify-center mb-6 text-yellow-500">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Feedback & Growth</h3>
                    <p class="text-gray-300">Receive detailed feedback to improve your skills and understanding over time.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-700 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-white mb-6">Ready to get started?</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Join Debriefing.com today and experience a new way of collaborative learning that benefits both students and teachers.
            </p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-white hover:bg-gray-100 text-blue-800 font-medium rounded-lg transition duration-300 shadow-lg hover:shadow-xl">
                Create Your Free Account
            </a>
        </div>
    </div>
@endsection

@section('styles')
<style>
    /* Hero Section Styles */
    .hero-section {
        background-color: var(--highlight-color);
        padding: 4rem 0;
        text-align: center;
        border-radius: 0.5rem;
        margin-bottom: 3rem;
    }

    .hero-title {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--primary-color);
    }

    .hero-subtitle {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        color: var(--secondary-color);
    }

    .hero-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    /* Features Section Styles */
    .features-section {
        padding: 3rem 0;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .feature-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: transform 0.3s;
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .feature-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .feature-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--secondary-color);
    }

    .feature-desc {
        color: var(--accent-color);
    }

    /* CTA Section Styles */
    .cta-section {
        background-color: var(--highlight-color);
        padding: 4rem 0;
        text-align: center;
        border-radius: 0.5rem;
        margin-top: 3rem;
    }

    .cta-title {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--primary-color);
    }

    .cta-desc {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        color: var(--secondary-color);
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .hero-buttons .btn {
            width: 100%;
            max-width: 300px;
            margin-bottom: 1rem;
        }
        
        .features-grid {
            grid-template-columns: 1fr;
        }
        
        .cta-title {
            font-size: 1.75rem;
        }
        
        .cta-desc {
            font-size: 1rem;
        }
    }
</style>
@endsection 