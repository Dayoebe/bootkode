<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <x-ui.flash :messages="$feedback" />
    <!-- Header -->
    <div class="mb-8">
        <h1 class=" font-bold text-content bk-page-title">Marketing Tools</h1>
        <p class="text-content-muted">Promotional materials and resources for your referrals</p>
    </div>

    @if(!$affiliate->isActive())
        <x-ui.empty-state title="Your affiliate account is not active" description="Staff activation is required before promotional links and banners are available." />
    @else
    <!-- Tool Selector -->
    <div class="bg-surface rounded-lg shadow-sm mb-8">
        <div class="border-b border-line">
            <nav class="-mb-px flex max-w-full gap-6 overflow-x-auto px-6">
                <button wire:click="setTool('links')"
                        class="min-h-11 shrink-0 py-4 px-1 border-b-2 font-medium text-sm {{ $selectedTool === 'links' ? 'border-blue-500 text-blue-600' : 'border-transparent text-content-muted hover:text-content hover:border-gray-300' }}">
                    <i class="fas fa-link mr-2"></i> Referral Links
                </button>
                <button wire:click="setTool('banners')"
                        class="min-h-11 shrink-0 py-4 px-1 border-b-2 font-medium text-sm {{ $selectedTool === 'banners' ? 'border-blue-500 text-blue-600' : 'border-transparent text-content-muted hover:text-content hover:border-gray-300' }}">
                    <i class="fas fa-image mr-2"></i> Banner Images
                </button>
                <button wire:click="setTool('social')"
                        class="min-h-11 shrink-0 py-4 px-1 border-b-2 font-medium text-sm {{ $selectedTool === 'social' ? 'border-blue-500 text-blue-600' : 'border-transparent text-content-muted hover:text-content hover:border-gray-300' }}">
                    <i class="fas fa-share-alt mr-2"></i> Social Media
                </button>
                <button wire:click="setTool('email')"
                        class="min-h-11 shrink-0 py-4 px-1 border-b-2 font-medium text-sm {{ $selectedTool === 'email' ? 'border-blue-500 text-blue-600' : 'border-transparent text-content-muted hover:text-content hover:border-gray-300' }}">
                    <i class="fas fa-envelope mr-2"></i> Email Templates
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($selectedTool === 'links')
                <!-- Referral Links Section -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-content mb-4">Your Referral Links</h3>

                        <!-- Main Referral Link -->
                        <div class="bg-surface-muted rounded-lg p-4 mb-4">
                            <label class="block text-sm font-medium text-content mb-2">Main Referral Link</label>
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <input type="text" value="{{ $affiliate->referral_link }}" readonly
                                       class="min-w-0 flex-1 px-3 py-2 border border-line rounded-md bg-surface text-sm">
                                <button wire:click="copyToClipboard('{{ $affiliate->referral_link }}', 'main')"
                                        class="bk-button bk-button-primary">
                                    @if($copiedText === 'main')
                                        <i class="fas fa-check"></i> Copied!
                                    @else
                                        <i class="fas fa-copy"></i> Copy
                                    @endif
                                </button>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="bg-surface-muted rounded-lg p-4">
                            <label class="block text-sm font-medium text-content mb-2">QR Code</label>
                            <div class="flex items-center space-x-4">
                                <img src="{{ $marketingAssets['qr_code_url'] }}" alt="QR Code" class="w-32 h-32 border border-line rounded-lg">
                                <div>
                                    <p class="text-sm text-content-muted mb-2">Share this QR code for easy mobile access</p>
                                    <a href="{{ $marketingAssets['qr_code_url'] }}" download="referral-qr-code.png"
                                       class="inline-flex items-center px-3 py-2 border border-line shadow-sm text-sm font-medium rounded-md text-content bg-surface hover:bg-surface-muted">
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
                    <h3 class="text-lg font-semibold text-content mb-4">Promotional Banners</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Large Banner -->
                        <div class="bg-surface-muted rounded-lg p-4">
                            <h4 class="font-medium text-content mb-2">Large Banner (728x90)</h4>
                            <div class="bg-brand-ink   text-white p-6 rounded-lg mb-3">
                                <div class="text-center">
                                    <h3 class="text-xl font-bold mb-2">Learn to Code with BootKode</h3>
                                    <p class="text-sm">Learn. Build. Grow. • Referral: {{ $affiliate->referral_code }}</p>
                                </div>
                            </div>
                            <button wire:click="copyToClipboard('{{ $marketingAssets['banner_images']['large'] }}', 'large-banner')"
                                    class="w-full px-3 py-2 bg-action text-action-foreground rounded-md hover:bg-action transition-colors text-sm">
                                @if($copiedText === 'large-banner') Copied! @else Copy Banner URL @endif
                            </button>
                        </div>

                        <!-- Medium Banner -->
                        <div class="bg-surface-muted rounded-lg p-4">
                            <h4 class="font-medium text-content mb-2">Medium Banner (300x250)</h4>
                            <div class="bg-brand-ink   text-white p-4 rounded-lg mb-3">
                                <h3 class="text-lg font-bold mb-1">BootKode</h3>
                                <p class="text-xs">Master Coding Skills</p>
                                <p class="text-xs mt-2">Code: {{ $affiliate->referral_code }}</p>
                            </div>
                            <button wire:click="copyToClipboard('{{ $marketingAssets['banner_images']['medium'] }}', 'medium-banner')"
                                    class="w-full px-3 py-2 bg-action text-action-foreground rounded-md hover:bg-action transition-colors text-sm">
                                @if($copiedText === 'medium-banner') Copied! @else Copy Banner URL @endif
                            </button>
                        </div>
                    </div>
                </div>

            @elseif($selectedTool === 'social')
                <!-- Social Media Section -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-content mb-4">Social Media Sharing</h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ $marketingAssets['social_media']['facebook'] }}" target="_blank"
                           class="flex flex-col items-center p-4 bg-brand/10 rounded-lg hover:bg-brand/10 transition-colors">
                            <i class="fab fa-facebook-f text-2xl text-brand mb-2"></i>
                            <span class="text-sm font-medium text-content">Facebook</span>
                        </a>

                        <a href="{{ $marketingAssets['social_media']['twitter'] }}" target="_blank"
                           class="flex flex-col items-center p-4 bg-brand/10 rounded-lg hover:bg-brand/10 transition-colors">
                            <i class="fab fa-twitter text-2xl text-brand mb-2"></i>
                            <span class="text-sm font-medium text-content">Twitter</span>
                        </a>

                        <a href="{{ $marketingAssets['social_media']['linkedin'] }}" target="_blank"
                           class="flex flex-col items-center p-4 bg-brand/10 rounded-lg hover:bg-brand/10 transition-colors">
                            <i class="fab fa-linkedin-in text-2xl text-brand mb-2"></i>
                            <span class="text-sm font-medium text-content">LinkedIn</span>
                        </a>

                        <a href="{{ $marketingAssets['social_media']['whatsapp'] }}" target="_blank"
                           class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fab fa-whatsapp text-2xl text-green-500 mb-2"></i>
                            <span class="text-sm font-medium text-content">WhatsApp</span>
                        </a>
                    </div>

                    <!-- Pre-written Social Posts -->
                    <div class="bg-surface-muted rounded-lg p-4">
                        <h4 class="font-medium text-content mb-3">Pre-written Posts</h4>
                        <div class="space-y-3">
                            @php
                                $socialPosts = [
                                    "🚀 Ready to level up your coding skills? Check out BootKode - the best platform for learning programming! Use my link: {$affiliate->referral_link}",
                                    "💻 Transform your career with coding! I recommend BootKode for quality courses and hands-on projects. Start here: {$affiliate->referral_link}",
                                    "📚 Learning to code has never been easier! BootKode offers expert-led courses for all levels. Join me: {$affiliate->referral_link}"
                                ];
                            @endphp

                            @foreach($socialPosts as $index => $post)
                                <div class="bg-surface rounded border p-3">
                                    <p class="text-sm text-content mb-2">{{ $post }}</p>
                                    <button wire:click="copyToClipboard('{{ $post }}', 'social-{{ $index }}')"
                                            class="text-xs px-2 py-1 bg-action text-action-foreground rounded hover:bg-action">
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
                    <h3 class="text-lg font-semibold text-content mb-4">Email Templates</h3>

                    <!-- Custom Message Input -->
                    <div class="bg-surface-muted rounded-lg p-4">
                        <label class="block text-sm font-medium text-content mb-2">Customize Your Message (Optional)</label>
                        <textarea aria-label="Add a personal touch to your email" wire:model="customMessage"
                                  placeholder="Add a personal touch to your email..."
                                  class="w-full px-3 py-2 border border-line rounded-md text-sm"
                                  rows="3"></textarea>
                        <button wire:click="generateCustomEmail"
                                class="mt-2 px-4 py-2 bg-action text-action-foreground rounded-md hover:bg-action text-sm">
                            Generate Email
                        </button>
                    </div>

                    <!-- Default Email Template -->
                    <div class="bg-surface border rounded-lg p-4">
                        <h4 class="font-medium text-content mb-3">Default Email Template</h4>
                        <div class="bg-surface-muted rounded p-4 text-sm">
                            <pre class="whitespace-pre-wrap text-content">{{ $marketingAssets['email_template'] }}</pre>
                        </div>
                        <div class="mt-3 flex space-x-3">
                            <button wire:click="copyToClipboard(@js($marketingAssets['email_template']), 'email-template')"
                                    class="px-4 py-2 bg-action text-action-foreground rounded-md hover:bg-action text-sm">
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
    <div class="bg-brand/10 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-brand mb-4">
            <i class="fas fa-lightbulb mr-2"></i> Marketing Tips
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="bg-surface rounded-lg p-4">
                <h4 class="font-medium text-content mb-2">Best Practices</h4>
                <ul class="text-content space-y-1">
                    <li>• Share your personal coding journey</li>
                    <li>• Highlight specific course benefits</li>
                    <li>• Use authentic, personal recommendations</li>
                    <li>• Target relevant audiences</li>
                </ul>
            </div>
            <div class="bg-surface rounded-lg p-4">
                <h4 class="font-medium text-content mb-2">High-Converting Strategies</h4>
                <ul class="text-content space-y-1">
                    <li>• Share verified experiences with permission</li>
                    <li>• Create comparison content</li>
                    <li>• Offer additional value (bonuses)</li>
                    <li>• Follow up with prospects</li>
                </ul>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('clipboard-copy', async (event) => {
            try { await navigator.clipboard.writeText(event.text); $wire.clipboardResult(event.type, true); }
            catch { $wire.clipboardResult(event.type, false); }
        });
        $wire.on('custom-email-generated', async (event) => {
            try { await navigator.clipboard.writeText(event.template); $wire.clipboardResult('email-template', true); }
            catch { $wire.clipboardResult('email-template', false); }
        });
    </script>
    @endscript

    @endif
</div>
