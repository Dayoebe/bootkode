<?php
// resources/views/student/certificates/index.blade.php
?>
<x-app-layout>
<div class="min-h-screen bg-gray-900 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">My Certificates</h1>
                <p class="text-gray-300 mt-2">View and manage your earned certificates</p>
            </div>
            
            <a href="{{ route('student.certificate.request') }}" 
               class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Request Certificate
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-3xl mr-4"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ auth()->user()->certificates()->approved()->count() }}</div>
                        <div class="text-sm opacity-90">Approved</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 rounded-xl p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-clock text-3xl mr-4"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ auth()->user()->certificates()->requested()->count() }}</div>
                        <div class="text-sm opacity-90">Pending</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-red-600 to-pink-600 rounded-xl p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-3xl mr-4"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ auth()->user()->certificates()->rejected()->count() }}</div>
                        <div class="text-sm opacity-90">Rejected</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-certificate text-3xl mr-4"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ auth()->user()->certificates()->count() }}</div>
                        <div class="text-sm opacity-90">Total</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse(auth()->user()->certificates()->with(['course', 'course.instructor'])->latest()->get() as $certificate)
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition-colors">
                <!-- Status Badge -->
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($certificate->status === 'approved') bg-green-900 text-green-300
                        @elseif($certificate->status === 'requested') bg-yellow-900 text-yellow-300
                        @elseif($certificate->status === 'rejected') bg-red-900 text-red-300
                        @elseif($certificate->status === 'revoked') bg-gray-700 text-gray-300
                        @else bg-blue-900 text-blue-300 @endif">
                        <i class="{{ $certificate->status_icon }} mr-1.5"></i>
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
                    <h3 class="text-lg font-semibold text-white mb-2">{{ $certificate->course->title }}</h3>
                    <p class="text-gray-400 text-sm">{{ $certificate->course->instructor->name ?? 'N/A' }}</p>
                </div>

                <!-- Certificate Details -->
                <div class="space-y-2 text-sm mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Certificate #:</span>
                        <span class="text-white font-mono text-xs">{{ $certificate->certificate_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Completion:</span>
                        <span class="text-white">{{ $certificate->completion_date->format('M j, Y') }}</span>
                    </div>
                    @if($certificate->issued_date)
                    <div class="flex justify-between">
                        <span class="text-gray-400">Issued:</span>
                        <span class="text-white">{{ $certificate->issued_date->format('M j, Y') }}</span>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    @if($certificate->isApproved() && $certificate->pdf_path)
                    <a href="{{ route('certificate.download', $certificate->verification_code) }}" 
                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded-lg text-sm transition-colors">
                        <i class="fas fa-download mr-1"></i>Download
                    </a>
                    <a href="{{ route('certificate.view', $certificate->verification_code) }}" 
                       target="_blank"
                       class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center py-2 px-3 rounded-lg text-sm transition-colors">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    @elseif($certificate->isRequested())
                    <div class="flex-1 bg-yellow-600 text-white text-center py-2 px-3 rounded-lg text-sm">
                        <i class="fas fa-clock mr-1"></i>Pending Review
                    </div>
                    @elseif($certificate->isRejected())
                    <div class="flex-1 bg-red-600 text-white text-center py-2 px-3 rounded-lg text-sm">
                        <i class="fas fa-times mr-1"></i>Rejected
                    </div>
                    @endif
                </div>

                @if($certificate->isRejected() && $certificate->rejection_reason)
                <div class="mt-3 p-2 bg-red-900/20 border border-red-900/30 rounded text-xs text-red-300">
                    <strong>Reason:</strong> {{ $certificate->rejection_reason }}
                </div>
                @endif
            </div>
            @empty
            <!-- Empty State -->
            <div class="col-span-full">
                <div class="bg-gray-800 rounded-xl p-12 text-center border border-gray-700">
                    <i class="fas fa-certificate text-6xl text-gray-600 mb-6"></i>
                    <h3 class="text-xl font-semibold text-white mb-4">No Certificates Yet</h3>
                    <p class="text-gray-400 mb-8">Complete courses to earn certificates and showcase your achievements.</p>
                    <a href="{{ route('student.course-catalog') }}" 
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                        <i class="fas fa-book mr-2"></i>
                        Explore Courses
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
</x-app-layout>