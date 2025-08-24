<div class="min-h-screen bg-gray-900 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Certificate Management</h1>
                <p class="text-gray-300 mt-2">Review and manage certificate requests</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-4 gap-4">
                <div class="bg-yellow-900/30 border border-yellow-500 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-yellow-400">{{ $certificates->where('status', 'requested')->count() }}</div>
                    <div class="text-xs text-yellow-300">Pending</div>
                </div>
                <div class="bg-green-900/30 border border-green-500 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-green-400">{{ $certificates->where('status', 'approved')->count() }}</div>
                    <div class="text-xs text-green-300">Approved</div>
                </div>
                <div class="bg-red-900/30 border border-red-500 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-red-400">{{ $certificates->where('status', 'rejected')->count() }}</div>
                    <div class="text-xs text-red-300">Rejected</div>
                </div>
                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-white">{{ $certificates->total() }}</div>
                    <div class="text-xs text-gray-300">Total</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-800 rounded-xl p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select wire:model.live="statusFilter" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="all">All Status</option>
                        <option value="requested">Requested</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="revoked">Revoked</option>
                    </select>
                </div>

                <!-- Course Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Course</label>
                    <select wire:model.live="courseFilter" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="all">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">From Date</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">To Date</label>
                    <input type="date" wire:model.live="dateTo" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" wire:model.live="searchTerm" placeholder="Student name, email, cert #..." 
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg pl-10 pr-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <button wire:click="clearFilters" class="text-indigo-400 hover:text-indigo-300 text-sm">
                    <i class="fas fa-times mr-1"></i> Clear Filters
                </button>
                
                @if(auth()->user()->isSuperAdmin())
                <div class="flex gap-2">
                    <button onclick="bulkApprove()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition-colors">
                        <i class="fas fa-check mr-2"></i>Bulk Approve Selected
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Certificates Table -->
        <div class="bg-gray-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            @if(auth()->user()->isSuperAdmin())
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-500 bg-gray-600 text-indigo-600">
                            </th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Certificate #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Requested</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($certificates as $certificate)
                        <tr class="hover:bg-gray-700 transition-colors">
                            @if(auth()->user()->isSuperAdmin())
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($certificate->isRequested())
                                <input type="checkbox" class="certificate-checkbox rounded border-gray-500 bg-gray-600 text-indigo-600" 
                                       value="{{ $certificate->id }}">
                                @endif
                            </td>
                            @endif
                            
                            <!-- Student Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ substr($certificate->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-white">{{ $certificate->user->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $certificate->user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Course Info -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-white font-medium">{{ $certificate->course->title }}</div>
                                <div class="text-sm text-gray-400">{{ $certificate->course->instructor->name }}</div>
                            </td>

                            <!-- Certificate Number -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-white">{{ $certificate->certificate_number }}</div>
                                <div class="text-xs text-gray-400">{{ $certificate->verification_code }}</div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($certificate->status === 'approved') bg-green-900 text-green-300
                                    @elseif($certificate->status === 'requested') bg-yellow-900 text-yellow-300
                                    @elseif($certificate->status === 'rejected') bg-red-900 text-red-300
                                    @elseif($certificate->status === 'revoked') bg-gray-700 text-gray-300
                                    @else bg-blue-900 text-blue-300 @endif">
                                    <i class="{{ $certificate->status_icon }} mr-1.5"></i>
                                    {{ ucfirst(str_replace('_', ' ', $certificate->status)) }}
                                </span>
                            </td>

                            <!-- Grade -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($certificate->grade)
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-indigo-900 text-indigo-300 text-sm font-semibold">
                                        {{ $certificate->grade }}
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>

                            <!-- Requested Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $certificate->requested_at->format('M j, Y') }}
                                <div class="text-xs text-gray-500">{{ $certificate->requested_at->diffForHumans() }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($certificate->isRequested())
                                        <button wire:click="showApprovalModal({{ $certificate->id }})"
                                                class="text-green-400 hover:text-green-300 p-2 rounded-lg hover:bg-green-900/30 transition-colors"
                                                title="Approve Certificate">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button wire:click="showRejectionModal({{ $certificate->id }})"
                                                class="text-red-400 hover:text-red-300 p-2 rounded-lg hover:bg-red-900/30 transition-colors"
                                                title="Reject Certificate">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @elseif($certificate->isApproved())
                                        <a href="{{ route('certificate.view', $certificate->verification_code) }}" 
                                           target="_blank"
                                           class="text-indigo-400 hover:text-indigo-300 p-2 rounded-lg hover:bg-indigo-900/30 transition-colors"
                                           title="View Certificate">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isAcademyAdmin())
                                        <button wire:click="showRevocationModal({{ $certificate->id }})"
                                                class="text-yellow-400 hover:text-yellow-300 p-2 rounded-lg hover:bg-yellow-900/30 transition-colors"
                                                title="Revoke Certificate">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        @endif
                                    @endif
                                    
                                    <!-- Info Button -->
                                    <button wire:click="selectCertificate({{ $certificate->id }})"
                                            onclick="document.getElementById('certificateDetailsModal').classList.remove('hidden')"
                                            class="text-gray-400 hover:text-gray-300 p-2 rounded-lg hover:bg-gray-700 transition-colors"
                                            title="View Details">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isSuperAdmin() ? 8 : 7 }}" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-certificate text-4xl mb-4"></i>
                                    <div class="text-lg font-medium mb-2">No certificates found</div>
                                    <div class="text-sm">Try adjusting your filters or search criteria</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($certificates->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $certificates->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Certificate Details Modal -->
    <div id="certificateDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-gray-800 rounded-xl max-w-4xl w-full mx-4 max-h-screen overflow-y-auto">
            @if($selectedCertificate)
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Certificate Details</h3>
                    <button onclick="document.getElementById('certificateDetailsModal').classList.add('hidden')"
                            class="text-gray-400 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Certificate Overview -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-white mb-3">Student Information</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Name:</span>
                                    <span class="text-white">{{ $selectedCertificate->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Email:</span>
                                    <span class="text-white">{{ $selectedCertificate->user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-white mb-3">Course Information</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Course:</span>
                                    <span class="text-white">{{ $selectedCertificate->course->title }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Instructor:</span>
                                    <span class="text-white">{{ $selectedCertificate->course->instructor->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Completion:</span>
                                    <span class="text-white">{{ $selectedCertificate->completion_date->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-white mb-3">Certificate Details</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Certificate #:</span>
                                    <span class="text-white font-mono">{{ $selectedCertificate->certificate_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Status:</span>
                                    <span class="capitalize text-{{ $selectedCertificate->status_color }}-400">
                                        {{ str_replace('_', ' ', $selectedCertificate->status) }}
                                    </span>
                                </div>
                                @if($selectedCertificate->grade)
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Grade:</span>
                                    <span class="text-white font-semibold">{{ $selectedCertificate->grade }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Verification:</span>
                                    <span class="text-white font-mono text-xs">{{ $selectedCertificate->verification_code }}</span>
                                </div>
                            </div>
                        </div>

                        @if($selectedCertificate->isApproved())
                        <div class="bg-gray-700 rounded-lg p-4 text-center">
                            <h4 class="text-lg font-semibold text-white mb-3">Quick Actions</h4>
                            <div class="space-y-2">
                                <a href="{{ route('certificate.view', $selectedCertificate->verification_code) }}" 
                                   target="_blank"
                                   class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition-colors inline-flex items-center justify-center">
                                    <i class="fas fa-eye mr-2"></i>View Certificate
                                </a>
                                <a href="{{ route('certificate.download', $selectedCertificate->verification_code) }}" 
                                   target="_blank"
                                   class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors inline-flex items-center justify-center">
                                    <i class="fas fa-download mr-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Status History -->
                <div class="bg-gray-700 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-white mb-3">Status History</h4>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <span class="text-white">Requested</span>
                                <span class="text-gray-400 ml-2">{{ $selectedCertificate->requested_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                        
                        @if($selectedCertificate->approved_at)
                        <div class="flex items-center text-sm">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <span class="text-white">Approved by {{ $selectedCertificate->approver->name }}</span>
                                <span class="text-gray-400 ml-2">{{ $selectedCertificate->approved_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                        @endif

                        @if($selectedCertificate->rejected_at)
                        <div class="flex items-center text-sm">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <span class="text-white">Rejected by {{ $selectedCertificate->rejecter->name }}</span>
                                <span class="text-gray-400 ml-2">{{ $selectedCertificate->rejected_at->format('M j, Y g:i A') }}</span>
                                @if($selectedCertificate->rejection_reason)
                                <div class="text-red-300 text-xs mt-1">{{ $selectedCertificate->rejection_reason }}</div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($selectedCertificate->revoked_at)
                        <div class="flex items-center text-sm">
                            <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <span class="text-white">Revoked by {{ $selectedCertificate->revoker->name }}</span>
                                <span class="text-gray-400 ml-2">{{ $selectedCertificate->revoked_at->format('M j, Y g:i A') }}</span>
                                @if($selectedCertificate->revocation_reason)
                                <div class="text-gray-300 text-xs mt-1">{{ $selectedCertificate->revocation_reason }}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Approval Modal -->
    @if($showApprovalModal && $selectedCertificate)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-4">Approve Certificate</h3>
                <p class="text-gray-300 mb-6">
                    Are you sure you want to approve the certificate for <strong>{{ $selectedCertificate->user->name }}</strong> 
                    in the course <strong>{{ $selectedCertificate->course->title }}</strong>?
                </p>
                <div class="flex justify-end space-x-4">
                    <button wire:click="closeModals" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="approveCertificate" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Rejection Modal -->
    @if($showRejectionModal && $selectedCertificate)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-4">Reject Certificate</h3>
                <p class="text-gray-300 mb-4">
                    Please provide a reason for rejecting this certificate request:
                </p>
                <textarea 
                    wire:model="rejectionReason" 
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-red-500 h-24 resize-none"
                    placeholder="Enter reason for rejection..."
                    required
                ></textarea>
                @error('rejectionReason') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                
                <div class="flex justify-end space-x-4 mt-6">
                    <button wire:click="closeModals" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="rejectCertificate" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Reject
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Revocation Modal -->
    @if($showRevocationModal && $selectedCertificate)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-4">Revoke Certificate</h3>
                <div class="bg-yellow-900/30 border border-yellow-500 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-yellow-400">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span class="font-semibold">Warning: This action cannot be undone</span>
                    </div>
                    <p class="text-yellow-300 text-sm mt-1">
                        Revoking this certificate will make it invalid and unverifiable.
                    </p>
                </div>
                
                <p class="text-gray-300 mb-4">
                    Please provide a reason for revoking this certificate:
                </p>
                <textarea 
                    wire:model="revocationReason" 
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-yellow-500 h-24 resize-none"
                    placeholder="Enter reason for revocation..."
                    required
                ></textarea>
                @error('revocationReason') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                
                <div class="flex justify-end space-x-4 mt-6">
                    <button wire:click="closeModals" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="revokeCertificate" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-ban mr-2"></i>Revoke Certificate
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-8 flex items-center">
            <i class="fas fa-spinner fa-spin text-indigo-400 text-2xl mr-4"></i>
            <span class="text-white font-medium">Processing request...</span>
        </div>
    </div>
</div>

<script>
    // Bulk operations
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.certificate-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    function bulkApprove() {
        const selectedIds = Array.from(document.querySelectorAll('.certificate-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Please select certificates to approve');
            return;
        }

        if (confirm(`Are you sure you want to approve ${selectedIds.length} certificates?`)) {
            @this.call('bulkApprove', selectedIds);
        }
    }

    // Close modals on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('certificateDetailsModal').classList.add('hidden');
            @this.call('closeModals');
        }
    });
</script>