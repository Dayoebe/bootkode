<div class="min-h-screen bg-gray-900 p-6">
    <div class="">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">My Certificates</h1>
                <p class="text-gray-300 mt-2">View and manage your earned certificates</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('student.course-catalog') }}" 
                   class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors flex items-center">
                    <i class="fas fa-book mr-2"></i>
                    Browse Courses
                </a>
                <button wire:click="requestNewCertificate"
                   class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Request Certificate
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-3xl mr-4"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ $stats['approved'] }}</div>
                        <div class="text-sm opacity-90">Approved</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 rounded-xl p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-clock text-3xl mr-4"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ $stats['pending'] }}</div>
                        <div class="text-sm opacity-90">Pending</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-red-600 to-pink-600 rounded-xl p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-3xl mr-4"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ $stats['rejected'] }}</div>
                        <div class="text-sm opacity-90">Rejected</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-certificate text-3xl mr-4"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                        <div class="text-sm opacity-90">Total</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-800 rounded-xl p-6 mb-8 border border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select wire:model.live="statusFilter" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="all">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="requested">Pending</option>
                        <option value="rejected">Rejected</option>
                        <option value="revoked">Revoked</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                               placeholder="Course name, certificate #..." 
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg pl-10 pr-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Sort By</label>
                    <select wire:model.live="sortBy" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="created_at">Request Date</option>
                        <option value="completion_date">Completion Date</option>
                        <option value="approved_at">Approval Date</option>
                        <option value="status">Status</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Order</label>
                    <select wire:model.live="sortDirection" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="desc">Newest First</option>
                        <option value="asc">Oldest First</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <button wire:click="clearFilters" class="text-indigo-400 hover:text-indigo-300 text-sm transition-colors">
                    <i class="fas fa-times mr-1"></i> Clear Filters
                </button>
                
                <div class="text-gray-400 text-sm">
                    Showing {{ $certificates->count() }} of {{ $certificates->total() }} certificates
                </div>
            </div>
        </div>

        <!-- Certificates Grid -->
        @if($certificates->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($certificates as $certificate)
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition-all duration-200 hover:shadow-lg">
                    <!-- Status Badge -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($certificate->status === 'approved') bg-green-900 text-green-300
                            @elseif($certificate->status === 'requested') bg-yellow-900 text-yellow-300
                            @elseif($certificate->status === 'rejected') bg-red-900 text-red-300
                            @elseif($certificate->status === 'revoked') bg-gray-700 text-gray-300
                            @else bg-blue-900 text-blue-300 @endif">
                            <i class="fas fa-{{ $certificate->status === 'approved' ? 'check-circle' : 
                                ($certificate->status === 'requested' ? 'clock' : 
                                ($certificate->status === 'rejected' ? 'times-circle' : 
                                ($certificate->status === 'revoked' ? 'ban' : 'certificate'))) }} mr-1.5"></i>
                            {{ ucfirst(str_replace('_', ' ', $certificate->status)) }}
                        </span>
                        
                        @if($certificate->grade)
                        <span class="bg-indigo-900 text-indigo-300 px-2 py-1 rounded text-sm font-semibold">
                            {{ $certificate->grade }}
                        </span>
                        @endif
                    </div>

                    <!-- Course Info -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2">{{ $certificate->course->title }}</h3>
                        <p class="text-gray-400 text-sm">{{ $certificate->course->instructor->name ?? 'N/A' }}</p>
                    </div>

                    <!-- Certificate Details -->
                    <div class="space-y-2 text-sm mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Certificate #:</span>
                            <span class="text-white font-mono text-xs bg-gray-700 px-2 py-1 rounded">
                                {{ $certificate->certificate_number }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Completed:</span>
                            <span class="text-white">{{ $certificate->completion_date->format('M j, Y') }}</span>
                        </div>
                        @if($certificate->approved_at)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Issued:</span>
                            <span class="text-white">{{ $certificate->approved_at->format('M j, Y') }}</span>
                        </div>
                        @endif
                        @if($certificate->credits)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Credits:</span>
                            <span class="text-white">{{ $certificate->credits }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2">
                        @if($certificate->status === 'approved')
                            <div class="flex gap-2">
                                @if($certificate->pdf_path)
                                <button wire:click="downloadCertificate({{ $certificate->id }})"
                                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center">
                                    <i class="fas fa-download mr-1"></i>Download
                                </button>
                                @endif
                                <button wire:click="viewCertificate({{ $certificate->id }})"
                                       class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                            </div>
                            @if($certificate->verification_code)
                            <div class="text-center">
                                <span class="text-xs text-gray-400">Verify at: </span>
                                <a href="{{ route('certificate.verify.code', $certificate->verification_code) }}" 
                                   target="_blank" 
                                   class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">
                                    {{ Str::limit(route('certificate.verify.code', $certificate->verification_code), 40) }}
                                </a>
                            </div>
                            @endif
                        @elseif($certificate->status === 'requested')
                            <div class="bg-yellow-900/30 border border-yellow-500 rounded-lg p-3 text-center">
                                <i class="fas fa-clock text-yellow-400 mr-2"></i>
                                <span class="text-yellow-300 text-sm">Pending Review</span>
                            </div>
                        @elseif($certificate->status === 'rejected')
                            <div class="bg-red-900/30 border border-red-500 rounded-lg p-3 text-center">
                                <i class="fas fa-times-circle text-red-400 mr-2"></i>
                                <span class="text-red-300 text-sm">Request Rejected</span>
                            </div>
                        @elseif($certificate->status === 'revoked')
                            <div class="bg-gray-700 border border-gray-600 rounded-lg p-3 text-center">
                                <i class="fas fa-ban text-gray-400 mr-2"></i>
                                <span class="text-gray-300 text-sm">Certificate Revoked</span>
                            </div>
                        @endif
                    </div>

                    <!-- Rejection/Revocation Reason -->
                    @if($certificate->status === 'rejected' && $certificate->rejection_reason)
                    <div class="mt-3 p-3 bg-red-900/20 border border-red-900/30 rounded-lg">
                        <div class="text-red-300 text-xs">
                            <strong>Reason:</strong> {{ $certificate->rejection_reason }}
                        </div>
                    </div>
                    @elseif($certificate->status === 'revoked' && $certificate->revocation_reason)
                    <div class="mt-3 p-3 bg-gray-700/50 border border-gray-600/30 rounded-lg">
                        <div class="text-gray-300 text-xs">
                            <strong>Revoked:</strong> {{ $certificate->revocation_reason }}
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($certificates->hasPages())
            <div class="flex justify-center">
                {{ $certificates->links() }}
            </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-xl p-12 text-center border border-gray-700">
                @if($statusFilter !== 'all' || $searchTerm)
                    <!-- No results for filters -->
                    <i class="fas fa-search text-6xl text-gray-600 mb-6"></i>
                    <h3 class="text-xl font-semibold text-white mb-4">No certificates found</h3>
                    <p class="text-gray-400 mb-8">No certificates match your current filters. Try adjusting your search criteria.</p>
                    <button wire:click="clearFilters" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Clear Filters
                    </button>
                @else
                    <!-- No certificates at all -->
                    <i class="fas fa-certificate text-6xl text-gray-600 mb-6"></i>
                    <h3 class="text-xl font-semibold text-white mb-4">No Certificates Yet</h3>
                    <p class="text-gray-400 mb-8">Complete courses to earn certificates and showcase your achievements.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('student.course-catalog') }}" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                            <i class="fas fa-book mr-2"></i>
                            Explore Courses
                        </a>
                        <a href="{{ route('student.enrolled-courses') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            My Enrolled Courses
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-8 flex items-center">
            <i class="fas fa-spinner fa-spin text-indigo-400 text-2xl mr-4"></i>
            <span class="text-white font-medium">Loading certificates...</span>
        </div>
    </div>
    
    <script>
    // Handle external URL opening
    document.addEventListener('livewire:init', function () {
        Livewire.on('open-url', (event) => {
            window.open(event.url, '_blank');
        });
    });

    // Add some smooth scrolling for pagination
    document.addEventListener('DOMContentLoaded', function() {
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function() {
                setTimeout(() => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 100);
            });
        });
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Custom scrollbar for activity feed */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #374151;
        border-radius: 3px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #6366f1;
        border-radius: 3px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #4f46e5;
    }
</style>
</div>