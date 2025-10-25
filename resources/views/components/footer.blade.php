<footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white py-6">
    <div class="max-w-100 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-10">

            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <i class="fas fa-code fa-lg text-white"></i>
                    </div>
                    <span
                        class="ml-3 text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-500">
                        BootKode
                    </span>
                </div>
                <p class="text-gray-300 text-sm max-w-sm">
                    Empowering Africa's youth with digital skills, mentorship, and career opportunities through
                    accessible tech education.
                </p>
                <div class="flex space-x-5">
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <span class="sr-only">Facebook</span>
                        <i class="fab fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <span class="sr-only">Twitter</span>
                        <i class="fab fa-twitter fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <span class="sr-only">LinkedIn</span>
                        <i class="fab fa-linkedin-in fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <span class="sr-only">Instagram</span>
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <span class="sr-only">YouTube</span>
                        <i class="fab fa-youtube fa-lg"></i>
                    </a>
                </div>
                
            </div>


            {{-- <div>
                <h4 class="text-white font-medium mb-4">Categories</h4>
                <ul class="space-y-2 text-sm">
                    @foreach($popularCategories->take(5) as $category)
                        <li>
                            <button wire:click="viewCategory({{ $category->id }})" 
                                    class="hover:text-white transition-colors">
                                {{ $category->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div> --}}


            <div class="space-y-4">
                <h3 class="text-lg font-bold text-white uppercase tracking-wide">
                    Explore
                </h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-white transition-colors">About
                            Us</a></li>
                    <li><a href="{{ route('contact') }}"
                            class="text-gray-300 hover:text-white transition-colors">Contact Us</a></li>
                    <li><a href="{{ route('statistics') }}"
                            class="text-gray-300 hover:text-white transition-colors">Statistics</a></li>
                    <li><a href="{{ route('guideline') }}"
                            class="text-gray-300 hover:text-white transition-colors">Guideline</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-bold text-white uppercase tracking-wide">
                    Blog
                </h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('blog.index') }}"
                            class="text-gray-300 hover:text-white transition-colors">home</a></li>
                    @forelse($topCategories as $category)
                        <li>
                            <a href="{{ route('blog.category', $category) }}"
                                class="text-gray-300 hover:text-white transition-colors flex items-center">
                                <span>{{ $category->name }}</span>
                                <span class="ml-2 text-xs bg-blue-600 text-white px-2 py-0.5 rounded-full">
                                    {{ $category->published_posts_count }}
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="text-gray-400 text-sm">No categories available</li>
                    @endforelse
                </ul>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-white uppercase tracking-wide">
                        Stay Updated
                    </h3>
                    <p class="text-gray-300 text-sm">Subscribe to our newsletter for the latest updates.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST"
                        class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input type="email" name="email" placeholder="Your email address"
                            class="px-4 py-3 rounded-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent flex-grow text-sm"
                            required value="{{ old('email') }}">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg transition-colors flex items-center justify-center text-sm">
                            <i class="fas fa-paper-plane mr-2"></i> Subscribe
                        </button>
                    </form>

                    @if(session('success'))
                        <div class="text-sm p-3 rounded-lg bg-green-100 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="text-sm p-3 rounded-lg bg-red-100 text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('errors') && session('errors')->any())
                        <div class="text-sm p-3 rounded-lg bg-red-100 text-red-800">
                            {{ session('errors')->first() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <hr class="border-gray-700 my-6">

        <div class="flex flex-col md:flex-row justify-between items-center text-center md:text-left text-sm">
            <p class="text-gray-400 mb-4 md:mb-0">
                &copy; {{ date('Y') }} BootKode. All rights reserved. BETA
            </p>
            <div class="flex flex-wrap justify-center md:justify-end space-x-6">
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>