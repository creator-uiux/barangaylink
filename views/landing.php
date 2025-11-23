<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarangayLink - Digital Governance Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6); }
        }
        .animate-spin { animation: spin 1s linear infinite; }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        .animate-fade-in { animation: fadeIn 0.6s ease-out; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-glow { animation: glow 2s ease-in-out infinite; }

        /* Enhanced button hover effects */
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6b4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        /* Modal backdrop with animated gradient */
        .modal-backdrop {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(30,58,138,0.8) 50%, rgba(88,28,135,0.8) 100%);
            backdrop-filter: blur(8px);
        }

        /* Custom responsive breakpoints for better mobile support */
        @media (min-width: 475px) {
            .xs\:px-4 { padding-left: 1rem; padding-right: 1rem; }
            .xs\:py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
            .xs\:py-14 { padding-top: 3.5rem; padding-bottom: 3.5rem; }
            .xs\:py-16 { padding-top: 4rem; padding-bottom: 4rem; }
            .xs\:py-20 { padding-top: 5rem; padding-bottom: 5rem; }
            .xs\:mb-16 { margin-bottom: 4rem; }
            .xs\:mb-20 { margin-bottom: 5rem; }
            .xs\:mb-10 { margin-bottom: 2.5rem; }
            .xs\:mb-12 { margin-bottom: 3rem; }
            .xs\:mb-8 { margin-bottom: 2rem; }
            .xs\:mb-6 { margin-bottom: 1.5rem; }
            .xs\:mb-4 { margin-bottom: 1rem; }
            .xs\:mt-12 { margin-top: 3rem; }
            .xs\:mt-16 { margin-top: 4rem; }
            .xs\:mt-8 { margin-top: 2rem; }
            .xs\:mt-10 { margin-top: 2.5rem; }
            .xs\:space-x-4 > :not([hidden]) ~ :not([hidden]) { margin-left: 1rem; margin-right: 0; }
            .xs\:space-x-3 > :not([hidden]) ~ :not([hidden]) { margin-left: 0.75rem; margin-right: 0; }
            .xs\:space-x-2 > :not([hidden]) ~ :not([hidden]) { margin-left: 0.5rem; margin-right: 0; }
            .xs\:space-x-1 > :not([hidden]) ~ :not([hidden]) { margin-left: 0.25rem; margin-right: 0; }
            .xs\:space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem; }
            .xs\:space-y-3 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.75rem; }
            .xs\:space-y-2 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.5rem; }
            .xs\:gap-10 { gap: 2.5rem; }
            .xs\:gap-8 { gap: 2rem; }
            .xs\:w-12 { width: 3rem; }
            .xs\:h-12 { height: 3rem; }
            .xs\:w-7 { width: 1.75rem; }
            .xs\:h-7 { height: 1.75rem; }
            .xs\:w-5 { width: 1.25rem; }
            .xs\:h-5 { height: 1.25rem; }
            .xs\:w-2 { width: 0.5rem; }
            .xs\:h-2 { height: 0.5rem; }
            .xs\:w-9 { width: 2.25rem; }
            .xs\:h-9 { height: 2.25rem; }
            .xs\:w-6 { width: 1.5rem; }
            .xs\:h-6 { height: 1.5rem; }
            .xs\:w-3 { width: 0.75rem; }
            .xs\:h-3 { height: 0.75rem; }
            .xs\:text-xl { font-size: 1.25rem; line-height: 1.75rem; }
            .xs\:text-lg { font-size: 1.125rem; line-height: 1.75rem; }
            .xs\:text-sm { font-size: 0.875rem; line-height: 1.25rem; }
            .xs\:text-xs { font-size: 0.75rem; line-height: 1rem; }
            .xs\:text-base { font-size: 1rem; line-height: 1.5rem; }
            .xs\:px-4 { padding-left: 1rem; padding-right: 1rem; }
            .xs\:py-2\.5 { padding-top: 0.625rem; padding-bottom: 0.625rem; }
            .xs\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
            .xs\:px-8 { padding-left: 2rem; padding-right: 2rem; }
            .xs\:px-10 { padding-left: 2.5rem; padding-right: 2.5rem; }
            .xs\:py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
            .xs\:py-4 { padding-top: 1rem; padding-bottom: 1rem; }
            .xs\:py-5 { padding-top: 1.25rem; padding-bottom: 1.25rem; }
            .xs\:rounded-xl { border-radius: 0.75rem; }
            .xs\:rounded-2xl { border-radius: 1rem; }
            .xs\:mt-1 { margin-top: 0.25rem; }
            .xs\:mt-0\.5 { margin-top: 0.125rem; }
            .xs\:pt-8 { padding-top: 2rem; }
            .xs\:pt-6 { padding-top: 1.5rem; }
            .xs\:mb-3 { margin-bottom: 0.75rem; }
            .xs\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 relative overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -left-32 w-64 h-64 bg-indigo-400/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-1/4 w-48 h-48 bg-purple-400/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Header -->
        <header class="bg-white/80 backdrop-blur-lg border-b border-blue-200/50 sticky top-0 z-50 shadow-lg">
            <div class="max-w-7xl mx-auto px-2 xs:px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4 xs:py-6">
                    <div class="flex items-center space-x-2 xs:space-x-4">
                        <div class="relative">
                            <div class="w-10 h-10 xs:w-12 xs:h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl flex items-center justify-center shadow-xl">
                                <svg class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="absolute -top-1 -right-1 w-4 h-4 xs:w-5 xs:h-5 sm:w-6 sm:h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <div class="w-1.5 h-1.5 xs:w-2 xs:h-2 bg-white rounded-full"></div>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-lg xs:text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-900 to-blue-700 bg-clip-text text-transparent">BarangayLink</h1>
                            <div class="flex items-center space-x-1 xs:space-x-2">
                                <div class="w-1.5 h-1.5 xs:w-2 xs:h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                <p class="text-xs xs:text-sm text-blue-600 font-medium">Digital Governance Platform</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 xs:space-x-4">
                        <button
                            onclick="showLoginModal()"
                            class="px-3 py-2 xs:px-4 xs:py-2.5 sm:px-6 sm:py-3 text-blue-600 hover:bg-blue-50 rounded-lg xs:rounded-xl transition-all duration-200 font-medium hover:shadow-md text-sm xs:text-base"
                        >
                            Sign In
                        </button>
                        <button
                            onclick="showSignupModal()"
                            class="px-4 py-2 xs:px-6 xs:py-2.5 sm:px-8 sm:py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg xs:rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105 text-sm xs:text-base"
                        >
                            Get Started
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative max-w-7xl mx-auto px-2 xs:px-4 sm:px-6 lg:px-8 py-12 xs:py-16 sm:py-20 lg:py-24">
            <div class="text-center max-w-4xl mx-auto mb-12 xs:mb-16 sm:mb-20">
                <div class="mb-6 xs:mb-8">
                    <div class="inline-flex items-center space-x-1 xs:space-x-2 bg-blue-100 text-blue-700 px-3 py-1.5 xs:px-4 xs:py-2 rounded-full text-xs xs:text-sm font-medium mb-4 xs:mb-6">
                        <div class="w-1.5 h-1.5 xs:w-2 xs:h-2 bg-blue-500 rounded-full animate-pulse"></div>
                        <span>Digital Transformation for Local Governance</span>
                    </div>
                </div>
                <h2 class="text-3xl xs:text-4xl sm:text-5xl lg:text-6xl font-bold bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-700 bg-clip-text text-transparent mb-4 xs:mb-6 leading-tight">
                    Connecting Communities Through
                    <span class="block bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Smart Governance</span>
                </h2>
                <p class="text-base xs:text-lg sm:text-xl text-gray-600 mb-8 xs:mb-10 sm:mb-12 leading-relaxed px-2 xs:px-0">
                    Experience seamless barangay services with our comprehensive digital platform.
                    From document requests to community updates and emergency alerts - everything you need in one place.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 xs:space-y-4 sm:space-y-0 sm:space-x-6">
                    <button
                        onclick="showSignupModal()"
                        class="w-full sm:w-auto px-6 xs:px-8 sm:px-10 py-3 xs:py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl xs:rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-semibold shadow-xl hover:shadow-2xl transform hover:scale-105 text-base xs:text-lg"
                    >
                        <div class="flex items-center justify-center space-x-2 xs:space-x-3">
                            <span>Get Started Today</span>
                            <svg class="w-4 h-4 xs:w-5 xs:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </button>
                    <button
                        onclick="showLoginModal()"
                        class="w-full sm:w-auto px-6 xs:px-8 py-3 xs:py-4 bg-white text-blue-600 border-2 border-blue-200 rounded-xl xs:rounded-2xl hover:bg-blue-50 hover:border-blue-300 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl text-base xs:text-lg"
                    >
                        Sign In to Continue
                    </button>
                </div>
            </div>

            <!-- Features Preview -->
            <div class="mt-20 bg-white/60 backdrop-blur-lg rounded-3xl shadow-2xl border border-blue-200/50 p-8 lg:p-12">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">Everything You Need for Digital Governance</h3>
                    <p class="text-lg text-gray-600">Streamlined services designed for modern barangay administration</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Service Card 1 -->
                    <div class="group bg-white/80 backdrop-blur-lg rounded-xl xs:rounded-2xl p-4 xs:p-6 sm:p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="w-12 h-12 xs:w-14 xs:h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4 xs:mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-6 h-6 xs:w-8 xs:h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg xs:text-xl font-bold text-gray-800 mb-2 xs:mb-3">Document Requests</h3>
                        <p class="text-sm xs:text-base text-gray-600 leading-relaxed">Request barangay clearance, indigency certificates, and other official documents online with fast processing.</p>
                        <div class="mt-4 xs:mt-6 flex items-center text-blue-600 group-hover:text-blue-700 transition-colors">
                            <span class="text-xs xs:text-sm font-medium">Learn more</span>
                            <svg class="w-3 h-3 xs:w-4 xs:h-4 ml-1 xs:ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Service Card 2 -->
                    <div class="group bg-white/80 backdrop-blur-lg rounded-xl xs:rounded-2xl p-4 xs:p-6 sm:p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="w-12 h-12 xs:w-14 xs:h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-4 xs:mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-6 h-6 xs:w-8 xs:h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg xs:text-xl font-bold text-gray-800 mb-2 xs:mb-3">Submit Concerns</h3>
                        <p class="text-sm xs:text-base text-gray-600 leading-relaxed">Report community issues and track their resolution status in real-time with detailed updates.</p>
                        <div class="mt-4 xs:mt-6 flex items-center text-blue-600 group-hover:text-blue-700 transition-colors">
                            <span class="text-xs xs:text-sm font-medium">Learn more</span>
                            <svg class="w-3 h-3 xs:w-4 xs:h-4 ml-1 xs:ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Service Card 3 -->
                    <div class="group bg-white/80 backdrop-blur-lg rounded-xl xs:rounded-2xl p-4 xs:p-6 sm:p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="w-12 h-12 xs:w-14 xs:h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-4 xs:mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-6 h-6 xs:w-8 xs:h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg xs:text-xl font-bold text-gray-800 mb-2 xs:mb-3">Events & Announcements</h3>
                        <p class="text-sm xs:text-base text-gray-600 leading-relaxed">Stay updated with the latest community events, news, and programs through instant notifications.</p>
                        <div class="mt-4 xs:mt-6 flex items-center text-blue-600 group-hover:text-blue-700 transition-colors">
                            <span class="text-xs xs:text-sm font-medium">Learn more</span>
                            <svg class="w-3 h-3 xs:w-4 xs:h-4 ml-1 xs:ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Service Card 4 -->
                    <div class="group bg-white/80 backdrop-blur-lg rounded-2xl p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Community Directory</h3>
                        <p class="text-gray-600 leading-relaxed">Access contact information for barangay officials and important local services with instant connectivity.</p>
                        <div class="mt-6 flex items-center text-blue-600 group-hover:text-blue-700 transition-colors">
                            <span class="text-sm font-medium">Learn more</span>
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Service Card 5 -->
                    <div class="group bg-white/80 backdrop-blur-lg rounded-2xl p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Emergency Alerts</h3>
                        <p class="text-gray-600 leading-relaxed">Receive real-time notifications about emergencies and safety updates with immediate response protocols.</p>
                        <div class="mt-6 flex items-center text-blue-600 group-hover:text-blue-700 transition-colors">
                            <span class="text-sm font-medium">Learn more</span>
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Service Card 6 -->
                    <div class="group bg-white/80 backdrop-blur-lg rounded-2xl p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Information Hub</h3>
                        <p class="text-gray-600 leading-relaxed">Access policies, guidelines, and important barangay information with comprehensive documentation.</p>
                        <div class="mt-6 flex items-center text-blue-600 group-hover:text-blue-700 transition-colors">
                            <span class="text-sm font-medium">Learn more</span>
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose BarangayLink Section -->
        <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="bg-gradient-to-br from-white via-blue-50 to-indigo-100 rounded-3xl p-12 border border-blue-200/50 shadow-2xl backdrop-blur-lg">
                <div class="text-center mb-16">
                    <h3 class="text-4xl font-bold bg-gradient-to-r from-blue-900 to-indigo-700 bg-clip-text text-transparent mb-4">
                        Why Choose BarangayLink?
                    </h3>
                    <p class="text-lg text-gray-600">Experience the future of local governance with our cutting-edge platform</p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-10">
                    <div class="text-center group">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-3">Secure & Reliable</h4>
                        <p class="text-gray-600 leading-relaxed">Enterprise-grade security with end-to-end encryption ensures your data remains protected at all times.</p>
                    </div>
                    
                    <div class="text-center group">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-3">Community-Focused</h4>
                        <p class="text-gray-600 leading-relaxed">Purpose-built for barangay governance with features specifically designed for local community needs.</p>
                    </div>
                    
                    <div class="text-center group">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-3">Easy to Use</h4>
                        <p class="text-gray-600 leading-relaxed">Intuitive interface designed for all ages with 24/7 accessibility from any device, anywhere.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative py-16 xs:py-20 sm:py-24 mt-12 xs:mt-16 sm:mt-20 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800"></div>
            <div class="absolute inset-0">
                <div class="absolute top-0 left-1/4 w-48 h-48 xs:w-64 xs:h-64 sm:w-72 sm:h-72 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-64 h-64 xs:w-80 xs:h-80 sm:w-96 sm:h-96 bg-white/5 rounded-full blur-3xl"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-2 xs:px-4 sm:px-6 lg:px-8 text-center">
                <div class="max-w-3xl mx-auto">
                    <h3 class="text-2xl xs:text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 xs:mb-6">
                        Ready to Join Your
                        <span class="block bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">Digital Barangay?</span>
                    </h3>
                    <p class="text-base xs:text-lg sm:text-xl text-blue-100 mb-8 xs:mb-10 sm:mb-12 leading-relaxed px-2 xs:px-0">
                        Transform your community experience with our comprehensive digital governance platform.
                        Join thousands of residents already using BarangayLink for seamless government services.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 xs:space-y-4 sm:space-y-0 sm:space-x-6">
                        <button
                            onclick="showSignupModal()"
                            class="w-full sm:w-auto px-8 xs:px-10 sm:px-12 py-3 xs:py-4 sm:py-5 bg-white text-blue-600 rounded-xl xs:rounded-2xl hover:bg-blue-50 transition-all duration-300 font-bold text-base xs:text-lg shadow-2xl hover:shadow-white/25 transform hover:scale-105"
                        >
                            <div class="flex items-center justify-center space-x-2 xs:space-x-3">
                                <span>Start Your Journey</span>
                                <svg class="w-4 h-4 xs:w-5 xs:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                        </button>

                        <button
                            onclick="showLoginModal()"
                            class="w-full sm:w-auto px-6 xs:px-8 sm:px-10 py-3 xs:py-4 sm:py-5 bg-white/10 text-white border-2 border-white/30 rounded-xl xs:rounded-2xl hover:bg-white/20 hover:border-white/50 transition-all duration-300 font-semibold backdrop-blur-lg text-base xs:text-lg"
                        >
                            I Already Have an Account
                        </button>
                    </div>

                    <div class="mt-8 xs:mt-10 sm:mt-12 flex flex-col xs:flex-row items-center justify-center space-y-2 xs:space-y-0 xs:space-x-4 sm:space-x-6 lg:space-x-8 text-blue-200 text-xs xs:text-sm">
                        <div class="flex items-center space-x-1 xs:space-x-2">
                            <svg class="w-3 h-3 xs:w-4 xs:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Free to Use</span>
                        </div>
                        <div class="flex items-center space-x-1 xs:space-x-2">
                            <svg class="w-3 h-3 xs:w-4 xs:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Secure & Private</span>
                        </div>
                        <div class="flex items-center space-x-1 xs:space-x-2">
                            <svg class="w-3 h-3 xs:w-4 xs:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>24/7 Access</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Support Links Section -->
        <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center mb-16">
                <h3 class="text-4xl font-bold bg-gradient-to-r from-blue-900 to-indigo-700 bg-clip-text text-transparent mb-4">
                    Need Help?
                </h3>
                <p class="text-lg text-gray-600">We're here to support you every step of the way</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="group bg-white/90 backdrop-blur-lg rounded-2xl p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-3">Help Center</h4>
                    <p class="text-gray-600 leading-relaxed mb-6">Find answers to common questions and troubleshooting guides</p>
                    <button class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 transition-colors font-medium">
                        <span>Get Help</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <div class="group bg-white/90 backdrop-blur-lg rounded-2xl p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-3">User Guide</h4>
                    <p class="text-gray-600 leading-relaxed mb-6">Comprehensive tutorials on how to use BarangayLink effectively</p>
                    <button class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 transition-colors font-medium">
                        <span>Get Help</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <div class="group bg-white/90 backdrop-blur-lg rounded-2xl p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-3">FAQ</h4>
                    <p class="text-gray-600 leading-relaxed mb-6">Frequently asked questions with detailed explanations</p>
                    <button class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 transition-colors font-medium">
                        <span>Get Help</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <div class="group bg-white/90 backdrop-blur-lg rounded-2xl p-8 border border-gray-200/50 hover:border-blue-300/50 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-3">Contact Us</h4>
                    <p class="text-gray-600 leading-relaxed mb-6">Get in touch with our support team for personalized assistance</p>
                    <button class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 transition-colors font-medium">
                        <span>Get Help</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gradient-to-br from-slate-800 via-gray-900 to-blue-900 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-12 mb-12">
                    <div>
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="text-2xl font-bold text-white">BarangayLink</span>
                                <p class="text-blue-300 text-sm">Digital Governance</p>
                            </div>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-6">
                            Empowering communities through innovative digital solutions. 
                            Building bridges between residents and local government services.
                        </p>
                        <div class="flex space-x-4">
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </div>
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </div>
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.219-.359-1.219c0-1.142.662-1.995 1.488-1.995.219 0 .518.16.518.72 0 .439-.279 1.098-.279 1.717 0 .718.359 1.277 1.078 1.277 1.295 0 2.291-1.365 2.291-3.336 0-1.744-1.252-2.965-3.043-2.965-2.07 0-3.282 1.551-3.282 3.156 0 .625.24 1.295.538 1.658a.234.234 0 0 1 .058.229c-.061.238-.199.777-.225.885-.035.146-.116.177-.268.107-1.001-.465-1.624-1.926-1.624-3.1 0-2.299 1.671-4.413 4.818-4.413 2.529 0 4.493 1.804 4.493 4.214 0 2.514-1.587 4.535-3.79 4.535-.74 0-1.438-.387-1.677-.896 0 0-.369 1.405-.458 1.748-.166.636-.615 1.431-.916 1.916C9.757 23.688 10.847 24 12.017 24c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-xl font-bold text-white mb-6">Quick Links</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-300 hover:text-blue-400 transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span>About Us</span>
                            </a></li>
                            <li><a href="#" class="text-gray-300 hover:text-blue-400 transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span>Services</span>
                            </a></li>
                            <li><a href="#" class="text-gray-300 hover:text-blue-400 transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span>Privacy Policy</span>
                            </a></li>
                            <li><a href="#" class="text-gray-300 hover:text-blue-400 transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span>Terms of Service</span>
                            </a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-xl font-bold text-white mb-6">Contact Details</h4>
                        <ul class="space-y-4">
                            <li class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center mt-1">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-300">Email</p>
                                    <p class="text-white font-medium">info@barangaylink.gov.ph</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center mt-1">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-300">Phone</p>
                                    <p class="text-white font-medium">+63 912 345 6789</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center mt-1">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-300">Address</p>
                                    <p class="text-white font-medium">Barangay Hall, Main Street</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 pt-8">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="text-gray-300 mb-4 md:mb-0">
                            <p>&copy; 2025 BarangayLink. All rights reserved.</p>
                        </div>
                        <div class="text-blue-400 font-medium">
                            <p>Serving our community with technology and care.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Auth Modal Placeholder (will be loaded via AJAX/Modal) -->
    <div id="auth-modal-container"></div>

    <script>
        function showLoginModal() {
            // This will be implemented with proper modal functionality
            window.location.href = 'index.php?action=show_login';
        }

        function showSignupModal() {
            // This will be implemented with proper modal functionality
            window.location.href = 'index.php?action=show_signup';
        }
    </script>
</body>
</html>
