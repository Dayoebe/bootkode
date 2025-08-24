<?php
// resources/views/certificates/verification-report.blade.php
?>
<x-app-layout>
<div class="min-h-screen bg-gray-900 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Certificate Verification Report</h1>
                <p class="text-gray-300 mt-2">Detailed verification analytics for {{ $certificate->certificate_number }}</p>
            </div>
            
            <div class="flex gap-4">
                <a href="{{ route('certificate.view', $certificate->verification_code) }}" 
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-eye mr-2"></i>View Certificate
                </a>
                <button onclick="window.print()" 
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
            </div>
        </div>

        <!-- Certificate Overview -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <!-- Certificate Info -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-certificate mr-3 text-indigo-400"></i>
                    Certificate Details
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Certificate Number:</span>
                        <span class="text-white font-mono">{{ $certificate->certificate_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status:</span>
                        <span class="text-{{ $certificate->status_color }}-400 capitalize">
                            {{ str_replace('_', ' ', $certificate->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Issued Date:</span>
                        <span class="text-white">{{ $certificate->issued_date?->format('M j, Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Grade:</span>
                        <span class="text-white font-semibold">{{ $certificate->grade ?? 'Pass' }}</span>
                    </div>
                </div>
            </div>

            <!-- Student Info -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-user mr-3 text-green-400"></i>
                    Student Information
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Name:</span>
                        <span class="text-white">{{ $certificate->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Email:</span>
                        <span class="text-white">{{ $certificate->user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Completion Date:</span>
                        <span class="text-white">{{ $certificate->completion_date->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Course Info -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-book mr-3 text-blue-400"></i>
                    Course Information
                </h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-400">Course Title:</span>
                        <div class="text-white font-medium mt-1">{{ $certificate->course->title }}</div>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Instructor:</span>
                        <span class="text-white">{{ $certificate->course->instructor->name ?? 'N/A' }}</span>
                    </div>
                    @if($certificate->credits)
                    <div class="flex justify-between">
                        <span class="text-gray-400">Credits:</span>
                        <span class="text-white">{{ $certificate->credits }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Verification Logs -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 mb-8">
            <h3 class="text-xl font-semibold text-white mb-6 flex items-center">
                <i class="fas fa-history mr-3 text-yellow-400"></i>
                Verification History
            </h3>

            @if(count($verificationLogs) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date & Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">IP Address</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">User Agent</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($verificationLogs as $log)
                        <tr class="hover:bg-gray-700">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-white">
                                {{ $log['timestamp'] }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($log['type'] === 'verify') bg-blue-900 text-blue-300
                                    @elseif($log['type'] === 'download') bg-green-900 text-green-300
                                    @elseif($log['type'] === 'view') bg-purple-900 text-purple-300
                                    @else bg-gray-700 text-gray-300 @endif">
                                    {{ ucfirst($log['type']) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">
                                {{ $log['ip'] }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-300 max-w-xs truncate">
                                {{ $log['user_agent'] }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $log['user'] ?? 'Anonymous' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-chart-line text-4xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-lg">No verification logs available</p>
                <p class="text-gray-500 text-sm">Verification logs will appear here once the certificate is accessed</p>
            </div>
            @endif
        </div>

        <!-- Status Timeline -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-xl font-semibold text-white mb-6 flex items-center">
                <i class="fas fa-timeline mr-3 text-indigo-400"></i>
                Certificate Status History
            </h3>
            
            <div class="space-y-6">
                <!-- Requested -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-4 h-4 bg-blue-500 rounded-full mt-2"></div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-medium">Certificate Requested</h4>
                            <span class="text-gray-400 text-sm">{{ $certificate->requested_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <p class="text-gray-400 text-sm mt-1">Student submitted certificate request</p>
                    </div>
                </div>

                @if($certificate->approved_at)
                <!-- Approved -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-4 h-4 bg-green-500 rounded-full mt-2"></div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-medium">Certificate Approved</h4>
                            <span class="text-gray-400 text-sm">{{ $certificate->approved_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <p class="text-gray-400 text-sm mt-1">
                            Approved by {{ $certificate->approver->name ?? 'System' }}
                        </p>
                    </div>
                </div>
                @endif

                @if($certificate->rejected_at)
                <!-- Rejected -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-4 h-4 bg-red-500 rounded-full mt-2"></div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-medium">Certificate Rejected</h4>
                            <span class="text-gray-400 text-sm">{{ $certificate->rejected_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <p class="text-gray-400 text-sm mt-1">
                            Rejected by {{ $certificate->rejecter->name ?? 'System' }}
                        </p>
                        @if($certificate->rejection_reason)
                        <div class="mt-2 p-3 bg-red-900/20 rounded-lg border border-red-900/30">
                            <p class="text-red-300 text-sm">{{ $certificate->rejection_reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($certificate->revoked_at)
                <!-- Revoked -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-4 h-4 bg-gray-500 rounded-full mt-2"></div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-medium">Certificate Revoked</h4>
                            <span class="text-gray-400 text-sm">{{ $certificate->revoked_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <p class="text-gray-400 text-sm mt-1">
                            Revoked by {{ $certificate->revoker->name ?? 'System' }}
                        </p>
                        @if($certificate->revocation_reason)
                        <div class="mt-2 p-3 bg-gray-900/50 rounded-lg border border-gray-700">
                            <p class="text-gray-300 text-sm">{{ $certificate->revocation_reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>