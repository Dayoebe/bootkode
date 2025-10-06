<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribed from Review Reminders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Unsubscribed Successfully</h1>
            <p class="text-gray-600 mb-6">
                You won't receive any more review reminders for <strong>{{ $course->title }}</strong>.
            </p>
            <a href="{{ route('course.view', $course->slug) }}" 
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Back to Course
            </a>
        </div>
    </div>
</body>
</html>