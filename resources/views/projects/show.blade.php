<x-app-layout title="Project Details" description="View project details and submissions" icon="fas fa-project-diagram" active="student.projects">
    <div class="bg-gray-800 p-6 rounded-lg shadow-xl text-white max-w-4xl mx-auto my-8 animate__animated animate__fadeIn">
        <h2 class="text-3xl font-extrabold text-teal-400 mb-6">{{ $project->title }}</h2>
        <p class="text-gray-300 mb-4">{{ $project->description }}</p>
        <div class="mb-4">
            <span class="inline-block bg-indigo-600/20 text-indigo-400 px-2 py-1 rounded-full text-sm">
                Course: {{ $project->course->title }}
            </span>
        </div>

        <h3 class="text-xl font-semibold text-white mt-6 mb-4">Submissions</h3>
        @forelse ($project->submissions as $submission)
            <div class="bg-gray-700 p-4 rounded-xl mb-4">
                <div class="flex justify-between">
                    <h4 class="text-lg font-medium text-green-400">{{ $submission->title }}</h4>
                    @if ($submission->is_featured)
                        <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                            <i class="fas fa-star mr-1"></i> Featured
                        </span>
                    @endif
                </div>
                <p class="text-gray-300 mt-2">{{ $submission->description }}</p>
                <div class="mt-2">
                    <span class="inline-block bg-green-600/20 text-green-400 px-2 py-1 rounded-full text-sm">
                        Status: {{ $submission->status }}
                    </span>
                    @if ($submission->grade)
                        <span class="inline-block bg-blue-600/20 text-blue-400 px-2 py-1 rounded-full text-sm ml-2">
                            Grade: {{ $submission->grade }}
                        </span>
                    @endif
                </div>
                @if ($submission->files)
                    <div class="mt-3">
                        <h5 class="text-sm font-medium text-gray-300">Files:</h5>
                        <ul class="list-disc pl-5 text-gray-400">
                            @foreach ($submission->files as $file)
                                <li><a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-teal-400 hover:underline">{{ basename($file) }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if ($submission->feedback)
                    <div class="mt-3">
                        <h5 class="text-sm font-medium text-gray-300">Feedback:</h5>
                        <p class="text-gray-400">{{ $submission->feedback }}</p>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-400">No submissions yet.</p>
        @endforelse
    </div>
</x-app-layout>