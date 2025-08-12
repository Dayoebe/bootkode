<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .certificate-container {
            width: 100%;
            height: 100vh;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .certificate-border {
            border: 10px solid #2d3748;
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
        }
        h1 {
            font-size: 3rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        h2 {
            font-size: 2rem;
            color: #4a5568;
            margin-top: 0;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.25rem;
            color: #4a5568;
            margin-bottom: 0.5rem;
        }
        .name {
            font-size: 3.5rem;
            font-weight: bold;
            color: #4338ca;
            margin: 1rem 0;
            font-style: italic;
        }
        .course-title {
            font-size: 2rem;
            font-weight: 600;
            margin: 1rem 0;
        }
        .issued-date {
            font-size: 1rem;
            color: #718096;
            margin-top: 2rem;
        }
        .uuid {
            font-size: 0.9rem;
            color: #a0aec0;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <h1>Certificate of Completion</h1>
            <p>This is to certify that</p>
            <p class="name">{{ $certification->user->name }}</p>
            <p>has successfully completed the course</p>
            <p class="course-title">{{ $certification->course->title }}</p>
            <p class="issued-date">Issued on: {{ $certification->completion_date->format('F d, Y') }}</p>
            <p class="uuid">Certificate ID: {{ $certification->certificate_uuid }}</p>
        </div>
    </div>
    
    <h1>Certificate for {{ $certificate->course->title }}</h1>
    <p>Issued to: {{ $certificate->user->name }}</p>
    <p>Date: {{ $certificate->issue_date }}</p>
</body>
</html>