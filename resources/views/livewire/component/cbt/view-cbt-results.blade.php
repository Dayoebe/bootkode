<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-chart-bar mr-2"></i> CBT Results
    </h1>
    <div class="bg-white rounded-xl shadow-lg p-6 animate__animated animate__fadeIn">
        @if(count($results) > 0)
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left text-gray-600">Exam</th>
                            <th class="px-4 py-2 text-left text-gray-600">Score</th>
                            <th class="px-4 py-2 text-left text-gray-600">Percentage</th>
                            <th class="px-4 py-2 text-left text-gray-600">Status</th>
                            <th class="px-4 py-2 text-left text-gray-600">Completed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr class="border-b border-gray-200">
                                <td class="px-4 py-2">{{ $result['exam_title'] }}</td>
                                <td class="px-4 py-2">{{ $result['score'] }} / {{ $result['total_marks'] }}</td>
                                <td class="px-4 py-2">{{ number_format($result['percentage'], 2) }}%</td>
                                <td class="px-4 py-2">
                                    <span class="{{ $result['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $result['passed'] ? 'Passed' : 'Failed' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ $result['completed_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center">No results available.</p>
        @endif
    </div>
</div>