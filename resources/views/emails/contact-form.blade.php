<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        /* Inline styles for email compatibility (mimic Tailwind) */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f9ff;
            color: #1e40af;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #15803d;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .content {
            padding: 20px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #6b7280;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        p {
            margin: 10px 0;
            font-size: 16px;
        }

        strong {
            color: #1e40af;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header with Branding -->
        <div class="header">
            <h1>BootKode Contact Form Submission</h1>
            <p>Empowering Africa's Youth with Digital Skills</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p><strong>Name:</strong> {{ $name }}</p>
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Message:</strong> {{ $userMessage }}</p> <!-- Renamed variable to avoid conflict -->
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} BootKode. All rights reserved.</p>
            <p>Contact: oyetoke.ebenezer@gmail.com | +234 903 003 6438</p>
        </div>
    </div>
</body>

</html>
