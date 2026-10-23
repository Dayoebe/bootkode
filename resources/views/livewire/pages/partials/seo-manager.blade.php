{{-- SEO Manager Partial --}}
<div class="space-y-6">
    <!-- SEO Header -->
    <div class="bg-surface rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-xl font-semibold text-content">SEO Manager</h2>
                <p class="text-content-muted mt-1">Monitor and improve your site's search engine optimization</p>
            </div>

            <div class="flex items-center space-x-4">
                <button wire:click="runSeoAudit"
                        class="bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action transition-colors">
                    <i class="fas fa-search mr-2" aria-hidden="true"></i>Run SEO Audit
                </button>

                <button wire:click="generateSitemap"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-sitemap mr-2" aria-hidden="true"></i>Generate Sitemap
                </button>

                <button wire:click="exportSeoReport"
                        class="bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action transition-colors">
                    <i class="fas fa-download mr-2" aria-hidden="true"></i>Export Report
                </button>
            </div>
        </div>

        <!-- SEO Score -->
        <div class="mt-6 bg-surface-muted rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-content">Overall SEO Score</h3>
                <div class="flex items-center space-x-2">
                    <div class="w-16 h-16 relative">
                        <svg class="transform -rotate-90 w-16 h-16">
                            <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="transparent"></circle>
                            <circle cx="32" cy="32" r="28" stroke="{{ $seoScore >= 80 ? '#10b981' : ($seoScore >= 60 ? '#f59e0b' : '#ef4444') }}"
                                    stroke-width="4" fill="transparent"
                                    stroke-dasharray="175.929"
                                    stroke-dashoffset="{{ 175.929 - (175.929 * $seoScore / 100) }}"
                                    stroke-linecap="round"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-lg font-bold {{ $seoScore >= 80 ? 'text-green-600' : ($seoScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $seoScore }}
                            </span>
                        </div>
                    </div>
                    <div class="text-sm text-content-muted">
                        <p class="font-medium {{ $seoScore >= 80 ? 'text-green-600' : ($seoScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $seoScore >= 80 ? 'Excellent' : ($seoScore >= 60 ? 'Good' : 'Needs Improvement') }}
                        </p>
                        <p class="text-xs">Last updated: {{ now()->format('M j, g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Issues -->
    @if(count($seoIssues) > 0)
        <div class="bg-surface rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-line">
                <h3 class="text-lg font-semibold text-content">SEO Issues</h3>
                <p class="text-sm text-content-muted mt-1">Issues that need your attention</p>
            </div>

            <div class="divide-y divide-line">
                @foreach($seoIssues as $index => $issue)
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center {{
                                    $issue['type'] === 'critical' ? 'bg-red-100' :
                                    ($issue['type'] === 'warning' ? 'bg-yellow-100' : 'bg-blue-100')
                                }}">
                                    <i class="fas {{
                                        $issue['type'] === 'critical' ? 'fa-exclamation-triangle text-red-600' :
                                        ($issue['type'] === 'warning' ? 'fa-exclamation text-yellow-600' : 'fa-info text-blue-600')
                                    }}" aria-hidden="true"></i>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-lg font-medium text-content">{{ $issue['title'] }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{
                                        $issue['type'] === 'critical' ? 'bg-red-100 text-red-800' :
                                        ($issue['type'] === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')
                                    }}">
                                        {{ $issue['count'] }} page(s)
                                    </span>
                                </div>
                                <p class="text-sm text-content-muted mt-1">{{ $issue['description'] }}</p>

                                @if(isset($issue['pages']) && count($issue['pages']) > 0)
                                    <div class="mt-3">
                                        <h5 class="text-sm font-medium text-content mb-2">Affected pages:</h5>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(array_slice($issue['pages'], 0, 5, true) as $slug => $title)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-muted text-content">
                                                    {{ $title }}
                                                </span>
                                            @endforeach
                                            @if(count($issue['pages']) > 5)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-content-muted">
                                                    +{{ count($issue['pages']) - 5 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4 flex space-x-3">
                                    <button wire:click="fixSeoIssue({{ $index }})"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-action-foreground bg-action hover:bg-action">
                                        <i class="fas fa-wrench mr-1" aria-hidden="true"></i>
                                        Auto Fix
                                    </button>
                                    <button class="inline-flex items-center px-3 py-1 border border-line text-xs font-medium rounded-md text-content bg-surface hover:bg-surface-muted">
                                        <i class="fas fa-eye mr-1" aria-hidden="true"></i>
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- SEO Suggestions -->
    @if(count($seoSuggestions) > 0)
        <div class="bg-surface rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-line">
                <h3 class="text-lg font-semibold text-content">SEO Suggestions</h3>
                <p class="text-sm text-content-muted mt-1">Recommendations to improve your SEO</p>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    @foreach($seoSuggestions as $suggestion)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-lightbulb text-yellow-500 mt-0.5" aria-hidden="true"></i>
                            </div>
                            <div class="text-sm text-content">
                                {{ $suggestion }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Grid Layout for Sitemap Status and Crawl Errors -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sitemap Status -->
        <div class="bg-surface rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-line">
                <h3 class="text-lg font-semibold text-content">Sitemap Status</h3>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-content">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{
                        ($sitemapStatus['status'] ?? 'missing') === 'healthy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                    }}">
                        {{ ucfirst($sitemapStatus['status'] ?? 'Missing') }}
                    </span>
                </div>

                @if(isset($sitemapStatus['last_generated']))
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-content">Last Generated</span>
                        <span class="text-sm text-content">
                            {{ \Carbon\Carbon::parse($sitemapStatus['last_generated'])->diffForHumans() }}
                        </span>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-content">Total URLs</span>
                    <span class="text-sm text-content">{{ number_format($sitemapStatus['total_urls'] ?? 0) }}</span>
                </div>

                @if(isset($sitemapStatus['file_size']))
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-content">File Size</span>
                        <span class="text-sm text-content">
                            {{ number_format($sitemapStatus['file_size'] / 1024, 2) }} KB
                        </span>
                    </div>
                @endif

                <div class="pt-4 border-t border-line">
                    <button wire:click="generateSitemap"
                            class="w-full bg-action text-action-foreground py-2 px-4 rounded-lg hover:bg-action transition-colors text-sm">
                        <i class="fas fa-sync mr-2" aria-hidden="true"></i>Regenerate Sitemap
                    </button>
                </div>
            </div>
        </div>

        <!-- Crawl Errors -->
        <div class="bg-surface rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-line">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-content">Crawl Errors</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ count($crawlErrors) }}
                    </span>
                </div>
            </div>

            <div class="divide-y divide-line max-h-80 overflow-y-auto">
                @forelse($crawlErrors as $error)
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-content">{{ $error['url'] }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{
                                $error['severity'] === 'critical' ? 'bg-red-100 text-red-800' :
                                ($error['severity'] === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')
                            }}">
                                {{ $error['error'] }}
                            </span>
                        </div>
                        <p class="text-xs text-content-muted">Last seen: {{ $error['last_seen'] }}</p>
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-3" aria-hidden="true"></i>
                        <p class="text-sm text-content-muted">No crawl errors found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
