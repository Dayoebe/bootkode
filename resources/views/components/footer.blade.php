<footer class="bg-gray-900 text-white py-12 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <!-- Brand Column -->
            <div class="space-y-4">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                    <span class="ml-2 text-xl font-bold text-white">BootKode</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Empowering Africa's youth with digital skills, mentorship, and careers through accessible tech education.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition duration-300 transform hover:scale-110">
                        <span class="sr-only">Facebook</span>
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition duration-300 transform hover:scale-110">
                        <span class="sr-only">Twitter</span>
                        <i class="fab fa-twitter text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition duration-300 transform hover:scale-110">
                        <span class="sr-only">LinkedIn</span>
                        <i class="fab fa-linkedin-in text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition duration-300 transform hover:scale-110">
                        <span class="sr-only">Instagram</span>
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition duration-300 transform hover:scale-110">
                        <span class="sr-only">YouTube</span>
                        <i class="fab fa-youtube text-lg"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-blue-400 uppercase tracking-wider">Explore</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> About Us
                    </a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> Contact Us
                    </a></li>
                    <li><a href="{{ route('statistics') }}" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> Statistics
                    </a></li>
                    <li><a href="{{ route('guideline') }}" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> Guideline
                    </a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> Certification
                    </a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> Success Stories
                    </a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-blue-400 uppercase tracking-wider">Resources</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> Blog
                    </a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> Documentation
                    </a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> Community
                    </a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300 flex items-center">
                        <i class="fas fa-chevron-right text-xs mr-2 text-blue-400"></i> Help Center
                    </a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-blue-400 uppercase tracking-wider">Contact</h3>
                <address class="not-italic text-gray-400 text-sm space-y-3">
                    <div class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-3 text-blue-400"></i>
                        <a href="mailto:oyetoke.ebenezer@gmail.com" class="hover:text-white transition duration-300">oyetoke.ebenezer@gmail.com</a>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-phone mt-1 mr-3 text-blue-400"></i>
                        <a href="tel:+2349030036438" class="hover:text-white transition duration-300">+234 903 003 6438</a>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-3 text-blue-400"></i>
                        <span>Awka, Anambra State, Nigeria</span>
                    </div>
                </address>

                <!-- Newsletter Signup -->
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-white mb-2">Subscribe to our newsletter</h4>
                    <div class="flex">
                        <input type="email" placeholder="Your email" class="px-3 py-2 text-sm text-gray-900 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-md text-sm font-medium transition duration-300">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright and Legal -->
        <div class="border-t border-gray-800 mt-12 pt-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="text-center md:text-left">
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} BootKode. All rights reserved.
                        <span class="block md:inline-block mt-1 md:mt-0 md:ml-2">Proprietary & Confidential - Wireless Computer Services</span>
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <ul class="flex justify-center md:justify-end space-x-6">
                        <li><a href="#" class="text-gray-500 hover:text-gray-400 text-xs transition duration-300">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-gray-400 text-xs transition duration-300">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-gray-400 text-xs transition duration-300">Cookie Policy</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-gray-400 text-xs transition duration-300">Sitemap</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>