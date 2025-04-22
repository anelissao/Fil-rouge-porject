<header class="site-header">
    <div class="container">
        <div class="header-wrapper">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <span class="logo-text">Debriefing.com</span>
                </a>
            </div>

            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Navigation Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="main-navigation" id="mainNavigation">
                <ul class="nav-list">
                    <!-- Navigation items shown to all users -->
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                    </li>

                    <!-- Guest Navigation -->
                    @guest
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}">Register</a>
                        </li>
                    @endguest

                    <!-- Authenticated User Navigation -->
                    @auth
                        <!-- Student Navigation -->
                        @if(auth()->user()->isStudent())
                            <li class="nav-item">
                                <a href="{{ route('briefs.index') }}" class="nav-link {{ request()->routeIs('briefs.*') ? 'active' : '' }}">Briefs</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('student.submissions.index') }}" class="nav-link {{ request()->routeIs('student.submissions.*') ? 'active' : '' }}">My Submissions</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('student.evaluations.index') }}" class="nav-link {{ request()->routeIs('student.evaluations.*') ? 'active' : '' }}">Evaluations</a>
                            </li>
                        @endif

                        <!-- Teacher Navigation -->
                        @if(auth()->user()->isTeacher())
                            <li class="nav-item">
                                <a href="{{ route('teacher.briefs.index') }}" class="nav-link {{ request()->routeIs('teacher.briefs.*') ? 'active' : '' }}">Manage Briefs</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('teacher.submissions.index') }}" class="nav-link {{ request()->routeIs('teacher.submissions.*') ? 'active' : '' }}">View Submissions</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('teacher.evaluations.index') }}" class="nav-link {{ request()->routeIs('teacher.evaluations.*') ? 'active' : '' }}">Evaluations</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('teacher.results.index') }}" class="nav-link {{ request()->routeIs('teacher.results.*') ? 'active' : '' }}">Results</a>
                            </li>
                        @endif

                        <!-- Admin Navigation -->
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Manage Users</a>
                            </li>
                        @endif

                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="userDropdown">
                                {{ auth()->user()->username }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('profile.show') }}" class="dropdown-item">Profile</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </nav>
        </div>
    </div>
</header>

<style>
    .site-header {
        background-color: var(--highlight-color);
        padding: 1rem 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo a {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: var(--secondary-color);
    }

    .logo-text {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .main-navigation {
        display: flex;
    }

    .nav-list {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        align-items: center;
    }

    .nav-item {
        margin-left: 1.5rem;
        position: relative;
    }

    .nav-link {
        color: var(--secondary-color);
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 0;
        transition: color 0.3s;
        display: block;
    }

    .nav-link:hover, .nav-link.active {
        color: var(--primary-color);
    }

    .dropdown-toggle {
        display: flex;
        align-items: center;
    }

    .dropdown-toggle::after {
        content: '';
        display: inline-block;
        width: 0.4rem;
        height: 0.4rem;
        margin-left: 0.5rem;
        border-right: 2px solid var(--secondary-color);
        border-bottom: 2px solid var(--secondary-color);
        transform: rotate(45deg);
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        top: 100%;
        background-color: var(--highlight-color);
        border-radius: 0.25rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        min-width: 180px;
        padding: 0.5rem 0;
        display: none;
        z-index: 10;
    }

    .nav-item.dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-item {
        display: block;
        padding: 0.5rem 1rem;
        color: var(--secondary-color);
        text-decoration: none;
        transition: background-color 0.3s;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .dropdown-item:hover {
        background-color: var(--primary-color);
        color: var(--secondary-color);
    }

    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
    }

    .mobile-menu-toggle span {
        display: block;
        width: 25px;
        height: 3px;
        background-color: var(--secondary-color);
        margin: 5px 0;
        transition: transform 0.3s, opacity 0.3s;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .header-wrapper {
            flex-wrap: wrap;
        }

        .mobile-menu-toggle {
            display: block;
            order: 3;
        }

        .main-navigation {
            flex-basis: 100%;
            order: 4;
            display: none;
        }

        .main-navigation.active {
            display: block;
            margin-top: 1rem;
        }

        .nav-list {
            flex-direction: column;
            align-items: flex-start;
        }

        .nav-item {
            margin-left: 0;
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .dropdown-menu {
            position: static;
            box-shadow: none;
            padding-left: 1rem;
            display: none;
        }

        .nav-item.dropdown.active .dropdown-menu {
            display: block;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mainNavigation = document.getElementById('mainNavigation');
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        if (mobileMenuToggle && mainNavigation) {
            mobileMenuToggle.addEventListener('click', function() {
                mainNavigation.classList.toggle('active');
                this.classList.toggle('active');
            });
        }

        // Handle dropdown toggles on mobile
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    this.closest('.nav-item').classList.toggle('active');
                }
            });
        });
    });
</script> 