<?php
// resources/views/certificates/verification-widget.blade.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4">
        @if($verificationData['valid'])
            <!-- Valid Certificate Widget -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-bold text-gray-800">Certificate Verified</h3>
                        <p class="text-sm text-gray-600">Valid & Authentic</p>
                    </div>
                </div>
                
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Holder:</span>
                        <span class="font-medium">{{ $verificationData['certificate']['student_name'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Course:</span>
                        <span class="font-medium">{{ Str::limit($verificationData['certificate']['course_title'], 30) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Issued:</span>
                        <span class="font-medium">{{ $verificationData['certificate']['issued_date'] }}</span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <a href="{{ route('certificate.view', $certificate->verification_code) }}" 
                       target="_blank"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded text-sm transition-colors block">
                        View Full Certificate
                    </a>
                </div>
            </div>
        @else
            <!-- Invalid Certificate Widget -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-times-circle text-red-500 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-bold text-gray-800">Invalid Certificate</h3>
                        <p class="text-sm text-gray-600">Cannot be verified</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 mb-4">
                    This certificate could not be verified. It may have been revoked or the verification code is incorrect.
                </p>
                
                <div class="mt-4 pt-4 border-t">
                    <a href="{{ route('certificate.verify') }}" 
                       target="_blank"
                       class="w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-4 rounded text-sm transition-colors block">
                        Verify Certificate
                    </a>
                </div>
            </div>
        @endif
        
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-500">
                Powered by <a href="{{ config('app.url') }}" class="text-blue-600 hover:underline">Academy Learning Platform</a>
            </p>
        </div>
    </div>
</body>
</html>
