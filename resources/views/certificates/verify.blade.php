<x-app-layout>
<div class="min-h-screen bg-gray-900 p-6">
    <!-- Main Content -->
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="mx-auto w-24 h-24 bg-primary-600 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-certificate text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-4">Certificate Verification</h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Verify the authenticity of certificates issued by Academy Learning Platform
                </p>
            </div>

            <!-- Verification Form -->
            <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-8 mb-8 border border-white border-opacity-20">
                <div class="max-w-md mx-auto">
                    <form id="verificationForm" class="space-y-6">
                        <div>
                            <label for="verification_code" class="block text-sm font-medium text-gray-200 mb-2">
                                Verification Code
                            </label>
                            <input 
                                type="text" 
                                id="verification_code" 
                                name="verification_code"
                                placeholder="Enter verification code"
                                class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                required
                            >
                            <p class="mt-1 text-xs text-gray-400">
                                Enter the verification code found on the certificate
                            </p>
                        </div>
                        
                        <button 
                            type="submit" 
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                        >
                            <i class="fas fa-search mr-2"></i>
                            Verify Certificate
                        </button>
                    </form>
                </div>
            </div>

            <!-- Results Section -->
            <div id="loadingSection" class="hidden bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-8 text-center border border-white border-opacity-20">
                <i class="fas fa-spinner fa-spin text-primary-400 text-3xl mb-4"></i>
                <p class="text-gray-300">Verifying certificate...</p>
            </div>

            <!-- Valid Certificate Display -->
            <div id="validCertificate" class="hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-8 mb-8">
                    <div class="flex items-center justify-center mb-6">
                        <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                            <i class="fas fa-check-circle text-white text-3xl"></i>
                        </div>
                        <div class="text-center">
                            <h2 class="text-2xl font-bold text-white mb-2">Certificate Verified ✓</h2>
                            <p class="text-green-100">This certificate is authentic and valid</p>
                        </div>
                    </div>
                </div>

                <!-- Certificate Details -->
                <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-8 border border-white border-opacity-20">
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <i class="fas fa-user mr-3 text-primary-400"></i>
                                    Certificate Holder
                                </h3>
                                <div class="bg-white bg-opacity-5 rounded-lg p-4">
                                    <p class="text-2xl font-bold text-white" id="studentName"></p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <i class="fas fa-book mr-3 text-primary-400"></i>
                                    Course Information
                                </h3>
                                <div class="bg-white bg-opacity-5 rounded-lg p-4">
                                    <p class="text-xl font-semibold text-white mb-2" id="courseTitle"></p>
                                    <p class="text-gray-300" id="instructorName"></p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <i class="fas fa-award mr-3 text-primary-400"></i>
                                    Achievement Details
                                </h3>
                                <div class="bg-white bg-opacity-5 rounded-lg p-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">Grade:</span>
                                        <span class="text-white font-semibold" id="grade"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">Completion Date:</span>
                                        <span class="text-white" id="completionDate"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">Issue Date:</span>
                                        <span class="text-white" id="issueDate"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <i class="fas fa-shield-alt mr-3 text-primary-400"></i>
                                    Verification Details
                                </h3>
                                <div class="bg-white bg-opacity-5 rounded-lg p-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">Certificate ID:</span>
                                        <span class="text-white font-mono text-sm" id="certificateNumber"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">Verification Code:</span>
                                        <span class="text-white font-mono text-sm" id="verificationCode"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">Verified On:</span>
                                        <span class="text-white text-sm" id="verificationDate"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- QR Code Display -->
                            <div class="text-center">
                                <h3 class="text-lg font-semibold text-white mb-4">Quick Verification</h3>
                                <div class="bg-white p-4 rounded-lg inline-block">
                                    <img id="qrCode" src="" alt="Verification QR Code" class="w-32 h-32">
                                </div>
                                <p class="text-xs text-gray-400 mt-2">Scan to verify instantly</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <button 
                                    id="viewCertificate" 
                                    class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center"
                                >
                                    <i class="fas fa-eye mr-2"></i>
                                    View Full Certificate
                                </button>
                                <button 
                                    id="downloadCertificate" 
                                    class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center"
                                >
                                    <i class="fas fa-download mr-2"></i>
                                    Download PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invalid Certificate Display -->
            <div id="invalidCertificate" class="hidden">
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-8 mb-8">
                    <div class="flex items-center justify-center mb-6">
                        <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                            <i class="fas fa-times-circle text-white text-3xl"></i>
                        </div>
                        <div class="text-center">
                            <h2 class="text-2xl font-bold text-white mb-2">Certificate Not Valid</h2>
                            <p class="text-red-100" id="invalidMessage">This certificate could not be verified</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-8 text-center border border-white border-opacity-20">
                    <p class="text-gray-300 mb-6">
                        The verification code you entered does not match our records, or the certificate may have been revoked.
                    </p>
                    <div class="space-y-4">
                        <p class="text-sm text-gray-400">Please check:</p>
                        <ul class="text-sm text-gray-400 text-left max-w-md mx-auto space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-check text-gray-500 mr-2 mt-0.5"></i>
                                The verification code is entered correctly
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-gray-500 mr-2 mt-0.5"></i>
                                There are no extra spaces or special characters
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-gray-500 mr-2 mt-0.5"></i>
                                The certificate was issued by Academy Learning Platform
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- How It Works Section -->
            <div class="mt-16 bg-white bg-opacity-5 backdrop-blur-lg rounded-2xl p-8 border border-white border-opacity-10">
                <h2 class="text-2xl font-bold text-white text-center mb-8">How Certificate Verification Works</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="bg-primary-600 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-search text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">1. Enter Code</h3>
                        <p class="text-gray-300 text-sm">Enter the verification code found on the certificate or scan the QR code</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-primary-600 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">2. Instant Check</h3>
                        <p class="text-gray-300 text-sm">Our system instantly verifies the certificate against our secure database</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-primary-600 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">3. Get Results</h3>
                        <p class="text-gray-300 text-sm">Receive immediate confirmation of authenticity with detailed certificate information</p>
                    </div>
                </div>
            </div>

            <!-- Trust Indicators -->
            <div class="mt-12 text-center">
                <p class="text-gray-400 text-sm mb-4">Trusted by employers and institutions worldwide</p>
                <div class="flex items-center justify-center space-x-6 opacity-60">
                    <i class="fas fa-university text-2xl text-gray-400"></i>
                    <i class="fas fa-building text-2xl text-gray-400"></i>
                    <i class="fas fa-briefcase text-2xl text-gray-400"></i>
                    <i class="fas fa-globe text-2xl text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('verificationForm');
            const loadingSection = document.getElementById('loadingSection');
            const validCertificate = document.getElementById('validCertificate');
            const invalidCertificate = document.getElementById('invalidCertificate');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const verificationCode = document.getElementById('verification_code').value.trim();
                
                if (!verificationCode) {
                    alert('Please enter a verification code');
                    return;
                }

                // Hide previous results
                validCertificate.classList.add('hidden');
                invalidCertificate.classList.add('hidden');
                
                // Show loading
                loadingSection.classList.remove('hidden');

                // Make verification request
                fetch(`/api/certificate/verify/${verificationCode}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingSection.classList.add('hidden');
                        
                        if (data.valid) {
                            displayValidCertificate(data.certificate);
                        } else {
                            displayInvalidCertificate(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        loadingSection.classList.add('hidden');
                        displayInvalidCertificate('An error occurred while verifying the certificate. Please try again.');
                    });
            });

            function displayValidCertificate(certificate) {
                // Populate certificate data
                document.getElementById('studentName').textContent = certificate.student_name;
                document.getElementById('courseTitle').textContent = certificate.course_title;
                document.getElementById('instructorName').textContent = 'Instructor: ' + certificate.instructor_name;
                document.getElementById('grade').textContent = certificate.grade || 'Pass';
                document.getElementById('completionDate').textContent = certificate.completion_date;
                document.getElementById('issueDate').textContent = certificate.issued_date;
                document.getElementById('certificateNumber').textContent = certificate.certificate_number;
                document.getElementById('verificationCode').textContent = certificate.verification_code;
                document.getElementById('verificationDate').textContent = new Date().toLocaleDateString();

                // Set up action buttons
                const viewBtn = document.getElementById('viewCertificate');
                const downloadBtn = document.getElementById('downloadCertificate');
                
                viewBtn.onclick = () => window.open(`/certificate/view/${certificate.verification_code}`, '_blank');
                downloadBtn.onclick = () => window.open(`/certificate/download/${certificate.verification_code}`, '_blank');

                // Show QR code if available
                const qrCode = document.getElementById('qrCode');
                qrCode.src = `/certificate/qr/${certificate.verification_code}`;

                validCertificate.classList.remove('hidden');
                validCertificate.scrollIntoView({ behavior: 'smooth' });
            }

            function displayInvalidCertificate(message) {
                document.getElementById('invalidMessage').textContent = message;
                invalidCertificate.classList.remove('hidden');
                invalidCertificate.scrollIntoView({ behavior: 'smooth' });
            }

            // URL parameter handling
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code');
            if (code) {
                document.getElementById('verification_code').value = code;
                form.dispatchEvent(new Event('submit'));
            }
        });
    </script>
</div>
</x-app-layout>