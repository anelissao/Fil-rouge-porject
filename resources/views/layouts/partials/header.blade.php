<header class="bg-highlight sticky top-0 z-50 shadow-md py-4">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <span class="text-2xl font-bold text-primary">Debriefing.com</span>
                </a>
            </div>

            <button class="lg:hidden flex flex-col space-y-1.5" id="mobileMenuToggle" aria-label="Toggle Navigation Menu">
                <span class="block w-6 h-0.5 bg-white"></span>
                <span class="block w-6 h-0.5 bg-white"></span>
                <span class="block w-6 h-0.5 bg-white"></span>
            </button>

            <nav class="hidden lg:flex" id="mainNavigation">
                <ul class="flex items-center space-x-6">
                    <!-- Navigation items shown to all users -->
                    <li>
                        <a href="{{ route('home') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('home') ? 'text-primary' : '' }}">Home</a>
                    </li>

                    <!-- Guest Navigation -->
                    @guest
                        <li>
                            <a href="{{ route('login') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('login') ? 'text-primary' : '' }}">Login</a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('register') ? 'text-primary' : '' }}">Register</a>
                        </li>
                    @endguest

                    <!-- Authenticated User Navigation -->
                    @auth
                        <!-- Student Navigation -->
                        @if(auth()->user()->isStudent())
                            <li>
                                <a href="{{ route('briefs.index') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('briefs.*') ? 'text-primary' : '' }}">Briefs</a>
                            </li>
                            <li>
                                <a href="{{ route('student.submissions.index') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('student.submissions.*') ? 'text-primary' : '' }}">My Submissions</a>
                            </li>
                            <li>
                                <a href="{{ route('student.evaluations.index') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('student.evaluations.*') ? 'text-primary' : '' }}">Evaluations</a>
                            </li>
                        @endif

                        <!-- Teacher Navigation -->
                        @if(auth()->user()->isTeacher())
                            <li>
                                <a href="{{ route('teacher.briefs.index') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('teacher.briefs.*') ? 'text-primary' : '' }}">Manage Briefs</a>
                            </li>
                            <li>
                                <a href="{{ route('teacher.submissions.index') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('teacher.submissions.*') ? 'text-primary' : '' }}">View Submissions</a>
                            </li>
                            <li>
                                <a href="{{ route('teacher.evaluations.index') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('teacher.evaluations.*') ? 'text-primary' : '' }}">Evaluations</a>
                            </li>
                            <li>
                                <a href="{{ route('teacher.results.index') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('teacher.results.*') ? 'text-primary' : '' }}">Results</a>
                            </li>
                        @endif

                        <!-- Admin Navigation -->
                        @if(auth()->user()->isAdmin())
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('admin.*') ? 'text-primary' : '' }}">Admin Dashboard</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.index') }}" class="font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('admin.users.*') ? 'text-primary' : '' }}">Manage Users</a>
                            </li>
                        @endif

                        <!-- User Dropdown -->
                        <li class="relative group">
                            <a href="#" class="flex items-center font-medium text-white hover:text-primary transition-colors" id="userDropdown">
                                {{ auth()->user()->username }}
                                <svg class="ml-1 w-4 h-4 transition-transform group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <ul class="absolute right-0 mt-2 py-2 w-48 bg-highlight rounded-lg shadow-lg z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible hover:opacity-100 hover:visible transition-all duration-300">
                                <li>
                                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-white hover:bg-gray-750 hover:text-primary transition-colors">Profile</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-white hover:bg-gray-750 hover:text-primary transition-colors">Logout</button>
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

<!-- Mobile Menu (Hidden by default) -->
<div id="mobileMenu" class="fixed inset-0 bg-background bg-opacity-95 z-50 transform translate-x-full transition-transform duration-300 lg:hidden">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-8">
            <div>
                <span class="text-2xl font-bold text-primary">Debriefing.com</span>
            </div>
            <button id="mobileMenuClose" class="text-white hover:text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <nav>
            <ul class="space-y-4">
                <!-- Navigation items shown to all users -->
                <li>
                    <a href="{{ route('home') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('home') ? 'text-primary' : '' }}">Home</a>
                </li>

                <!-- Guest Navigation -->
                @guest
                    <li>
                        <a href="{{ route('login') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('login') ? 'text-primary' : '' }}">Login</a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('register') ? 'text-primary' : '' }}">Register</a>
                    </li>
                @endguest

                <!-- Authenticated User Navigation -->
                @auth
                    <!-- Student Navigation -->
                    @if(auth()->user()->isStudent())
                        <li>
                            <a href="{{ route('briefs.index') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('briefs.*') ? 'text-primary' : '' }}">Briefs</a>
                        </li>
                        <li>
                            <a href="{{ route('student.submissions.index') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('student.submissions.*') ? 'text-primary' : '' }}">My Submissions</a>
                        </li>
                        <li>
                            <a href="{{ route('student.evaluations.index') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('student.evaluations.*') ? 'text-primary' : '' }}">Evaluations</a>
                        </li>
                    @endif

                    <!-- Teacher Navigation -->
                    @if(auth()->user()->isTeacher())
                        <li>
                            <a href="{{ route('teacher.briefs.index') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('teacher.briefs.*') ? 'text-primary' : '' }}">Manage Briefs</a>
                        </li>
                        <li>
                            <a href="{{ route('teacher.submissions.index') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('teacher.submissions.*') ? 'text-primary' : '' }}">View Submissions</a>
                        </li>
                        <li>
                            <a href="{{ route('teacher.evaluations.index') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('teacher.evaluations.*') ? 'text-primary' : '' }}">Evaluations</a>
                        </li>
                        <li>
                            <a href="{{ route('teacher.results.index') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('teacher.results.*') ? 'text-primary' : '' }}">Results</a>
                        </li>
                    @endif

                    <!-- Admin Navigation -->
                    @if(auth()->user()->isAdmin())
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('admin.*') ? 'text-primary' : '' }}">Admin Dashboard</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors {{ request()->routeIs('admin.users.*') ? 'text-primary' : '' }}">Manage Users</a>
                        </li>
                    @endif

                    <!-- User Profile Link -->
                    <li class="pt-4 mt-4 border-t border-gray-700">
                        <a href="{{ route('profile.show') }}" class="block text-lg font-medium text-white hover:text-primary transition-colors">Profile</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left text-lg font-medium text-white hover:text-primary transition-colors">Logout</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </nav>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenuClose = document.getElementById('mobileMenuClose');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuToggle && mobileMenu && mobileMenuClose) {
            mobileMenuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('translate-x-full');
            });
            
            mobileMenuClose.addEventListener('click', function() {
                mobileMenu.classList.add('translate-x-full');
            });
        }
    });
</script> 