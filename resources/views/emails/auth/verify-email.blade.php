<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - {{ $appName }}</title>
    <!-- Load Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS for Email -->
    <style type="text/css">
        .email-bg { background-color: #f3f4f6; }
        .email-container { max-width: 600px; }
        .email-header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
    </style>
</head>
<body class="email-bg email:p-5" style="margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <!-- Main Container -->
    <div class="email-container email:mx-auto email:bg-white email:rounded-lg email:shadow-sm email:overflow-hidden" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <!-- Header with Gradient Background -->
        <div class="email-header email:py-10 email:px-6 email:text-center email:text-white" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); padding: 2.5rem 1.5rem; text-align: center; color: white;">
            <i class="fas fa-envelope-open-text email:text-5xl email:mb-5" style="font-size: 3rem; margin-bottom: 1.25rem;"></i>
            <h1 class="email:text-3xl email:font-bold email:m-0" style="font-size: 1.875rem; font-weight: 700; margin: 0;">Verify Your Email</h1>
            <p class="email:mt-2 email:opacity-90" style="margin-top: 0.5rem; opacity: 0.9;">Welcome to {{ $appName }}!</p>
        </div>

        <!-- Content Area -->
        <div class="email:px-6 email:py-8" style="padding: 1.5rem 2rem;">
            <h2 class="email:text-2xl email:font-semibold email:mt-0 email:text-gray-900" style="font-size: 1.5rem; font-weight: 600; margin-top: 0; color: #111827;">Hello {{ $user->name }},</h2>
            
            <p class="email:text-gray-700 email:mb-6" style="color: #374151; margin-bottom: 1.5rem;">Thank you for registering with {{ $appName }}. To complete your registration, please verify your email address by clicking the button below:</p>
            
            <!-- Verification Button -->
            <div class="email:text-center email:my-8" style="text-align: center; margin: 2rem 0;">
                <a href="{{ $verificationUrl }}" class="email:inline-flex email:items-center email:px-8 email:py-3 email:bg-gradient-to-r email:from-indigo-600 email:to-purple-600 email:text-white email:font-semibold email:rounded-full email:shadow-md email:hover:scale-105 email:transition-transform" style="display: inline-flex; align-items: center; padding: 0.75rem 2rem; background: linear-gradient(to right, #4f46e5, #7c3aed); color: white; font-weight: 600; border-radius: 9999px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); text-decoration: none; transition: transform 0.2s;">
                    <i class="fas fa-check-circle email:mr-2" style="margin-right: 0.5rem;"></i> Verify Email Address
                </a>
            </div>
            
            <!-- Email Address Confirmation -->
            <div class="email:bg-indigo-50 email:px-4 email:py-3 email:rounded-lg email:mb-6" style="background-color: #e0e7ff; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <p class="email:m-0" style="margin: 0;">
                    <i class="fas fa-envelope email:mr-2 email:text-indigo-500" style="margin-right: 0.5rem; color: #6366f1;"></i>
                    <strong>Name:</strong> {{ $user->name }}
                </p>
                <p class="email:m-0" style="margin: 0;">
                    <i class="fas fa-envelope email:mr-2 email:text-indigo-500" style="margin-right: 0.5rem; color: #6366f1;"></i>
                    <strong>Email:</strong> {{ $user->email }}
                </p>
            </div>
            
            <!-- Alternative Link -->
            <p class="email:text-gray-700 email:mb-2" style="color: #374151; margin-bottom: 0.5rem;">If the button doesn't work, copy and paste this link into your browser:</p>
            <div class="email:bg-gray-100 email:px-4 email:py-3 email:rounded email:break-all email:font-mono email:text-sm" style="background-color: #f3f4f6; padding: 0.75rem 1rem; border-radius: 0.25rem; word-break: break-all; font-family: monospace; font-size: 0.875rem;">
                {{ $verificationUrl }}
            </div>
            
            <!-- Security Notice -->
            <div class="email:bg-yellow-50 email:border-l-4 email:border-yellow-400 email:px-4 email:py-3 email:rounded-r-lg email:mt-8" style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 0.75rem 1rem; border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; margin-top: 2rem;">
                <h3 class="email:font-medium email:text-yellow-800 email:mt-0" style="font-weight: 500; color: #92400e; margin-top: 0;">
                    <i class="fas fa-shield-alt email:mr-2" style="margin-right: 0.5rem;"></i> Security Notice
                </h3>
                <ul class="email:list-disc email:list-inside email:text-yellow-700 email:space-y-1 email:pl-1" style="list-style-type: disc; list-style-position: inside; color: #92400e; padding-left: 0.25rem;">
                    <li>This link will expire in 60 minutes</li>
                    <li>If you didn't request this, please ignore this email</li>
                    <li>Never share your verification link with anyone</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="email:px-6 email:py-4 email:text-center email:text-xs email:text-gray-500 email:border-t email:border-gray-200" style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: #6b7280; border-top: 1px solid #e5e7eb;">
            <p class="email:m-0" style="margin: 0;">© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <p class="email:mt-1 email:m-0" style="margin: 0.25rem 0 0;">
                <a href="#" class="email:text-gray-500 email:hover:text-gray-700" style="color: #6b7280; text-decoration: none;">Help Center</a> | 
                <a href="#" class="email:text-gray-500 email:hover:text-gray-700" style="color: #6b7280; text-decoration: none;">Privacy Policy</a> | 
                <a href="#" class="email:text-gray-500 email:hover:text-gray-700" style="color: #6b7280; text-decoration: none;">Contact Us</a>
            </p>
        </div>
    </div>
</body>
</html>