<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
    <style>
        /* Reset styles */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
        
        /* General styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        /* Header styles */
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        
        .header.passed {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .header.failed {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .header-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }
        
        .header-title {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .header-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin: 5px 0;
        }
        
        /* Content styles */
        .content {
            padding: 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        
        /* Score card */
        .score-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            border: 3px solid #3b82f6;
        }
        
        .score-card.passed {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-color: #10b981;
        }
        
        .score-card.failed {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-color: #ef4444;
        }
        
        .score-number {
            font-size: 72px;
            font-weight: bold;
            line-height: 1;
            margin: 10px 0;
        }
        
        .score-number.passed { color: #059669; }
        .score-number.failed { color: #dc2626; }
        
        .score-label {
            font-size: 16px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        /* Stats grid */
        .stats-grid {
            display: table;
            width: 100%;
            margin: 25px 0;
            border-collapse: separate;
            border-spacing: 10px;
        }
        
        .stat-item {
            display: table-cell;
            background-color: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 25%;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            display: block;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            margin-top: 5px;
            display: block;
        }
        
        /* Insights section */
        .insight-card {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .insight-card.excellent {
            background-color: #d1fae5;
            border-left-color: #10b981;
        }
        
        .insight-card.improvement {
            background-color: #fee2e2;
            border-left-color: #ef4444;
        }
        
        .insight-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        
        .insight-title {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .insight-message {
            color: #4b5563;
            font-size: 14px;
        }
        
        /* Question section */
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .question-card {
            background-color: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-left: 4px solid #6b7280;
        }
        
        .question-card.correct {
            background-color: #f0fdf4;
            border-left-color: #10b981;
        }
        
        .question-card.incorrect {
            background-color: #fef2f2;
            border-left-color: #ef4444;
        }
        
        .question-number {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 14px;
            margin-right: 10px;
        }
        
        .question-text {
            color: #1f2937;
            font-size: 15px;
            margin: 10px 0;
            line-height: 1.6;
        }
        
        .answer-label {
            font-weight: bold;
            color: #4b5563;
            font-size: 13px;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        
        .answer-text {
            color: #1f2937;
            font-size: 14px;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .correct-answer { border-left: 3px solid #10b981; }
        .user-answer.correct { border-left: 3px solid #10b981; }
        .user-answer.incorrect { border-left: 3px solid #ef4444; }
        
        .explanation {
            background-color: #eff6ff;
            border-left: 3px solid #3b82f6;
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 13px;
            color: #1e40af;
        }
        
        /* Certificate section */
        .certificate {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 3px dashed #f59e0b;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        
        .certificate-title {
            font-size: 24px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 15px;
        }
        
        .certificate-id {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #78350f;
            background-color: rgba(255, 255, 255, 0.5);
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
        
        /* Buttons */
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 10px 5px;
            font-size: 14px;
        }
        
        .button.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .button.secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }
        
        /* Footer */
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }
        
        .footer-links {
            margin: 15px 0;
        }
        
        .footer-links a {
            color: #3b82f6;
            text-decoration: none;
            margin: 0 10px;
        }
        
        /* Mobile responsive */
        @media only screen and (max-width: 600px) {
            .stats-grid {
                display: block !important;
            }
            .stat-item {
                display: block !important;
                width: 100% !important;
                margin-bottom: 10px;
            }
            .header-title {
                font-size: 24px;
            }
            .score-number {
                font-size: 56px;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" class="email-container" cellpadding="0" cellspacing="0">
        <!-- Header -->
        <tr>
            <td class="header {{ $results['results']['passed'] ? 'passed' : 'failed' }}">
                <div class="header-icon">
                    @if($results['results']['passed'])
                        🎉
                    @else
                        📊
                    @endif
                </div>
                <div class="header-title">
                    @if($results['results']['passed'])
                        Congratulations!
                    @else
                        Exam Results
                    @endif
                </div>
                <div class="header-subtitle">{{ $results['assessment']['title'] }}</div>
            </td>
        </tr>
        
        <!-- Content -->
        <tr>
            <td class="content">
                <div class="greeting">
                    Dear {{ $results['student']['name'] }},
                </div>
                
                <p style="color: #4b5563; line-height: 1.6;">
                    @if($results['results']['passed'])
                        Excellent work! You have successfully passed the exam <strong>{{ $results['assessment']['title'] }}</strong>.
                        Below are your detailed results and performance insights.
                    @else
                        Thank you for completing <strong>{{ $results['assessment']['title'] }}</strong>. 
                        While you didn't pass this time, we've prepared detailed feedback to help you improve and succeed on your next attempt.
                    @endif
                </p>
                
                <!-- Score Card -->
                <div class="score-card {{ $results['results']['passed'] ? 'passed' : 'failed' }}">
                    <div class="score-label">Your Score</div>
                    <div class="score-number {{ $results['results']['passed'] ? 'passed' : 'failed' }}">
                        {{ $results['results']['percentage'] }}%
                    </div>
                    <div style="margin-top: 10px; color: #4b5563; font-size: 14px;">
                        Pass Mark: {{ $results['assessment']['pass_percentage'] }}%
                    </div>
                </div>
                
                <!-- Statistics -->
                <table class="stats-grid" role="presentation">
                    <tr>
                        <td class="stat-item">
                            <span class="stat-value" style="color: #10b981;">{{ $results['results']['correct_answers'] }}</span>
                            <span class="stat-label">Correct</span>
                        </td>
                        <td class="stat-item">
                            <span class="stat-value" style="color: #ef4444;">{{ $results['results']['total_questions'] - $results['results']['correct_answers'] }}</span>
                            <span class="stat-label">Incorrect</span>
                        </td>
                        <td class="stat-item">
                            <span class="stat-value" style="color: #3b82f6;">{{ $results['statistics']['time_spent'] }}</span>
                            <span class="stat-label">Time Spent</span>
                        </td>
                        <td class="stat-item">
                            <span class="stat-value" style="color: #f59e0b;">{{ $results['statistics']['accuracy_rate'] }}%</span>
                            <span class="stat-label">Accuracy</span>
                        </td>
                    </tr>
                </table>
                
                <!-- Performance Insights -->
                @if(count($results['insights']) > 0)
                <div class="section-title">📊 Performance Insights</div>
                @foreach($results['insights'] as $insight)
                <div class="insight-card {{ $insight['type'] }}">
                    <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td style="width: 40px; vertical-align: top;">
                                <span class="insight-icon">{{ $insight['icon'] }}</span>
                            </td>
                            <td>
                                <div class="insight-title">{{ $insight['title'] }}</div>
                                <div class="insight-message">{{ $insight['message'] }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                @endforeach
                @endif
                
                <!-- Certificate -->
                @if($results['certificate'])
                <div class="certificate">
                    <div class="certificate-title">🏆 Certificate of Completion</div>
                    <p style="color: #78350f; margin: 10px 0;">
                        Congratulations! You've earned a certificate for passing this assessment.
                    </p>
                    <div class="certificate-id">
                        Certificate ID: {{ $results['certificate']['id'] }}
                    </div>
                </div>
                @endif
                
                <!-- Question Breakdown -->
                @if(count($results['questions']['incorrect']) > 0)
                <div class="section-title">❌ Questions You Missed (Review These!)</div>
                @foreach(array_slice($results['questions']['incorrect'], 0, 5) as $index => $question)
                <div class="question-card incorrect">
                    <div>
                        <span class="question-number">{{ $index + 1 }}</span>
                        <strong style="color: #1f2937;">{{ $question['type'] }} ({{ $question['points'] }} pts)</strong>
                    </div>
                    <div class="question-text">{{ $question['text'] }}</div>
                    
                    <div class="answer-label">Your Answer:</div>
                    <div class="answer-text user-answer incorrect">{{ $question['user_answer'] }}</div>
                    
                    <div class="answer-label">Correct Answer:</div>
                    <div class="answer-text correct-answer">{{ $question['correct_answer'] }}</div>
                    
                    @if($question['explanation'])
                    <div class="explanation">
                        <strong>💡 Explanation:</strong> {{ $question['explanation'] }}
                    </div>
                    @endif
                </div>
                @endforeach
                
                @if(count($results['questions']['incorrect']) > 5)
                <p style="text-align: center; color: #6b7280; font-style: italic;">
                    ... and {{ count($results['questions']['incorrect']) - 5 }} more. 
                    <a href="{{ $results['results_url'] }}" style="color: #3b82f6;">View all in your dashboard</a>
                </p>
                @endif
                @endif
                
                @if(count($results['questions']['correct']) > 0)
                <div class="section-title">✅ Questions You Got Right</div>
                <p style="color: #6b7280; margin-bottom: 15px;">
                    Great job! You answered {{ count($results['questions']['correct']) }} question(s) correctly.
                    @if(count($results['questions']['correct']) <= 3)
                        Here's what you mastered:
                    @endif
                </p>
                
                @foreach(array_slice($results['questions']['correct'], 0, 3) as $index => $question)
                <div class="question-card correct">
                    <div>
                        <span class="question-number" style="background-color: #10b981;">{{ $index + 1 }}</span>
                        <strong style="color: #1f2937;">{{ $question['type'] }} ({{ $question['points'] }} pts)</strong>
                    </div>
                    <div class="question-text">{{ $question['text'] }}</div>
                    
                    <div class="answer-label">Your Answer:</div>
                    <div class="answer-text user-answer correct">{{ $question['user_answer'] }}</div>
                    
                    @if($question['explanation'])
                    <div class="explanation">
                        <strong>💡 Why it's correct:</strong> {{ $question['explanation'] }}
                    </div>
                    @endif
                </div>
                @endforeach
                @endif
                
                <!-- Action Buttons -->
                <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%; margin: 30px 0;">
                    <tr>
                        <td style="text-align: center;">
                            @if(!$results['results']['passed'])
                            <a href="{{ $results['exam_url'] }}" class="button success">
                                🔄 Retake Exam
                            </a>
                            @endif
                            <a href="{{ $results['results_url'] }}" class="button">
                                📊 View Full Results
                            </a>
                        </td>
                    </tr>
                </table>
                
                <!-- Recommendations -->
                @if(count($results['recommendations']) > 0)
                <div class="section-title">💡 Recommended Next Steps</div>
                @foreach(array_slice($results['recommendations'], 0, 3) as $recommendation)
                <div style="background-color: #f9fafb; border-radius: 8px; padding: 15px; margin: 10px 0;">
                    <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td style="width: 40px; vertical-align: top; font-size: 24px;">
                                {{ $recommendation['icon'] }}
                            </td>
                            <td>
                                <div style="font-weight: bold; color: #1f2937; margin-bottom: 5px;">
                                    {{ $recommendation['title'] }}
                                </div>
                                <div style="color: #6b7280; font-size: 14px;">
                                    {{ $recommendation['description'] }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                @endforeach
                @endif
                
                <!-- Closing -->
                <p style="color: #4b5563; line-height: 1.6; margin-top: 30px;">
                    @if($results['results']['passed'])
                        Once again, congratulations on your achievement! Keep up the excellent work!
                    @else
                        Remember, every attempt is a learning opportunity. Review the explanations above and you'll do even better next time!
                    @endif
                </p>
                
                <p style="color: #4b5563; line-height: 1.6;">
                    Best regards,<br>
                    <strong>{{ config('app.name') }} Team</strong>
                </p>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td class="footer">
                <div>
                    <strong>{{ config('app.name') }}</strong>
                </div>
                <div style="margin: 10px 0;">
                    Exam completed on {{ $results['submitted_at'] }}
                </div>
                <div class="footer-links">
                    <a href="{{ $results['exam_url'] }}">Take More Exams</a> |
                    <a href="{{ $results['results_url'] }}">View Dashboard</a> |
                    <a href="{{ config('app.url') }}">Visit Website</a>
                </div>
                <div style="margin-top: 15px; font-size: 12px;">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </td>
        </tr>
    </table>
</body>
</html>