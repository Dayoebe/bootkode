{{-- Email Templates --}}
{{-- resources/views/emails/newsletter.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .header {
            background-color: #1f2937;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
        }

        .footer {
            background-color: #f3f4f6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }

        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }

            .content {
                padding: 15px !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $campaign->from_name }}</h1>
        </div>

        <div class="content">
            {!! $content !!}
        </div>

        <div class="footer">
            <p>You received this email because you're subscribed to our newsletter.</p>
            <p>
                <a href="{{ $unsubscribe_url }}">Unsubscribe</a> |
                <a href="{{ $preferences_url }}">Update Preferences</a> |
                <a href="{{ $view_online_url }}">View Online</a>
            </p>
            <p>© {{ date('Y') }} {{ $campaign->from_name }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>