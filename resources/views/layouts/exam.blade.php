{{-- resources/views/layouts/exam.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CBT System') }} - Exam Mode</title>

    {{-- Prevent caching --}}
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    {{-- Security Headers --}}
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="referrer" content="no-referrer">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Custom Exam Styles --}}
    <style>
        /* Disable text selection globally during exam */
        body.exam-mode {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            font-family: 'Inter', sans-serif;
        }

        /* Disable image dragging */
        body.exam-mode img {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
            pointer-events: none;
        }

        /* Disable right-click context menu */
        body.exam-mode {
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
        }

        /* Disable highlighting */
        body.exam-mode *::selection {
            background: transparent;
        }

        body.exam-mode *::-moz-selection {
            background: transparent;
        }

        /* Hide scrollbars but maintain functionality */
        body.exam-mode::-webkit-scrollbar {
            width: 4px;
        }

        body.exam-mode::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        body.exam-mode::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }

        /* Fullscreen styles */
        .fullscreen-mode {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: white !important;
            z-index: 9999 !important;
        }

        /* Disable print styles */
        @media print {
            body {
                display: none !important;
            }
        }

        /* Security warning overlay */
        .security-warning {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(239, 68, 68, 0.95);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Timer warning animation */
        .timer-critical {
            animation: pulse-red 1s infinite;
        }

        @keyframes pulse-red {

            0%,
            100% {
                background-color: rgb(254 226 226);
                color: rgb(185 28 28);
            }

            50% {
                background-color: rgb(239 68 68);
                color: white;
            }
        }

        /* Disable zoom */
        body.exam-mode {
            touch-action: manipulation;
        }

        /* Custom focus styles for accessibility */
        body.exam-mode button:focus,
        body.exam-mode input:focus,
        body.exam-mode textarea:focus,
        body.exam-mode select:focus {
            outline: 2px solid #3B82F6 !important;
            outline-offset: 2px !important;
        }

        /* Disable browser autofill styling */
        body.exam-mode input:-webkit-autofill,
        body.exam-mode input:-webkit-autofill:hover,
        body.exam-mode input:-webkit-autofill:focus,
        body.exam-mode input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            -webkit-text-fill-color: black !important;
        }
    </style>

    @stack('styles')
</head>

