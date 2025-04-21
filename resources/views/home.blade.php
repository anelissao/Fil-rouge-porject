@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Welcome to Debriefing.com</h1>
            <p class="hero-subtitle">A collaborative learning platform for students and teachers</p>
            <div class="hero-buttons">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-outline">Register</a>
                @else
                    <a href="{{ auth()->user()->isTeacher() ? route('teacher.briefs.create') : route('briefs.index') }}" class="btn btn-primary">
                        {{ auth()->user()->isTeacher() ? 'Create Brief' : 'View Briefs' }}
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <div class="features-section">
        <h2 class="section-title">How Debriefing.com Works</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3 class="feature-title">Briefs Creation</h3>
                <p class="feature-desc">Teachers create comprehensive project briefs with detailed criteria and tasks.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-upload"></i>
                </div>
                <h3 class="feature-title">Student Submissions</h3>
                <p class="feature-desc">Students submit their work before deadlines with descriptions and attachments.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="feature-title">Peer Evaluations</h3>
                <p class="feature-desc">Students evaluate each other's work based on predefined criteria.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Feedback & Growth</h3>
                <p class="feature-desc">Receive detailed feedback to improve your skills and understanding.</p>
            </div>
        </div>
    </div>

    <div class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">Ready to get started?</h2>
            <p class="cta-desc">Join Debriefing.com today and experience a new way of collaborative learning.</p>
            <a href="{{ route('register') }}" class="btn btn-primary">Sign Up Now</a>
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