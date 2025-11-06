<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CBT System') }} - Exam Mode</title>
    <!-- Google tag (gtag.js) -->
    <meta name="google-adsense-account" content="ca-pub-3911204427206897">
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-10833921436"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-10833921436');
</script>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TJ23X96Z');</script>
    <!-- End Google Tag Manager -->
    <meta name="google-site-verification" content="cmciE9Iqsl6Gl3u_0Zts_-SlchWbsZZ_8OMVpELH3CA" />

    {{-- Prevent caching --}}
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    {{-- Security Headers --}}
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

    {{-- Enhanced Exam Styles --}}
    <style>
        /* CSS Variables for theme customization */
        :root {
            --font-size-base: 16px;
            --font-size-sm: 14px;
            --font-size-lg: 18px;
            --font-size-xl: 20px;
            --font-size-2xl: 24px;
        }

        /* Font size classes */
        .font-size-sm { font-size: 14px !important; }
        .font-size-base { font-size: 16px !important; }
        .font-size-lg { font-size: 18px !important; }
        .font-size-xl { font-size: 20px !important; }
        .font-size-2xl { font-size: 24px !important; }

        /* High contrast mode */
        .high-contrast {
            filter: contrast(150%) !important;
        }

        .high-contrast * {
            border-color: #000 !important;
            background: #fff !important;
            color: #000 !important;
        }

        .high-contrast .bg-blue-600 {
            background-color: #000 !important;
            color: #fff !important;
        }

        .high-contrast .bg-green-500 {
            background-color: #000 !important;
            color: #fff !important;
        }

        /* Dark mode enhancements */
        .dark {
            color-scheme: dark;
        }

        /* Keyboard navigation focus indicators */
        .keyboard-nav *:focus {
            outline: 3px solid #3b82f6 !important;
            outline-offset: 2px !important;
        }

        /* Progress bar animations */
        .progress-animate {
            transition: width 0.5s ease-in-out;
        }

        /* Browser lockdown styles */
        .lockdown-active {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999999 !important;
            background: white !important;
        }

        .dark .lockdown-active {
            background: #1f2937 !important;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .mobile-stack {
                flex-direction: column !important;
            }
            
            .mobile-full {
                width: 100% !important;
            }
            
            .mobile-hidden {
                display: none !important;
            }
            
            .mobile-text-sm {
                font-size: 14px !important;
            }
        }

        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Print prevention */
        @media print {
            body {
                display: none !important;
            }
        }

        /* Timer critical state */
        .timer-critical {
            animation: pulse-red 1s infinite;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
        }

        @keyframes pulse-red {
            0%, 100% {
                background-color: rgb(185 28 28);
                color: white;
            }
            50% {
                background-color: rgb(239 68 68);
                color: white;
            }
        }

        /* Security overlay */
        .security-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(220, 38, 38, 0.98) !important;
            z-index: 999998 !important;
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
        }

        .critical-pulse {
            animation: criticalPulse 1s infinite;
        }

        @keyframes criticalPulse {
            0%, 100% { 
                background-color: rgba(220, 38, 38, 0.95);
            }
            50% { 
                background-color: rgba(185, 28, 28, 0.98);
            }
        }

        /* Application lockdown prevention */
        body.app-locked {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        body.app-locked img {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
            pointer-events: none;
        }

        body.app-locked *::selection {
            background: transparent;
        }

        body.app-locked *::-moz-selection {
            background: transparent;
        }
    </style>

    @stack('styles')
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-300" id="examBody">
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TJ23X96Z"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    {{-- Accessibility Settings Panel (Hidden by default) --}}
    <div id="accessibilityPanel" class="fixed top-4 left-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 z-50 transform -translate-x-full transition-transform duration-300">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Accessibility</h3>
            <button onclick="examInterface.toggleAccessibilityPanel()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Font Size</label>
                <select id="fontSizeSelect" onchange="examInterface.changeFontSize(this.value)" class="w-full px-2 py-1 border rounded text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="sm">Small</option>
                    <option value="base" selected>Normal</option>
                    <option value="lg">Large</option>
                    <option value="xl">Extra Large</option>
                    <option value="2xl">Huge</option>
                </select>
            </div>
            
            <div>
                <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" id="highContrastToggle" onchange="examInterface.toggleHighContrast()" class="mr-2">
                    High Contrast
                </label>
            </div>
            
            <div>
                <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" id="darkModeToggle" onchange="examInterface.toggleDarkMode()" class="mr-2">
                    Dark Mode
                </label>
            </div>
            
            <div>
                <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" id="keyboardNavToggle" onchange="examInterface.toggleKeyboardNavigation()" class="mr-2">
                    Keyboard Navigation
                </label>
            </div>
        </div>
    </div>

    {{-- Security/Browser Lockdown Overlay --}}
    <div id="securityOverlay" class="security-overlay">
        <div class="text-center p-8 max-w-lg">
            <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-6 animate-spin">
                <i class="fas fa-exclamation-triangle text-white text-4xl"></i>
            </div>
            <h1 class="text-4xl font-bold mb-6 animate-pulse">SECURITY VIOLATION</h1>
            <p class="text-xl mb-4">Application switching detected!</p>
            <p class="text-lg mb-6">You must remain in the exam application during the test.</p>
            <div class="space-y-4">
                <button onclick="examSecurity.returnToExam()" 
                    class="w-full bg-white text-red-600 px-8 py-4 rounded-lg text-xl font-bold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-undo mr-3"></i>Return to Exam
                </button>
                <p class="text-sm opacity-90">This violation has been logged</p>
            </div>
        </div>
    </div>

    {{-- Progress Tracking Overlay --}}
    <div id="progressOverlay" class="fixed bottom-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 z-40 transform translate-x-full transition-transform duration-300">
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">Progress</h4>
            <button onclick="examInterface.toggleProgressPanel()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <div id="progressContent" class="space-y-2 text-xs">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Completed:</span>
                <span id="progressCompleted" class="font-semibold">0/0</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Time Left:</span>
                <span id="progressTimeLeft" class="font-semibold">--:--</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Avg. Time/Q:</span>
                <span id="progressAvgTime" class="font-semibold">--:--</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Est. Finish:</span>
                <span id="progressEstFinish" class="font-semibold">--:--</span>
            </div>
        </div>
    </div>

    {{-- Security Warning Container --}}
    <div id="securityWarnings" class="fixed top-4 right-4 z-40 space-y-2"></div>

    {{-- Floating Accessibility Button --}}
    <button id="accessibilityButton" 
        onclick="examInterface.toggleAccessibilityPanel()"
        class="fixed top-4 left-4 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors z-40"
        title="Accessibility Options (Alt+A)"
        aria-label="Accessibility Options">
        <i class="fas fa-universal-access"></i>
    </button>

    {{-- Floating Progress Button --}}
    <button id="progressButton" 
        onclick="examInterface.toggleProgressPanel()"
        class="fixed bottom-4 right-4 bg-green-600 text-white p-3 rounded-full shadow-lg hover:bg-green-700 transition-colors z-40"
        title="Progress Tracking (Alt+P)"
        aria-label="Progress Tracking">
        <i class="fas fa-chart-line"></i>
    </button>

    {{-- Main Content --}}
    <main class="h-full">
        {{ $slot }}
    </main>

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Enhanced Security & Interface Scripts --}}
    <script>
        // Global exam interface management
        window.examInterface = {
            settings: {
                fontSize: 'base',
                highContrast: false,
                darkMode: localStorage.getItem('darkMode') === 'true',
                keyboardNavigation: false
            },
            
            init() {
                this.loadSettings();
                this.setupKeyboardHandlers();
                this.setupMobileHandlers();
                this.initializeTheme();
            },

            loadSettings() {
                const saved = localStorage.getItem('examSettings');
                if (saved) {
                    this.settings = { ...this.settings, ...JSON.parse(saved) };
                    this.applySettings();
                }
            },

            saveSettings() {
                localStorage.setItem('examSettings', JSON.stringify(this.settings));
            },

            applySettings() {
                document.body.className = document.body.className.replace(/font-size-\w+/g, '');
                document.body.classList.add(`font-size-${this.settings.fontSize}`);
                
                if (this.settings.highContrast) {
                    document.body.classList.add('high-contrast');
                }
                
                if (this.settings.keyboardNavigation) {
                    document.body.classList.add('keyboard-nav');
                }

                document.getElementById('fontSizeSelect').value = this.settings.fontSize;
                document.getElementById('highContrastToggle').checked = this.settings.highContrast;
                document.getElementById('darkModeToggle').checked = this.settings.darkMode;
                document.getElementById('keyboardNavToggle').checked = this.settings.keyboardNavigation;
            },

            setupKeyboardHandlers() {
                document.addEventListener('keydown', (e) => {
                    // Accessibility shortcuts
                    if (e.altKey && e.key === 'a') {
                        e.preventDefault();
                        this.toggleAccessibilityPanel();
                    }
                    
                    if (e.altKey && e.key === 'p') {
                        e.preventDefault();
                        this.toggleProgressPanel();
                    }

                    // Navigation shortcuts (only when keyboard nav is enabled)
                    if (this.settings.keyboardNavigation && window.examSecurity.examStarted) {
                        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                            e.preventDefault();
                            this.navigateQuestion('previous');
                        } else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                            e.preventDefault();
                            this.navigateQuestion('next');
                        } else if (e.key === 'f' || e.key === 'F') {
                            e.preventDefault();
                            this.toggleCurrentQuestionFlag();
                        }
                    }

                    // Font size shortcuts
                    if (e.ctrlKey && e.key === '+') {
                        e.preventDefault();
                        this.increaseFontSize();
                    } else if (e.ctrlKey && e.key === '-') {
                        e.preventDefault();
                        this.decreaseFontSize();
                    }
                });
            },

            setupMobileHandlers() {
                // Touch gestures for mobile navigation
                let touchStartX = 0;
                let touchEndX = 0;

                document.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                });

                document.addEventListener('touchend', (e) => {
                    if (!window.examSecurity.examStarted) return;
                    
                    touchEndX = e.changedTouches[0].screenX;
                    const swipeDistance = Math.abs(touchEndX - touchStartX);
                    
                    if (swipeDistance > 50) {
                        if (touchEndX < touchStartX) {
                            // Swipe left - next question
                            this.navigateQuestion('next');
                        } else {
                            // Swipe right - previous question
                            this.navigateQuestion('previous');
                        }
                    }
                });
            },

            initializeTheme() {
                if (this.settings.darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            },

            // Accessibility methods
            toggleAccessibilityPanel() {
                const panel = document.getElementById('accessibilityPanel');
                panel.classList.toggle('-translate-x-full');
            },

            changeFontSize(size) {
                this.settings.fontSize = size;
                this.applySettings();
                this.saveSettings();
            },

            increaseFontSize() {
                const sizes = ['sm', 'base', 'lg', 'xl', '2xl'];
                const currentIndex = sizes.indexOf(this.settings.fontSize);
                if (currentIndex < sizes.length - 1) {
                    this.changeFontSize(sizes[currentIndex + 1]);
                }
            },

            decreaseFontSize() {
                const sizes = ['sm', 'base', 'lg', 'xl', '2xl'];
                const currentIndex = sizes.indexOf(this.settings.fontSize);
                if (currentIndex > 0) {
                    this.changeFontSize(sizes[currentIndex - 1]);
                }
            },

            toggleHighContrast() {
                this.settings.highContrast = !this.settings.highContrast;
                document.body.classList.toggle('high-contrast', this.settings.highContrast);
                this.saveSettings();
            },

            toggleDarkMode() {
                this.settings.darkMode = !this.settings.darkMode;
                document.documentElement.classList.toggle('dark', this.settings.darkMode);
                localStorage.setItem('darkMode', this.settings.darkMode);
                this.saveSettings();
            },

            toggleKeyboardNavigation() {
                this.settings.keyboardNavigation = !this.settings.keyboardNavigation;
                document.body.classList.toggle('keyboard-nav', this.settings.keyboardNavigation);
                this.saveSettings();
            },

            // Progress tracking methods
            toggleProgressPanel() {
                const panel = document.getElementById('progressOverlay');
                panel.classList.toggle('translate-x-full');
            },

            updateProgress(data) {
                if (!data) return;
                
                document.getElementById('progressCompleted').textContent = 
                    `${data.answered}/${data.total}`;
                document.getElementById('progressTimeLeft').textContent = 
                    this.formatTime(data.timeRemaining);
                document.getElementById('progressAvgTime').textContent = 
                    this.formatTime(data.avgTimePerQuestion);
                document.getElementById('progressEstFinish').textContent = 
                    data.estimatedFinishTime;
            },

            formatTime(seconds) {
                if (!seconds) return '--:--';
                const mins = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            },

            // Navigation helpers
            navigateQuestion(direction) {
                if (window.livewire) {
                    const component = document.querySelector('[wire\\:id]');
                    if (component) {
                        const livewireComponent = window.livewire.find(component.getAttribute('wire:id'));
                        if (direction === 'next') {
                            livewireComponent.call('nextQuestion');
                        } else if (direction === 'previous') {
                            livewireComponent.call('previousQuestion');
                        }
                    }
                }
            },

            toggleCurrentQuestionFlag() {
                if (window.livewire) {
                    const component = document.querySelector('[wire\\:id]');
                    if (component) {
                        const livewireComponent = window.livewire.find(component.getAttribute('wire:id'));
                        // Assuming current question index is available
                        livewireComponent.call('toggleFlag', 0); // You'll need to pass actual index
                    }
                }
            }
        };

        // Enhanced exam security with browser lockdown
        window.examSecurity = {
            examStarted: false,
            fullscreenForced: false,
            browserLocked: false,
            violationCount: 0,
            focusViolations: 0,
            progressTracking: {
                startTime: null,
                questionTimes: [],
                currentQuestionStart: null
            },

            init() {
                console.log('Enhanced exam security initialized');
                this.setupSecurityMonitoring();
            },

            setupSecurityMonitoring() {
                // Application focus monitoring
                window.addEventListener('blur', () => {
                    if (this.examStarted && this.browserLocked) {
                        this.handleApplicationSwitch();
                    }
                });

                window.addEventListener('focus', () => {
                    if (this.examStarted && this.browserLocked) {
                        this.hideSecurityOverlay();
                    }
                });

                // Visibility change monitoring
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden && this.examStarted && this.browserLocked) {
                        this.handleApplicationSwitch();
                    }
                });

                // Prevent common exit shortcuts
                document.addEventListener('keydown', (e) => {
                    if (!this.examStarted) return;

                    // Block Alt+Tab, Alt+F4, Ctrl+Alt+Del, Windows key, etc.
                    if ((e.altKey && e.key === 'Tab') ||
                        (e.altKey && e.key === 'F4') ||
                        (e.ctrlKey && e.altKey && e.key === 'Delete') ||
                        e.key === 'Meta' || e.key === 'Win') {
                        e.preventDefault();
                        this.handleApplicationSwitch();
                        return false;
                    }
                });

                // Prevent right-click during exam
                document.addEventListener('contextmenu', (e) => {
                    if (this.examStarted) {
                        e.preventDefault();
                        this.showSecurityWarning('Right-click is disabled during exam');
                        return false;
                    }
                });
            },

            markExamStarted() {
                console.log('Exam started - activating browser lockdown');
                this.examStarted = true;
                this.browserLocked = true;
                this.fullscreenForced = true;
                this.progressTracking.startTime = Date.now();
                
                // Apply lockdown styles
                document.body.classList.add('app-locked', 'lockdown-active');
                
                // Enter fullscreen
                this.enterFullscreen();
                
                // Start progress tracking
                this.startProgressTracking();
                
                // Hide accessibility buttons during exam (optional)
                // document.getElementById('accessibilityButton').style.display = 'none';
            },

            allowFullscreenExit() {
                console.log('Allowing fullscreen exit');
                this.examStarted = false;
                this.browserLocked = false;
                this.fullscreenForced = false;
                
                // Remove lockdown
                document.body.classList.remove('app-locked', 'lockdown-active');
                this.hideSecurityOverlay();
                
                // Show accessibility buttons again
                document.getElementById('accessibilityButton').style.display = 'block';
                
                // Exit fullscreen
                if (this.isFullscreen()) {
                    this.exitFullscreen();
                }
            },

            handleApplicationSwitch() {
                this.focusViolations++;
                this.showSecurityOverlay();
                this.showSecurityWarning(`Application switch detected - Violation #${this.focusViolations}`);
                
                // Log violation to server
                if (window.livewire) {
                    const component = document.querySelector('[wire\\:id]');
                    if (component) {
                        window.livewire.find(component.getAttribute('wire:id'))
                            .call('handleSecurityViolation', 'app_switch', this.focusViolations);
                    }
                }

                // Auto-submit after too many violations
                if (this.focusViolations >= 3) {
                    this.showSecurityWarning('Too many security violations. Exam will be auto-submitted.');
                    setTimeout(() => {
                        if (window.livewire) {
                            const component = document.querySelector('[wire\\:id]');
                            if (component) {
                                window.livewire.find(component.getAttribute('wire:id')).call('submitExam');
                            }
                        }
                    }, 3000);
                }
            },

            showSecurityOverlay() {
                const overlay = document.getElementById('securityOverlay');
                overlay.style.display = 'flex';
                overlay.classList.add('critical-pulse');
            },

            hideSecurityOverlay() {
                const overlay = document.getElementById('securityOverlay');
                overlay.style.display = 'none';
                overlay.classList.remove('critical-pulse');
            },

            returnToExam() {
                this.hideSecurityOverlay();
                window.focus();
                this.enterFullscreen();
            },

            // Progress tracking methods
            startProgressTracking() {
                this.progressTracking.currentQuestionStart = Date.now();
                this.updateProgressDisplay();
                
                // Update progress every 5 seconds
                this.progressInterval = setInterval(() => {
                    this.updateProgressDisplay();
                }, 5000);
            },

            trackQuestionTime(questionIndex) {
                if (this.progressTracking.currentQuestionStart) {
                    const timeSpent = Date.now() - this.progressTracking.currentQuestionStart;
                    this.progressTracking.questionTimes[questionIndex] = timeSpent;
                    this.progressTracking.currentQuestionStart = Date.now();
                }
            },

            updateProgressDisplay() {
                if (!this.examStarted) return;
                
                // Get data from Livewire component
                if (window.livewire) {
                    const component = document.querySelector('[wire\\:id]');
                    if (component) {
                        const livewireComponent = window.livewire.find(component.getAttribute('wire:id'));
                        
                        // Calculate progress data
                        const totalTime = Date.now() - this.progressTracking.startTime;
                        const avgTimePerQuestion = this.progressTracking.questionTimes.length > 0 
                            ? this.progressTracking.questionTimes.reduce((a, b) => a + b, 0) / this.progressTracking.questionTimes.length / 1000
                            : 0;
                        
                        // You'll need to get these values from your Livewire component
                        const progressData = {
                            answered: 0, // Get from component
                            total: 0, // Get from component
                            timeRemaining: 0, // Get from component
                            avgTimePerQuestion: Math.round(avgTimePerQuestion),
                            estimatedFinishTime: this.calculateEstimatedFinish()
                        };
                        
                        window.examInterface.updateProgress(progressData);
                    }
                }
            },

            calculateEstimatedFinish() {
                // Calculate based on current pace
                const now = new Date();
                // Add estimation logic here
                return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            },

            // Fullscreen management
            isFullscreen() {
                return !!(
                    document.fullscreenElement ||
                    document.webkitFullscreenElement ||
                    document.mozFullScreenElement ||
                    document.msFullscreenElement ||
                    (window.innerHeight >= (screen.height - 100) && window.innerWidth >= (screen.width - 100))
                );
            },

            enterFullscreen() {
                const elem = document.documentElement;
                
                if (elem.requestFullscreen) {
                    elem.requestFullscreen({ navigationUI: 'hide' }).catch(console.error);
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.mozRequestFullScreen) {
                    elem.mozRequestFullScreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            },

            exitFullscreen() {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(console.error);
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            },

            showSecurityWarning(message, duration = 5000) {
                const warningDiv = document.createElement('div');
                warningDiv.className = 'bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg animate-pulse border-2 border-red-400';
                warningDiv.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <span class="font-semibold">${message}</span>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                const warningsContainer = document.getElementById('securityWarnings');
                if (warningsContainer) {
                    warningsContainer.appendChild(warningDiv);

                    setTimeout(() => {
                        if (warningDiv.parentNode) {
                            warningDiv.style.opacity = '0';
                            setTimeout(() => warningDiv.remove(), 300);
                        }
                    }, duration);
                }
            }
        };

        // Initialize everything on DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            window.examInterface.init();
            window.examSecurity.init();
        });

        // Global helper functions for Livewire
        window.markExamStarted = () => window.examSecurity.markExamStarted();
        window.allowFullscreenExit = () => window.examSecurity.allowFullscreenExit();
        
        // Prevent back button during exam
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            if (window.examSecurity.examStarted) {
                window.examSecurity.showSecurityWarning('Navigation blocked during exam');
                history.go(1);
            }
        };
    </script>

    @stack('scripts')
</body>

</html>