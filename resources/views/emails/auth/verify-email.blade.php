<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - {{ $appName }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style type="text/css">
        @media only screen and (max-width: 600px) {
            .responsive-table {
                width: 100% !important;
            }

            .responsive-column {
                display: block !important;
                width: 100% !important;
                padding: 10px 0 !important;
            }

            .email-padding {
                padding: 20px !important;
            }

            .email-header {
                padding: 30px 20px !important;
            }

            .verification-button {
                display: block !important;
                width: 100% !important;
            }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.5;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .profile-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #4f46e5;
        }

        .verification-button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            color: white;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.25);
        }

        .verification-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(79, 70, 229, 0.3);
        }

        .info-box {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
            border-left: 3px solid #e2e8f0;
        }

        .social-links {
            margin-top: 16px;
        }

        .social-links a {
            display: inline-block;
            margin-right: 8px;
            color: #4f46e5;
            text-decoration: none;
        }
    </style>
</head>

<body
    style="margin: 0; padding: 20px; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; background-color: #f8fafc; color: #334155; line-height: 1.5;">
    <!-- Main Container -->
    <div class="email-container"
        style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); overflow: hidden;">
        <!-- Header -->
        <div class="email-header"
            style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 40px 30px; text-align: center; color: white;">
            <i class="fas fa-envelope-open-text" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
            <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 8px 0;">Welcome to {{ $appName }},
                {{ $user->name }}!</h1>
            <p style="margin: 0; opacity: 0.9; font-size: 16px;">Let's verify your email to get started</p>
        </div>

        <!-- Content Area -->
        <div class="email-padding" style="padding: 30px;">
            <!-- Greeting -->
            <h2 style="font-size: 22px; font-weight: 600; margin-top: 0; color: #1e293b;">Hello {{ $user->name }},
            </h2>

            <p style="margin-bottom: 20px; color: #475569;">Thank you for joining {{ $appName }}! We're excited to
                have you as part of our community. To complete your registration, please verify your email address by
                clicking the button below:</p>

            <!-- Verification Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="verification-button"
                    style="display: inline-block; padding: 12px 24px; background: linear-gradient(to right, #4f46e5, #7c3aed); color: white; font-weight: 600; text-decoration: none; border-radius: 8px; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.25);">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i> Verify Email Address
                </a>
            </div>

            <!-- User Profile Card -->
            <div class="profile-card"
                style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); padding: 20px; margin: 20px 0; border-left: 4px solid #4f46e5;">
                <h3 style="margin-top: 0; color: #4f46e5; font-size: 18px;">
                    <i class="fas fa-user-circle" style="margin-right: 8px;"></i> Your Profile Details
                </h3>

                <div style="display: flex; margin-bottom: 16px;">
                    <div style="flex: 1;">
                        <p style="margin: 8px 0;"><strong><i class="fas fa-envelope"
                                    style="margin-right: 8px; color: #4f46e5;"></i> Email:</strong> {{ $user->email }}
                        </p>
                        @if ($user->phone_number)
                            <p style="margin: 8px 0;"><strong><i class="fas fa-phone"
                                        style="margin-right: 8px; color: #4f46e5;"></i> Phone:</strong>
                                {{ $user->phone_number }}</p>
                        @endif
                        @if ($user->date_of_birth)
                            <p style="margin: 8px 0;"><strong><i class="fas fa-birthday-cake"
                                        style="margin-right: 8px; color: #4f46e5;"></i> Age:</strong>
                                {{ $user->date_of_birth->age }} years</p>
                        @endif
                    </div>
                    <div style="flex: 1;">
                        @if ($user->occupation)
                            <p style="margin: 8px 0;"><strong><i class="fas fa-briefcase"
                                        style="margin-right: 8px; color: #4f46e5;"></i> Occupation:</strong>
                                {{ $user->occupation }}</p>
                        @endif
                        @if ($user->education_level)
                            <p style="margin: 8px 0;"><strong><i class="fas fa-graduation-cap"
                                        style="margin-right: 8px; color: #4f46e5;"></i> Education:</strong>
                                {{ $user->education_level }}</p>
                        @endif
                        @if ($user->role)
                            <p style="margin: 8px 0;"><strong><i class="fas fa-user-tag"
                                        style="margin-right: 8px; color: #4f46e5;"></i> Role:</strong>
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                        @endif
                    </div>
                </div>

                @if ($user->full_address)
                    <p style="margin: 8px 0;"><strong><i class="fas fa-map-marker-alt"
                                style="margin-right: 8px; color: #4f46e5;"></i> Address:</strong>
                        {{ $user->full_address }}</p>
                @endif

                @if ($user->skills)
                    <p style="margin: 8px 0;"><strong><i class="fas fa-lightbulb"
                                style="margin-right: 8px; color: #4f46e5;"></i> Skills:</strong> {{ $user->skills }}
                    </p>
                @endif

                @if ($user->bio)
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                        <p style="margin: 8px 0;"><strong><i class="fas fa-info-circle"
                                    style="margin-right: 8px; color: #4f46e5;"></i> Bio:</strong></p>
                        <p style="margin: 8px 0; font-style: italic;">{{ $user->bio }}</p>
                    </div>
                @endif
            </div>

            <!-- Next Steps -->
            <div class="info-box"
                style="background-color: #f8fafc; border-radius: 8px; padding: 16px; margin: 16px 0; border-left: 3px solid #e2e8f0;">
                <h3 style="margin-top: 0; color: #4f46e5; font-size: 18px;">
                    <i class="fas fa-rocket" style="margin-right: 8px;"></i> What's Next?
                </h3>
                <ul style="margin: 8px 0; padding-left: 20px;">
                    <li style="margin-bottom: 8px;">Complete your profile to get personalized recommendations</li>
                    <li style="margin-bottom: 8px;">Explore courses and resources tailored to your interests</li>
                    <li style="margin-bottom: 8px;">Connect with other members in your field</li>
                    @if ($user->hasRole('student'))
                        <li>Access your student dashboard to track your learning progress</li>
                    @elseif($user->hasRole('instructor'))
                        <li>Set up your instructor profile to start creating content</li>
                    @endif
                </ul>
            </div>

            <!-- Alternative Link -->
            <div class="info-box"
                style="background-color: #f8fafc; border-radius: 8px; padding: 16px; margin: 16px 0; border-left: 3px solid #e2e8f0;">
                <p style="margin: 8px 0;"><strong><i class="fas fa-link" style="margin-right: 8px; color: #4f46e5;"></i>
                        Verification Link:</strong></p>
                <div
                    style="background-color: #f1f5f9; padding: 12px; border-radius: 6px; word-break: break-all; font-family: monospace; font-size: 14px; color: #334155;">
                    {{ $verificationUrl }}
                </div>
                <p style="margin: 8px 0; font-size: 14px; color: #64748b;">If the button doesn't work, copy and paste
                    this link into your browser.</p>
            </div>

            <!-- Security Notice -->
            <div
                style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 0 8px 8px 0; margin-top: 24px;">
                <h3 style="font-weight: 600; color: #92400e; margin-top: 0; font-size: 16px;">
                    <i class="fas fa-shield-alt" style="margin-right: 8px;"></i> Security Notice
                </h3>
                <ul style="margin: 8px 0; padding-left: 20px; color: #92400e;">
                    <li style="margin-bottom: 4px;">This link will expire in 60 minutes</li>
                    <li style="margin-bottom: 4px;">Never share your verification link with anyone</li>
                    <li>If you didn't request this, please ignore this email</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div
            style="padding: 20px; text-align: center; font-size: 14px; color: #64748b; border-top: 1px solid #e2e8f0; background-color: #f8fafc;">
            <p style="margin: 0 0 8px 0;">© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>

            <div style="margin-bottom: 8px;">
                @if ($user->social_links)
                    <div class="social-links" style="margin-top: 16px;">
                        @foreach ($user->social_links as $platform => $url)
                            <a href="{{ $url }}" target="_blank"
                                style="display: inline-block; margin-right: 8px; color: #4f46e5; text-decoration: none;">
                                <i class="fab fa-{{ strtolower($platform) }}" style="font-size: 18px;"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Add role-based content if needed, but use hasRole -->
            @if ($user->hasRole('student'))
                <p style="margin-top: 8px; color: #4f46e5;">Special tip for students: Check your dashboard for courses!
                </p>
            @endif

            <div>
                <a href="#" style="color: #64748b; text-decoration: none; margin: 0 8px;">Help Center</a>
                <span>•</span>
                <a href="#" style="color: #64748b; text-decoration: none; margin: 0 8px;">Privacy Policy</a>
                <span>•</span>
                <a href="#" style="color: #64748b; text-decoration: none; margin: 0 8px;">Contact Us</a>
            </div>
        </div>
    </div>
</body>

</html>
