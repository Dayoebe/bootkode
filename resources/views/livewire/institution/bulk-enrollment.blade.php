<div class="space-y-6">
    <!-- Header and Controls -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Bulk Enrollment</h2>
            <p class="text-sm text-gray-600">Upload CSV files to enroll multiple users at once</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="downloadSampleCsv" 
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-download mr-2"></i>
                Sample CSV
            </button>
            <button wire:click="openUploadModal" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-upload mr-2"></i>
                Upload CSV
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Statuses</option>
                    @foreach($statuses as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Institution</label>
                <select wire:model.live="institutionFilter" class="w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Institutions</option>
                    @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                <select wire:model.live="perPage" class="w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" 
                            wire:click="sortBy('name')">
                            <div class="flex items-center space-x-1">
                                <span>Batch</span>
                                @if($sortBy === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Institution
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" 
                            wire:click="sortBy('status')">
                            <div class="flex items-center space-x-1">
                                <span>Status</span>
                                @if($sortBy === 'status')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Progress
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Results
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" 
                            wire:click="sortBy('created_at')">
                            <div class="flex items-center space-x-1">
                                <span>Created</span>
                                @if($sortBy === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($batch->name, 30) }}</div>
                                    <div class="text-xs text-gray-500">{{ $batch->original_filename }}</div>
                                    @if($batch->description)
                                        <div class="text-xs text-gray-400 mt-1">{{ Str::limit($batch->description, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $batch->institution->name }}</div>
                                <div class="text-xs text-gray-500">{{ $batch->institution->institution_type_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($batch->status === 'completed') bg-green-100 text-green-800
                                    @elseif($batch->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($batch->status === 'failed') bg-red-100 text-red-800
                                    @elseif($batch->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                        @if($batch->status === 'completed') bg-green-500
                                        @elseif($batch->status === 'processing') bg-blue-500
                                        @elseif($batch->status === 'failed') bg-red-500
                                        @elseif($batch->status === 'pending') bg-yellow-500
                                        @else bg-gray-500 @endif"></span>
                                    {{ $batch->status_name }}
                                </span>
                                @if($batch->isProcessing())
                                    <div class="text-xs text-gray-500 mt-1">
                                        Started {{ $batch->started_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($batch->total_records > 0)
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" 
                                                     style="width: {{ $batch->getProgressPercentage() }}%"></div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 whitespace-nowrap">
                                            {{ $batch->processed_records }}/{{ $batch->total_records }}
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ number_format($batch->getProgressPercentage(), 1) }}% complete
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500">No data</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if($batch->successful_enrollments > 0)
                                        <div class="text-xs text-green-600">
                                            <i class="fas fa-check mr-1"></i>{{ $batch->successful_enrollments }} successful
                                        </div>
                                    @endif
                                    @if($batch->failed_enrollments > 0)
                                        <div class="text-xs text-red-600">
                                            <i class="fas fa-times mr-1"></i>{{ $batch->failed_enrollments }} failed
                                        </div>
                                    @endif
                                    @if($batch->successful_enrollments == 0 && $batch->failed_enrollments == 0)
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $batch->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $batch->created_at->format('g:i A') }}</div>
                                @if($batch->completed_at)
                                    <div class="text-xs text-gray-400">
                                        Completed {{ $batch->completed_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button wire:click="viewDetails({{ $batch->id }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($batch->errors && count($batch->errors) > 0)
                                        <button wire:click="downloadErrorReport({{ $batch->id }})"
                                                class="text-orange-600 hover:text-orange-900 transition-colors"
                                                title="Download Error Report">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </button>
                                    @endif
                                    @if($batch->isFailed())
                                        <button wire:click="retryBatch({{ $batch->id }})"
                                                class="text-green-600 hover:text-green-900 transition-colors"
                                                title="Retry">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    @endif
                                    @if($batch->isPending() || $batch->isProcessing())
                                        <button wire:click="cancelBatch({{ $batch->id }})"
                                                class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                                title="Cancel">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @endif
                                    <button wire:click="deleteBatch({{ $batch->id }})"
                                            class="text-red-600 hover:text-red-900 transition-colors"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No bulk enrollments yet</h3>
                                    <p class="text-sm text-gray-500 mb-4">Upload a CSV file to get started with bulk enrollments.</p>
                                    <button wire:click="openUploadModal"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                        <i class="fas fa-upload mr-2"></i>
                                        Upload CSV
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($batches->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    <!-- Upload Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModals">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Upload Bulk Enrollment CSV</h3>
                    <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="uploadCsv" class="mt-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Institution *</label>
                            <select wire:model="selectedInstitution" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Institution</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedInstitution') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Courses to Enroll *</label>
                            <div class="mt-2 max-h-40 overflow-y-auto border border-gray-300 rounded-md p-2">
                                @foreach($courses as $course)
                                    <label class="flex items-center space-x-2 py-1">
                                        <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ $course->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedCourses') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Batch Name *</label>
                            <input type="text" wire:model="batchName" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('batchName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="batchDescription" rows="2" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('batchDescription') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">CSV File *</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Upload a file</span>
                                            <input type="file" wire:model="csvFile" accept=".csv,.txt" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">CSV files up to 10MB</p>
                                    @if($csvFile)
                                        <p class="text-sm text-green-600 mt-2">
                                            <i class="fas fa-file-csv mr-1"></i>{{ $csvFile->getClientOriginalName() }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @error('csvFile') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- CSV Format Help -->
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                            <div class="flex">
                                <i class="fas fa-info-circle text-blue-400 mt-0.5 mr-2"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">CSV Format Requirements</h4>
                                    <div class="text-sm text-blue-700 mt-1">
                                        <p>Required columns: <code>name</code>, <code>email</code></p>
                                        <p>Optional columns: <code>department</code>, <code>employee_id</code>, <code>institution_role</code></p>
                                        <p class="mt-2">
                                            <button type="button" wire:click="downloadSampleCsv" 
                                                    class="text-blue-600 hover:text-blue-800 underline">
                                                Download sample CSV
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="closeModals" 
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                            <i class="fas fa-upload mr-2"></i>Start Bulk Enrollment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Details Modal -->
    @if($showDetailsModal && $selectedBatch)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModals">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Batch Details</h3>
                    <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="mt-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Batch Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedBatch->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Institution</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedBatch->institution->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Status</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $selectedBatch->status_color }}-100 text-{{ $selectedBatch->status_color }}-800">
                                            {{ $selectedBatch->status_name }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Created by</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedBatch->creator->name }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Progress</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Total Records</dt>
                                    <dd class="text-sm text-gray-900">{{ number_format($selectedBatch->total_records) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Processed</dt>
                                    <dd class="text-sm text-gray-900">{{ number_format($selectedBatch->processed_records) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Successful</dt>
                                    <dd class="text-sm text-green-600">{{ number_format($selectedBatch->successful_enrollments) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Failed</dt>
                                    <dd class="text-sm text-red-600">{{ number_format($selectedBatch->failed_enrollments) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($selectedBatch->course_names)
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Enrolled Courses</h4>
                            <p class="text-sm text-gray-600">{{ $selectedBatch->course_names }}</p>
                        </div>
                    @endif

                    @if($selectedBatch->description)
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Description</h4>
                            <p class="text-sm text-gray-600">{{ $selectedBatch->description }}</p>
                        </div>
                    @endif

                    @if($selectedBatch->errors && count($selectedBatch->errors) > 0)
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Recent Errors ({{ count($selectedBatch->errors) }} total)</h4>
                            <div class="bg-red-50 border border-red-200 rounded-md p-3 max-h-40 overflow-y-auto">
                                @foreach(array_slice($selectedBatch->errors, 0, 5) as $error)
                                    <div class="text-sm text-red-800 mb-1">
                                        Row {{ $error['row'] ?? 'N/A' }}: {{ $error['message'] ?? 'Unknown error' }}
                                    </div>
                                @endforeach
                                @if(count($selectedBatch->errors) > 5)
                                    <div class="text-xs text-red-600 mt-2">
                                        And {{ count($selectedBatch->errors) - 5 }} more errors...
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    @if($selectedBatch->errors && count($selectedBatch->errors) > 0)
                        <button wire:click="downloadErrorReport({{ $selectedBatch->id }})" 
                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-md transition-colors">
                            <i class="fas fa-download mr-2"></i>Download Error Report
                        </button>
                    @endif
                    <button wire:click="closeModals" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-md transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>