<body class="h-full bg-gray-50 exam-mode" id="examBody">
    {{-- Security Warning Container --}}
    <div id="securityWarnings"></div>

    {{-- Fullscreen Prompt --}}
    <div id="fullscreenPrompt"
        class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center text-white"
        style="display: none;">
        <div class="text-center p-8">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-expand text-red-500 text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-4">Fullscreen Required</h2>
            <p class="text-lg mb-6">This exam must be taken in fullscreen mode for security purposes.</p>
            <button onclick="enterFullscreen()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg text-lg font-semibold">
                Enter Fullscreen
            </button>
        </div>
    </div>

    {{-- Exit Fullscreen Warning --}}
    <div id="fullscreenExitWarning"
        class="fixed inset-0 bg-red-600 bg-opacity-95 z-50 flex items-center justify-center text-white"
        style="display: none;">
        <div class="text-center p-8">
            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold mb-4">SECURITY VIOLATION</h2>
            <p class="text-xl mb-2">You have exited fullscreen mode!</p>
            <p class="text-lg mb-6">Please return to fullscreen immediately or your exam may be terminated.</p>
            <button onclick="enterFullscreen()"
                class="bg-white text-red-600 px-8 py-3 rounded-lg text-lg font-bold hover:bg-gray-100">
                Return to Fullscreen
            </button>
            <p class="text-sm mt-4 opacity-75">This incident has been logged for security purposes.</p>
        </div>
    </div>

    {{-- Main Content --}}
    <main class="h-full">
        {{ $slot }}
    </main>

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Exam Security & Monitoring Scripts --}}
    <script>
        let securityViolationCount = 0;
        let fullscreenExitCount = 0;
        let examStarted = false;
        let securityMonitoringActive = false;

        // Initialize security monitoring
        document.addEventListener('DOMContentLoaded', function () {
            initializeSecurityMonitoring();
        });

        function initializeSecurityMonitoring() {
            if (securityMonitoringActive) return;
            securityMonitoringActive = true;

            // Disable right-click context menu
            document.addEventListener('contextmenu', function (e) {
                e.preventDefault();
                showSecurityWarning('Right-click is disabled during exam');
                return false;
            });

            // Disable keyboard shortcuts
            document.addEventListener('keydown', function (e) {
                // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, etc.
                if (
                    e.key === 'F12' ||
                    (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
                    (e.ctrlKey && e.key === 'U') ||
                    (e.ctrlKey && e.key === 'S') ||
                    (e.ctrlKey && e.key === 'A') ||
                    (e.ctrlKey && e.key === 'P') ||
                    (e.metaKey && e.key === 'S') ||
                    (e.metaKey && e.key === 'P')
                ) {
                    e.preventDefault();
                    securityViolationCount++;
                    showSecurityWarning('Keyboard shortcut blocked - Security violation #' + securityViolationCount);

                    if (securityViolationCount >= 5) {
                        showSecurityWarning('Multiple security violations detected! Exam may be terminated.');
                        // Emit event to Livewire component
                        if (window.Livewire) {
                            Livewire.emit('securityViolation', 'multiple_keyboard_attempts');
                        }
                    }
                    return false;
                }

                // Allow specific navigation keys for the exam
                const allowedKeys = [
                    'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight',
                    'Tab', 'Enter', 'Escape', 'Backspace', 'Delete',
                    'Home', 'End', 'PageUp', 'PageDown'
                ];

                if (e.altKey && (e.key === 'ArrowLeft' || e.key === 'ArrowRight')) {
                    // Allow Alt+Arrow for navigation in exam
                    return true;
                }
            });

            // Disable text selection
            document.addEventListener('selectstart', function (e) {
                e.preventDefault();
                return false;
            });

            // Disable drag operations
            document.addEventListener('dragstart', function (e) {
                e.preventDefault();
                return false;
            });

            // Monitor copy/paste attempts
            document.addEventListener('copy', function (e) {
                e.preventDefault();
                showSecurityWarning('Copy operation blocked');
                return false;
            });

            document.addEventListener('paste', function (e) {
                // Allow paste in answer input fields
                if (e.target.tagName === 'TEXTAREA' || (e.target.tagName === 'INPUT' && e.target.type === 'text')) {
                    return true;
                }
                e.preventDefault();
                showSecurityWarning('Paste operation blocked');
                return false;
            });

            // Monitor window focus changes
            let focusLossCount = 0;
            let lastFocusTime = Date.now();

            window.addEventListener('blur', function () {
                if (!examStarted) return;

                focusLossCount++;
                const focusLossTime = Date.now() - lastFocusTime;

                if (focusLossTime > 1000) { // Only count if focus was lost for more than 1 second
                    showSecurityWarning('Window focus lost - Violation #' + focusLossCount);

                    if (window.Livewire) {
                        Livewire.emit('visibilityChanged', false);
                    }

                    if (focusLossCount >= 3) {
                        showSecurityWarning('Multiple focus changes detected! This behavior is being monitored.');
                    }
                }
            });

            window.addEventListener('focus', function () {
                lastFocusTime = Date.now();
            });

            // Monitor visibility changes (tab switching)
            document.addEventListener('visibilitychange', function () {
                if (!examStarted) return;

                if (document.hidden) {
                    showSecurityWarning('Tab switching detected - This activity is being monitored');
                    if (window.Livewire) {
                        Livewire.emit('visibilityChanged', false);
                    }
                }
            });

            // Monitor fullscreen changes
            document.addEventListener('fullscreenchange', handleFullscreenChange);
            document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
            document.addEventListener('mozfullscreenchange', handleFullscreenChange);
            document.addEventListener('msfullscreenchange', handleFullscreenChange);

            // Monitor for browser developer tools
            let devToolsOpen = false;
            setInterval(function () {
                if (window.outerHeight - window.innerHeight > 200 || window.outerWidth - window.innerWidth > 200) {
                    if (!devToolsOpen) {
                        devToolsOpen = true;
                        showSecurityWarning('Developer tools detected - This is a serious security violation!');
                        securityViolationCount += 3;

                        if (window.Livewire) {
                            Livewire.emit('securityViolation', 'developer_tools_detected');
                        }
                    }
                } else {
                    devToolsOpen = false;
                }
            }, 1000);

            // Disable printing
            window.addEventListener('beforeprint', function (e) {
                e.preventDefault();
                showSecurityWarning('Printing is disabled during exam');
                return false;
            });

            // Monitor for screenshots (limited detection)
            document.addEventListener('keydown', function (e) {
                if ((e.metaKey || e.ctrlKey) && e.shiftKey && (e.key === '3' || e.key === '4')) {
                    showSecurityWarning('Screenshot attempt detected');
                    if (window.Livewire) {
                        Livewire.emit('securityViolation', 'screenshot_attempt');
                    }
                }
            });
        }

        function handleFullscreenChange() {
            const isFullscreen = !!(
                document.fullscreenElement ||
                document.webkitFullscreenElement ||
                document.mozFullScreenElement ||
                document.msFullscreenElement
            );

            if (!isFullscreen && examStarted) {
                fullscreenExitCount++;
                document.getElementById('fullscreenExitWarning').style.display = 'flex';

                if (window.Livewire) {
                    Livewire.emit('securityViolation', 'fullscreen_exit', fullscreenExitCount);
                }

                setTimeout(() => {
                    if (!document.fullscreenElement) {
                        // Force fullscreen after 10 seconds
                        enterFullscreen();
                    }
                }, 10000);
            } else {
                document.getElementById('fullscreenExitWarning').style.display = 'none';
            }
        }

        function enterFullscreen() {
            const elem = document.documentElement;

            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.mozRequestFullScreen) {
                elem.mozRequestFullScreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }

            document.getElementById('fullscreenPrompt').style.display = 'none';
            document.getElementById('fullscreenExitWarning').style.display = 'none';
        }

        function showSecurityWarning(message, duration = 5000) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'security-warning';
            warningDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.getElementById('securityWarnings').appendChild(warningDiv);

            setTimeout(() => {
                if (warningDiv.parentNode) {
                    warningDiv.style.opacity = '0';
                    setTimeout(() => warningDiv.remove(), 300);
                }
            }, duration);
        }

        // Mark exam as started (called from Livewire component)
        window.markExamStarted = function () {
            examStarted = true;

            // Prompt for fullscreen if not already
            setTimeout(() => {
                if (!document.fullscreenElement) {
                    document.getElementById('fullscreenPrompt').style.display = 'flex';
                }
            }, 1000);
        };

        // Prevent back button
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            if (examStarted) {
                showSecurityWarning('Navigation blocked during exam');
                history.go(1);
            }
        };

        // Monitor for mobile device rotation
        window.addEventListener('orientationchange', function () {
            if (examStarted) {
                setTimeout(() => {
                    if (window.orientation !== 0) {
                        showSecurityWarning('Please use landscape orientation for better exam experience');
                    }
                }, 500);
            }
        });

        // Prevent zoom
        document.addEventListener('gesturestart', function (e) {
            e.preventDefault();
        });

        document.addEventListener('gesturechange', function (e) {
            e.preventDefault();
        });

        document.addEventListener('gestureend', function (e) {
            e.preventDefault();
        });

        // Handle beforeunload
        window.addEventListener('beforeunload', function (e) {
            if (examStarted && window.Livewire) {
                Livewire.emit('beforeUnload');
                e.preventDefault();
                e.returnValue = 'Are you sure you want to leave? Your progress will be saved but leaving may affect your session.';
            }
        });

        // Browser compatibility checks
        function checkBrowserCompatibility() {
            const isCompatible = (
                'requestFullscreen' in document.documentElement ||
                'webkitRequestFullscreen' in document.documentElement ||
                'mozRequestFullScreen' in document.documentElement ||
                'msRequestFullscreen' in document.documentElement
            );

            if (!isCompatible) {
                alert('Your browser may not support all security features required for this exam. Please use a modern browser like Chrome, Firefox, Safari, or Edge.');
            }
        }

        // Initialize compatibility check
        document.addEventListener('DOMContentLoaded', checkBrowserCompatibility);

        // Global error handler for exam security
        window.addEventListener('error', function (e) {
            console.error('Exam Error:', e.error);
            // Don't show detailed errors to prevent information leakage
        });
    </script>

    @stack('scripts')
</body>

</html>