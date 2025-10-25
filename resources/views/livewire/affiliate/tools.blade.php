<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Marketing Tools</h1>
        <p class="text-gray-600">Promotional materials and resources for your referrals</p>
    </div>

    <!-- Tool Selector -->
    <div class="bg-white rounded-xl shadow-lg mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <button wire:click="setTool('links')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $selectedTool === 'links' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-link mr-2"></i> Referral Links
                </button>
                <button wire:click="setTool('banners')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $selectedTool === 'banners' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-image mr-2"></i> Banner Images
                </button>
                <button wire:click="setTool('social')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $selectedTool === 'social' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-share-alt mr-2"></i> Social Media
                </button>
                <button wire:click="setTool('email')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $selectedTool === 'email' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-envelope mr-2"></i> Email Templates
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($selectedTool === 'links')
                <!-- Referral Links Section -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Referral Links</h3>
                        
                        <!-- Main Referral Link -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Main Referral Link</label>
                            <div class="flex items-center space-x-3">
                                <input type="text" value="{{ $affiliate->referral_link }}" readonly 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-white text-sm">
                                <button wire:click="copyToClipboard('{{ $affiliate->referral_link }}', 'main')" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    @if($copiedText === 'main')
                                        <i class="fas fa-check"></i> Copied!
                                    @else
                                        <i class="fas fa-copy"></i> Copy
                                    @endif
                                </button>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">QR Code</label>
                            <div class="flex items-center space-x-4">
                                <img src="{{ $marketingAssets['qr_code_url'] }}" alt="QR Code" class="w-32 h-32 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600 mb-2">Share this QR code for easy mobile access</p>
                                    <a href="{{ $marketingAssets['qr_code_url'] }}" download="referral-qr-code.png" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <i class="fas fa-download mr-2"></i> Download QR Code
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($selectedTool === 'banners')
                <!-- Banner Images Section -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Promotional Banners</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Large Banner -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Large Banner (728x90)</h4>
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-lg mb-3">
                                <div class="text-center">
                                    <h3 class="text-xl font-bold mb-2">Learn to Code with BootKode</h3>
                                    <p class="text-sm">Join thousands of students • Use code: {{ $affiliate->referral_code }}</p>
                                </div>
                            </div>
                            <button wire:click="copyToClipboard('{{ $marketingAssets['banner_images']['large'] }}', 'large-banner')" 
                                    class="w-full px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                                @if($copiedText === 'large-banner') Copied! @else Copy Banner URL @endif
                            </button>
                        </div>

                        <!-- Medium Banner -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Medium Banner (300x250)</h4>
                            <div class="bg-gradient-to-br from-green-500 to-blue-600 text-white p-4 rounded-lg mb-3">
                                <h3 class="text-lg font-bold mb-1">BootKode</h3>
                                <p class="text-xs">Master Coding Skills</p>
                                <p class="text-xs mt-2">Code: {{ $affiliate->referral_code }}</p>
                            </div>
                            <button wire:click="copyToClipboard('{{ $marketingAssets['banner_images']['medium'] }}', 'medium-banner')" 
                                    class="w-full px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                                @if($copiedText === 'medium-banner') Copied! @else Copy Banner URL @endif
                            </button>
                        </div>
                    </div>
                </div>

            @elseif($selectedTool === 'social')
                <!-- Social Media Section -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Media Sharing</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ $marketingAssets['social_media']['facebook'] }}" target="_blank" 
                           class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fab fa-facebook-f text-2xl text-blue-600 mb-2"></i>
                            <span class="text-sm font-medium text-gray-900">Facebook</span>
                        </a>
                        
                        <a href="{{ $marketingAssets['social_media']['twitter'] }}" target="_blank" 
                           class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fab fa-twitter text-2xl text-blue-400 mb-2"></i>
                            <span class="text-sm font-medium text-gray-900">Twitter</span>
                        </a>
                        
                        <a href="{{ $marketingAssets['social_media']['linkedin'] }}" target="_blank" 
                           class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fab fa-linkedin-in text-2xl text-blue-700 mb-2"></i>
                            <span class="text-sm font-medium text-gray-900">LinkedIn</span>
                        </a>
                        
                        <a href="{{ $marketingAssets['social_media']['whatsapp'] }}" target="_blank" 
                           class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fab fa-whatsapp text-2xl text-green-500 mb-2"></i>
                            <span class="text-sm font-medium text-gray-900">WhatsApp</span>
                        </a>
                    </div>

                    <!-- Pre-written Social Posts -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3">Pre-written Posts</h4>
                        <div class="space-y-3">
                            @php
                                $socialPosts = [
                                    "🚀 Ready to level up your coding skills? Check out BootKode - the best platform for learning programming! Use my link: {$affiliate->referral_link}",
                                    "💻 Transform your career with coding! I recommend BootKode for quality courses and hands-on projects. Start here: {$affiliate->referral_link}",
                                    "📚 Learning to code has never been easier! BootKode offers expert-led courses for all levels. Join me: {$affiliate->referral_link}"
                                ];
                            @endphp
                            
                            @foreach($socialPosts as $index => $post)
                                <div class="bg-white rounded border p-3">
                                    <p class="text-sm text-gray-700 mb-2">{{ $post }}</p>
                                    <button wire:click="copyToClipboard('{{ $post }}', 'social-{{ $index }}')" 
                                            class="text-xs px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        @if($copiedText === "social-{$index}") Copied! @else Copy Post @endif
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            @elseif($selectedTool === 'email')
                <!-- Email Templates Section -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Email Templates</h3>
                    
                    <!-- Custom Message Input -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customize Your Message (Optional)</label>
                        <textarea wire:model="customMessage" 
                                  placeholder="Add a personal touch to your email..." 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" 
                                  rows="3"></textarea>
                        <button wire:click="generateCustomEmail" 
                                class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                            Generate Email
                        </button>
                    </div>

                    <!-- Default Email Template -->
                    <div class="bg-white border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3">Default Email Template</h4>
                        <div class="bg-gray-50 rounded p-4 text-sm">
                            <pre class="whitespace-pre-wrap text-gray-700">{{ $marketingAssets['email_template'] }}</pre>
                        </div>
                        <div class="mt-3 flex space-x-3">
                            <button wire:click="copyToClipboard('{{ addslashes($marketingAssets['email_template']) }}', 'email-template')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                @if($copiedText === 'email-template') Copied! @else Copy Template @endif
                            </button>
                            <a href="mailto:?subject=Join BootKode&body={{ urlencode($marketingAssets['email_template']) }}" 
                               class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                Open in Email Client
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Performance Tips -->
    <div class="bg-blue-50 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">
            <i class="fas fa-lightbulb mr-2"></i> Marketing Tips
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="bg-white rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">Best Practices</h4>
                <ul class="text-gray-700 space-y-1">
                    <li>• Share your personal coding journey</li>
                    <li>• Highlight specific course benefits</li>
                    <li>• Use authentic, personal recommendations</li>
                    <li>• Target relevant audiences</li>
                </ul>
            </div>
            <div class="bg-white rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">High-Converting Strategies</h4>
                <ul class="text-gray-700 space-y-1">
                    <li>• Share success stories and testimonials</li>
                    <li>• Create comparison content</li>
                    <li>• Offer additional value (bonuses)</li>
                    <li>• Follow up with prospects</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('livewire:init', function() {
        Livewire.on('clipboard-copy', (event) => {
            navigator.clipboard.writeText(event.text).then(function() {
                // Success notification would go here
            });
        });

        Livewire.on('custom-email-generated', (event) => {
            // Handle custom email generation
            navigator.clipboard.writeText(event.template);
        });
    });
</script>

</div>