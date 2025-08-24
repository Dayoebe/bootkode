
<?php
// resources/views/certificates/widget-invalid.blade.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-question-circle text-gray-400 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-bold text-gray-800">Certificate Not Found</h3>
                    <p class="text-sm text-gray-600">Invalid verification code</p>
                </div>
            </div>
            
            <p class="text-sm text-gray-600 mb-4">
                The certificate verification code provided could not be found in our system.
            </p>
            
            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('certificate.verify') }}" 
                   target="_blank"
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded text-sm transition-colors block">
                    Verify Certificate
                </a>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-500">
                Powered by <a href="{{ config('app.url') }}" class="text-blue-600 hover:underline">Academy Learning Platform</a>
            </p>
        </div>
    </div>
</body>
</html>