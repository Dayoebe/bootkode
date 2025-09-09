dd
{{-- Unsubscribe Page --}}
{{-- resources/views/newsletter/unsubscribe.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['title'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <i class="fas fa-envelope-open text-4xl text-gray-400 mb-4"></i>
                <h2 class="text-3xl font-extrabold text-gray-900">
                    {{ $settings['title'] }}
                </h2>
                <p class="mt-4 text-gray-600">
                    {{ $settings['message'] }}
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-4">Email: {{ $subscriber->email }}</p>
                    
<!-- In unsubscribe.blade.php -->
@if($subscriber->status === 'unsubscribed')
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded">
        <i class="fas fa-check-circle text-green-600 mr-2"></i>
        <span class="text-green-700">Successfully unsubscribed</span>
    </div>
    
    <p class="text-sm text-gray-600 mb-4">
        {{ $settings['resubscribe_text'] }}
    </p>
    
    <form method="POST" action="{{ route('newsletter.resubscribe', $subscriber->unsubscribe_token) }}">
        @csrf
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
            Resubscribe
        </button>
    </form>
@else
    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
        <span class="text-yellow-700">Already unsubscribed</span>
    </div>
@endif
                </div>
            </div>

            <div class="text-center">
                <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800">
                    Return to {{ config('app.name') }}
                </a>
            </div>
        </div>
    </div>
</body>
</html>
