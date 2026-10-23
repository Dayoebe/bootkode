<div class="space-y-6">
    <!-- Header and Controls -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <div>
            <h2 class="text-xl font-semibold text-content dark:text-white">Bulk Enrollment</h2>
            <p class="text-sm text-content-muted dark:text-content-muted">Upload CSV files to enroll multiple users at once</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="downloadSampleCsv"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors">
                <i aria-hidden="true" class="fas fa-download mr-2"></i>
                Sample CSV
            </button>
            <button wire:click="openUploadModal"
                    class="inline-flex items-center px-4 py-2 bg-action hover:bg-action text-action-foreground text-sm font-medium rounded-md transition-colors">
                <i aria-hidden="true" class="fas fa-upload mr-2"></i>
                Upload CSV
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-4 transition-colors duration-300">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Status</label>
                <select aria-label="Status filter" wire:model.live="statusFilter" class="w-full border border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-brand/30 focus:border-brand">
                    <option value="all">All Statuses</option>
                    @foreach($statuses as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Institution</label>
                <select aria-label="Institution filter" wire:model.live="institutionFilter" class="w-full border border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-brand/30 focus:border-brand">
                    <option value="all">All Institutions</option>
                    @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Per Page</label>
                <select aria-label="Per page" wire:model.live="perPage" class="w-full border border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-brand/30 focus:border-brand">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Batches Table -->
    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 overflow-hidden transition-colors duration-300">
        <div class="overflow-x-auto">
            <x-ui.table label="Bulk Enrollment records"><table class="min-w-full divide-y divide-line dark:divide-gray-700">
                <thead class="bg-surface-muted dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted dark:text-content-muted uppercase tracking-wider cursor-pointer hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors"
                            wire:click="sortBy('name')">
                            <div class="flex items-center space-x-1">
                                <span>Batch</span>
                                @if($sortBy === 'name')
                                    <i aria-hidden="true" class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-brand dark:text-brand"></i>
                                @else
                                    <i aria-hidden="true" class="fas fa-sort text-gray-300 dark:text-content-muted"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted dark:text-content-muted uppercase tracking-wider">
                            Institution
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted dark:text-content-muted uppercase tracking-wider cursor-pointer hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors"
                            wire:click="sortBy('status')">
                            <div class="flex items-center space-x-1">
                                <span>Status</span>
                                @if($sortBy === 'status')
                                    <i aria-hidden="true" class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-brand dark:text-brand"></i>
                                @else
                                    <i aria-hidden="true" class="fas fa-sort text-gray-300 dark:text-content-muted"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted dark:text-content-muted uppercase tracking-wider">
                            Progress
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted dark:text-content-muted uppercase tracking-wider">
                            Results
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted dark:text-content-muted uppercase tracking-wider cursor-pointer hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors"
                            wire:click="sortBy('created_at')">
                            <div class="flex items-center space-x-1">
                                <span>Created</span>
                                @if($sortBy === 'created_at')
                                    <i aria-hidden="true" class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-brand dark:text-brand"></i>
                                @else
                                    <i aria-hidden="true" class="fas fa-sort text-gray-300 dark:text-content-muted"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted dark:text-content-muted uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-surface dark:bg-gray-800 divide-y divide-line dark:divide-gray-700">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-surface-muted dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-content dark:text-white">{{ Str::limit($batch->name, 30) }}</div>
                                    <div class="text-xs text-content-muted dark:text-content-muted">{{ $batch->original_filename }}</div>
                                    @if($batch->description)
                                        <div class="text-xs text-content-muted dark:text-content-muted mt-1">{{ Str::limit($batch->description, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-content dark:text-white">{{ $batch->institution->name }}</div>
                                <div class="text-xs text-content-muted dark:text-content-muted">{{ $batch->institution->institution_type_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($batch->status === 'completed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                    @elseif($batch->status === 'processing') bg-brand/10 dark:bg-action/30 text-brand dark:text-brand
                                    @elseif($batch->status === 'failed') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                    @elseif($batch->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                    @else bg-surface-muted dark:bg-gray-700 text-content dark:text-gray-300 @endif">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                        @if($batch->status === 'completed') bg-green-500 dark:bg-green-400
                                        @elseif($batch->status === 'processing') bg-action dark:bg-blue-400
                                        @elseif($batch->status === 'failed') bg-red-500 dark:bg-red-400
                                        @elseif($batch->status === 'pending') bg-yellow-500 dark:bg-yellow-400
                                        @else bg-gray-500 dark:bg-gray-400 @endif"></span>
                                    {{ $batch->status_name }}
                                </span>
                                @if($batch->isProcessing())
                                    <div class="text-xs text-content-muted dark:text-content-muted mt-1">
                                        Started {{ $batch->started_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($batch->total_records > 0)
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-action dark:bg-action h-2 rounded-full transition-all duration-300"
                                                     style="width: {{ $batch->getProgressPercentage() }}%"></div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-content-muted dark:text-content-muted whitespace-nowrap">
                                            {{ $batch->processed_records }}/{{ $batch->total_records }}
                                        </div>
                                    </div>
                                    <div class="text-xs text-content-muted dark:text-content-muted mt-1">
                                        {{ number_format($batch->getProgressPercentage(), 1) }}% complete
                                    </div>
                                @else
                                    <span class="text-xs text-content-muted dark:text-content-muted">No data</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if($batch->successful_enrollments > 0)
                                        <div class="text-xs text-green-600 dark:text-green-400">
                                            <i aria-hidden="true" class="fas fa-check mr-1"></i>{{ $batch->successful_enrollments }} successful
                                        </div>
                                    @endif
                                    @if($batch->failed_enrollments > 0)
                                        <div class="text-xs text-red-600 dark:text-red-400">
                                            <i aria-hidden="true" class="fas fa-times mr-1"></i>{{ $batch->failed_enrollments }} failed
                                        </div>
                                    @endif
                                    @if($batch->successful_enrollments == 0 && $batch->failed_enrollments == 0)
                                        <span class="text-xs text-content-muted dark:text-content-muted">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-content dark:text-white">{{ $batch->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-content-muted dark:text-content-muted">{{ $batch->created_at->format('g:i A') }}</div>
                                @if($batch->completed_at)
                                    <div class="text-xs text-content-muted dark:text-content-muted">
                                        Completed {{ $batch->completed_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button wire:click="viewDetails({{ $batch->id }})"
                                            class="text-brand dark:text-brand hover:text-brand dark:hover:text-brand transition-colors"
                                            title="View Details">
                                        <i aria-hidden="true" class="fas fa-eye"></i>
                                    </button>
                                    @if($batch->errors && count($batch->errors) > 0)
                                        <button wire:click="downloadErrorReport({{ $batch->id }})"
                                                class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300 transition-colors"
                                                title="Download Error Report">
                                            <i aria-hidden="true" class="fas fa-exclamation-triangle"></i>
                                        </button>
                                    @endif
                                    @if($batch->isFailed())
                                        <button wire:click="retryBatch({{ $batch->id }})"
                                                class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 transition-colors"
                                                title="Retry">
                                            <i aria-hidden="true" class="fas fa-redo"></i>
                                        </button>
                                    @endif
                                    @if($batch->isPending() || $batch->isProcessing())
                                        <button wire:click="cancelBatch({{ $batch->id }})"
                                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 transition-colors"
                                                title="Cancel">
                                            <i aria-hidden="true" class="fas fa-pause"></i>
                                        </button>
                                    @endif
                                    <button wire:click="deleteBatch({{ $batch->id }})"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors"
                                            title="Delete">
                                        <i aria-hidden="true" class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i aria-hidden="true" class="fas fa-users text-4xl text-gray-300 dark:text-content-muted mb-4"></i>
                                    <h3 class="text-lg font-medium text-content dark:text-white mb-2">No bulk enrollments yet</h3>
                                    <p class="text-sm text-content-muted dark:text-content-muted mb-4">Upload a CSV file to get started with bulk enrollments.</p>
                                    <button wire:click="openUploadModal"
                                            class="inline-flex items-center px-4 py-2 bg-action hover:bg-action text-action-foreground text-sm font-medium rounded-md transition-colors">
                                        <i aria-hidden="true" class="fas fa-upload mr-2"></i>
                                        Upload CSV
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table></x-ui.table>
        </div>

        <!-- Pagination -->
        @if($batches->hasPages())
            <div class="bg-surface dark:bg-gray-800 px-4 py-3 border-t border-line dark:border-gray-700 sm:px-6">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    <!-- Upload Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-gray-600 dark:bg-black bg-opacity-50 dark:bg-opacity-70 overflow-y-auto h-full w-full z-50" wire:click="closeModals" x-data="{ dialogOpen: true }" x-trap.inert.noscroll="dialogOpen" x-on:keydown.escape.window="dialogOpen = false; $wire.closeModals()" role="dialog" aria-modal="true" aria-label="Bulk Enrollment dialog" tabindex="-1">
            <div class="relative top-20 mx-auto p-5 border border-line dark:border-gray-700 w-11/12 md:w-2/3 lg:w-1/2 shadow-sm rounded-md bg-surface dark:bg-gray-800" wire:click.stop>
                <div class="flex items-center justify-between pb-4 border-b border-line dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-content dark:text-white">Upload Bulk Enrollment CSV</h3>
                    <button aria-label="Close dialog" wire:click="closeModals" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-gray-300">
                        <i aria-hidden="true" class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="uploadCsv" class="mt-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-content dark:text-gray-300">Institution *</label>
                            <select aria-label="Selected institution" wire:model="selectedInstitution" class="mt-1 block w-full border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand/30 focus:border-brand">
                                <option value="">Select Institution</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedInstitution') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            @php $selectedUploadInstitution = $institutions->firstWhere('id', (int) $selectedInstitution); @endphp
                            @if($selectedUploadInstitution)
                                <div class="mt-2 rounded-md border border-brand bg-brand/10 p-3 text-xs text-brand dark:border-brand dark:bg-action/20 dark:text-brand">
                                    License seats: {{ $selectedUploadInstitution->licenseLimitLabel() }}
                                    @if($selectedUploadInstitution->license_type !== 'enterprise')
                                        · {{ number_format($selectedUploadInstitution->remainingLicenseSeats()) }} remaining
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-content dark:text-gray-300">Courses to Enroll *</label>
                            <div class="mt-2 max-h-40 overflow-y-auto border border-line dark:border-gray-600 rounded-md p-2 bg-surface dark:bg-gray-700">
                                @foreach($courses as $course)
                                    <label class="flex items-center space-x-2 py-1">
                                        <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}"
                                               class="rounded border-line dark:border-gray-600 text-brand focus:ring-brand/30 dark:bg-gray-700">
                                        <span class="text-sm text-content dark:text-gray-300">{{ $course->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedCourses') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-content dark:text-gray-300">Batch Name *</label>
                            <input aria-label="Batch name" type="text" wire:model="batchName"
                                   class="mt-1 block w-full border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand/30 focus:border-brand">
                            @error('batchName') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-content dark:text-gray-300">Description</label>
                            <textarea aria-label="Batch description" wire:model="batchDescription" rows="2"
                                      class="mt-1 block w-full border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand/30 focus:border-brand"></textarea>
                            @error('batchDescription') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-content dark:text-gray-300">CSV File *</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-line dark:border-gray-600 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500 transition-colors bg-surface dark:bg-gray-700/30">
                                <div class="space-y-1 text-center">
                                    <i aria-hidden="true" class="fas fa-cloud-upload-alt text-3xl text-content-muted dark:text-content-muted"></i>
                                    <div class="flex text-sm text-content-muted dark:text-content-muted">
                                        <label class="relative cursor-pointer bg-surface dark:bg-gray-800 rounded-md font-medium text-brand dark:text-brand hover:text-brand dark:hover:text-brand">
                                            <span>Upload a file</span>
                                            <input type="file" wire:model="csvFile" accept=".csv,.txt" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-content-muted dark:text-content-muted">CSV files up to 10MB</p>
                                    @if($csvFile)
                                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">
                                            <i aria-hidden="true" class="fas fa-file-csv mr-1"></i>{{ $csvFile->getClientOriginalName() }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @error('csvFile') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- CSV Format Help -->
                        <div class="bg-brand/10 dark:bg-action/20 border border-brand dark:border-brand rounded-md p-3">
                            <div class="flex">
                                <i aria-hidden="true" class="fas fa-info-circle text-brand dark:text-brand mt-0.5 mr-2"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-brand dark:text-brand">CSV Format Requirements</h4>
                                    <div class="text-sm text-brand dark:text-brand mt-1">
                                        <p>Required columns: <code class="bg-brand/10 dark:bg-action/40 px-1 rounded">name</code>, <code class="bg-brand/10 dark:bg-action/40 px-1 rounded">email</code></p>
                                        <p>Optional columns: <code class="bg-brand/10 dark:bg-action/40 px-1 rounded">department</code>, <code class="bg-brand/10 dark:bg-action/40 px-1 rounded">employee_id</code>, <code class="bg-brand/10 dark:bg-action/40 px-1 rounded">institution_role</code></p>
                                        <p class="mt-2">
                                            <button type="button" wire:click="downloadSampleCsv"
                                                    class="text-brand dark:text-brand hover:text-brand dark:hover:text-brand underline">
                                                Download sample CSV
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-line dark:border-gray-700">
                        <button type="button" wire:click="closeModals"
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-content dark:text-white text-sm font-medium rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-action hover:bg-action text-action-foreground text-sm font-medium rounded-md transition-colors">
                            <i aria-hidden="true" class="fas fa-upload mr-2"></i>Start Bulk Enrollment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Details Modal -->
    @if($showDetailsModal && $selectedBatch)
        <div class="fixed inset-0 bg-gray-600 dark:bg-black bg-opacity-50 dark:bg-opacity-70 overflow-y-auto h-full w-full z-50" wire:click="closeModals" x-data="{ dialogOpen: true }" x-trap.inert.noscroll="dialogOpen" x-on:keydown.escape.window="dialogOpen = false; $wire.closeModals()" role="dialog" aria-modal="true" aria-label="Bulk Enrollment dialog" tabindex="-1">
            <div class="relative top-20 mx-auto p-5 border border-line dark:border-gray-700 w-11/12 md:w-2/3 lg:w-1/2 shadow-sm rounded-md bg-surface dark:bg-gray-800" wire:click.stop>
                <div class="flex items-center justify-between pb-4 border-b border-line dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-content dark:text-white">Batch Details</h3>
                    <button aria-label="Close dialog" wire:click="closeModals" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-gray-300">
                        <i aria-hidden="true" class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="mt-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-content dark:text-white mb-3">Batch Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-xs font-medium text-content-muted dark:text-content-muted">Name</dt>
                                    <dd class="text-sm text-content dark:text-white">{{ $selectedBatch->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-content-muted dark:text-content-muted">Institution</dt>
                                    <dd class="text-sm text-content dark:text-white">{{ $selectedBatch->institution->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-content-muted dark:text-content-muted">Status</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($selectedBatch->status === 'completed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                            @elseif($selectedBatch->status === 'processing') bg-brand/10 dark:bg-action/30 text-brand dark:text-brand
                                            @elseif($selectedBatch->status === 'failed') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                            @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 @endif">
                                            {{ $selectedBatch->status_name }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-content-muted dark:text-content-muted">Created by</dt>
                                    <dd class="text-sm text-content dark:text-white">{{ $selectedBatch->creator->name }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h4 class="font-medium text-content dark:text-white mb-3">Progress</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-xs font-medium text-content-muted dark:text-content-muted">Total Records</dt>
                                    <dd class="text-sm text-content dark:text-white">{{ number_format($selectedBatch->total_records) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-content-muted dark:text-content-muted">Processed</dt>
                                    <dd class="text-sm text-content dark:text-white">{{ number_format($selectedBatch->processed_records) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-content-muted dark:text-content-muted">Successful</dt>
                                    <dd class="text-sm text-green-600 dark:text-green-400">{{ number_format($selectedBatch->successful_enrollments) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-content-muted dark:text-content-muted">Failed</dt>
                                    <dd class="text-sm text-red-600 dark:text-red-400">{{ number_format($selectedBatch->failed_enrollments) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($selectedBatch->course_names)
                        <div>
                            <h4 class="font-medium text-content dark:text-white mb-2">Enrolled Courses</h4>
                            <p class="text-sm text-content-muted dark:text-content-muted">{{ $selectedBatch->course_names }}</p>
                        </div>
                    @endif

                    @if($selectedBatch->description)
                        <div>
                            <h4 class="font-medium text-content dark:text-white mb-2">Description</h4>
                            <p class="text-sm text-content-muted dark:text-content-muted">{{ $selectedBatch->description }}</p>
                        </div>
                    @endif

                    @if($selectedBatch->errors && count($selectedBatch->errors) > 0)
                        <div>
                            <h4 class="font-medium text-content dark:text-white mb-2">Recent Errors ({{ count($selectedBatch->errors) }} total)</h4>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3 max-h-40 overflow-y-auto">
                                @foreach(array_slice($selectedBatch->errors, 0, 5) as $error)
                                    <div class="text-sm text-red-800 dark:text-red-300 mb-1">
                                        Row {{ $error['row'] ?? 'N/A' }}: {{ $error['message'] ?? 'Unknown error' }}
                                    </div>
                                @endforeach
                                @if(count($selectedBatch->errors) > 5)
                                    <div class="text-xs text-red-600 dark:text-red-400 mt-2">
                                        And {{ count($selectedBatch->errors) - 5 }} more errors...
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-line dark:border-gray-700">
                    @if($selectedBatch->errors && count($selectedBatch->errors) > 0)
                        <button wire:click="downloadErrorReport({{ $selectedBatch->id }})"
                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-md transition-colors">
                            <i aria-hidden="true" class="fas fa-download mr-2"></i>Download Error Report
                        </button>
                    @endif
                    <button wire:click="closeModals"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-content dark:text-white text-sm font-medium rounded-md transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
