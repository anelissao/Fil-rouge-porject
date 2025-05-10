<footer class="bg-highlight py-12 mt-auto">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
                <h3 class="text-xl font-semibold text-primary mb-4 pb-2 border-b-2 border-primary inline-block">Debriefing.com</h3>
                <p class="text-white">A collaborative learning platform for students and teachers to work together on projects and provide peer evaluations.</p>
            </div>
            
            <div>
                <h3 class="text-xl font-semibold text-primary mb-4 pb-2 border-b-2 border-primary inline-block">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-accent hover:text-primary transition-colors">Home</a></li>
                    <li><a href="#" class="text-accent hover:text-primary transition-colors">About Us</a></li>
                    <li><a href="#" class="text-accent hover:text-primary transition-colors">Contact</a></li>
                    <li><a href="#" class="text-accent hover:text-primary transition-colors">FAQ</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-xl font-semibold text-primary mb-4 pb-2 border-b-2 border-primary inline-block">Contact</h3>
                <ul class="space-y-2">
                    <li class="flex items-center text-accent">
                        <i class="fas fa-envelope mr-2 text-primary"></i>
                        info@debriefing.com
                    </li>
                    <li class="flex items-center text-accent">
                        <i class="fas fa-phone mr-2 text-primary"></i>
                        +212 5XX-XXXXXX
                    </li>
                    <li class="flex items-center text-accent">
                        <i class="fas fa-map-marker-alt mr-2 text-primary"></i>
                        Marrakech, Morocco
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-700 pt-6 flex flex-col md:flex-row md:justify-between md:items-center">
            <div class="text-accent text-sm mb-4 md:mb-0">
                &copy; {{ date('Y') }} Debriefing.com. All rights reserved.
            </div>
            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-6">
                <a href="#" class="text-accent hover:text-primary transition-colors text-sm">Privacy Policy</a>
                <a href="#" class="text-accent hover:text-primary transition-colors text-sm">Terms of Service</a>
            </div>
        </div>
    </div>
</footer> 