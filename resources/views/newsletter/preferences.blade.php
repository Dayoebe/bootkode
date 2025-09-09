{{-- Preferences Page --}}
{{-- resources/views/newsletter/preferences.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['title'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <i class="fas fa-cog text-4xl text-blue-400 mb-4"></i>
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
                    
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <span class="text-blue-700">Your current subscription status: {{ ucfirst($subscriber->status) }}</span>
                    </div>
                    
                    <div class="space-y-4">
                        <form method="POST" action="{{ route('newsletter.resubscribe', $subscriber->unsubscribe_token) }}">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Resubscribe to Newsletter
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Unsubscribe from Newsletter
                            </button>
                        </form>
                    </div>
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