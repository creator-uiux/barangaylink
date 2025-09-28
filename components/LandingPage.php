<?php
function renderLandingPage() {
?>
<div class="min-h-screen bg-background">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/95 backdrop-blur-md z-50 border-b border-blue-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-600 rounded-lg">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-blue-900">BarangayLink</span>
                        <div class="text-xs text-blue-600 -mt-1">Your Direct Link to Local Updates and Services</div>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <button onclick="scrollToSection('home')" class="text-slate-600 hover:text-blue-600 transition-colors font-medium">
                        Home
                    </button>
                    <button onclick="scrollToSection('announcements')" class="text-slate-600 hover:text-blue-600 transition-colors font-medium">
                        Announcements
                    </button>
                    <button onclick="scrollToSection('events')" class="text-slate-600 hover:text-blue-600 transition-colors font-medium">
                        Events
                    </button>
                    <button onclick="scrollToSection('projects')" class="text-slate-600 hover:text-blue-600 transition-colors font-medium">
                        Services
                    </button>
                    <button onclick="scrollToSection('contact')" class="text-slate-600 hover:text-blue-600 transition-colors font-medium">
                        Contact
                    </button>
                    <button onclick="openAuthModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                        Access Portal
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="p-2 rounded-md hover:bg-gray-100">
                        <div class="w-6 h-6 flex flex-col justify-around">
                            <span id="menu-line-1" class="h-0.5 w-6 bg-gray-600 transform transition"></span>
                            <span id="menu-line-2" class="h-0.5 w-6 bg-gray-600 transition"></span>
                            <span id="menu-line-3" class="h-0.5 w-6 bg-gray-600 transform transition"></span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden bg-background border-t hidden">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <button onclick="scrollToSection('home')" class="block px-3 py-2 w-full text-left hover:bg-blue-50 rounded-md">
                        Home
                    </button>
                    <button onclick="scrollToSection('announcements')" class="block px-3 py-2 w-full text-left hover:bg-blue-50 rounded-md">
                        Announcements
                    </button>
                    <button onclick="scrollToSection('events')" class="block px-3 py-2 w-full text-left hover:bg-blue-50 rounded-md">
                        Events
                    </button>
                    <button onclick="scrollToSection('projects')" class="block px-3 py-2 w-full text-left hover:bg-blue-50 rounded-md">
                        Services
                    </button>
                    <button onclick="scrollToSection('contact')" class="block px-3 py-2 w-full text-left hover:bg-blue-50 rounded-md">
                        Contact
                    </button>
                    <button onclick="openAuthModal()" class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                        Access Portal
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="pt-16 min-h-screen flex items-center bg-gradient-to-br from-blue-50 via-white to-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span class="text-sm font-medium">Secure • Transparent • Accessible</span>
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-slate-900 mb-6 leading-tight">
                        Your Digital Gateway to 
                        <span class="text-blue-600"> Barangay Services</span>
                    </h1>
                    <p class="text-xl text-slate-600 mb-8 max-w-2xl leading-relaxed">
                        Experience seamless access to local government services, real-time community updates, and transparent digital governance at your fingertips.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <button onclick="openAuthModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 text-lg rounded-md font-medium transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                            Access Portal
                        </button>
                        <button onclick="scrollToSection('announcements')" class="border border-blue-300 text-blue-700 hover:bg-blue-50 px-8 py-3 text-lg rounded-md font-medium transition-colors">
                            Explore Services
                        </button>
                    </div>
                </div>
                <div class="flex justify-center lg:justify-end">
                    <div class="relative">
                        <div class="w-80 h-80 bg-gradient-to-br from-blue-500 to-blue-700 rounded-3xl flex items-center justify-center transform rotate-3 shadow-2xl">
                            <svg class="w-36 h-36 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-white rounded-2xl shadow-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-4 -right-4 w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Announcements Preview -->
    <section id="announcements" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Community Updates</h2>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">Stay informed with the latest announcements, events, and important notices from your barangay administration.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-6 hover:shadow-xl transition-all duration-300 border-0 shadow-md bg-gradient-to-br from-white to-blue-50 rounded-lg">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-blue-600 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-md text-sm">Dec 15, 2024</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900">Community Clean-up Drive</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Join us for our monthly community clean-up drive this Saturday at 7:00 AM. Let's keep our barangay clean and green!
                    </p>
                    <button class="p-0 text-blue-600 hover:text-blue-800 font-semibold">Read More →</button>
                </div>

                <div class="p-6 hover:shadow-xl transition-all duration-300 border-0 shadow-md bg-gradient-to-br from-white to-green-50 rounded-lg">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-green-600 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V9a2 2 0 01-2 2H4a2 2 0 01-2-2V7a2 2 0 012-2h2m6 0h2a2 2 0 012 2v2a2 2 0 01-2 2h-2m-6 0v6a2 2 0 002 2h4a2 2 0 002-2v-6"></path>
                            </svg>
                        </div>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-md text-sm">Dec 12, 2024</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900">Barangay Assembly Meeting</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Monthly barangay assembly meeting scheduled for December 20, 2024. All residents are encouraged to attend.
                    </p>
                    <button class="p-0 text-green-600 hover:text-green-800 font-semibold">Read More →</button>
                </div>

                <div class="p-6 hover:shadow-xl transition-all duration-300 border-0 shadow-md bg-gradient-to-br from-white to-purple-50 rounded-lg">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-purple-600 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-md text-sm">Dec 10, 2024</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900">Free Medical Check-up</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Free medical check-up and consultation available at the barangay health center every Tuesday and Thursday.
                    </p>
                    <button class="p-0 text-purple-600 hover:text-purple-800 font-semibold">Read More →</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Events & Projects Timeline -->
    <section id="events" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Community Timeline</h2>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">Track our progress and upcoming initiatives that make our barangay a better place to live.</p>
            </div>
            <div class="max-w-4xl mx-auto">
                <div class="relative">
                    <div class="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-blue-600"></div>
                    
                    <div class="relative flex justify-end mb-8">
                        <div class="w-5/12 bg-white p-8 rounded-2xl shadow-lg mr-8 border border-blue-100">
                            <h3 class="text-xl font-bold mb-3 text-slate-900">Christmas Festival</h3>
                            <p class="text-slate-600 leading-relaxed">Annual Christmas celebration with cultural performances, food stalls, and activities for the whole family.</p>
                        </div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 -translate-y-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                            Dec 2024
                        </div>
                    </div>

                    <div class="relative flex justify-start mb-8">
                        <div class="w-5/12 bg-white p-8 rounded-2xl shadow-lg ml-8 border border-green-100">
                            <h3 class="text-xl font-bold mb-3 text-slate-900">Road Improvement Project</h3>
                            <p class="text-slate-600 leading-relaxed">Major road repairs and improvements completed on Main Street and surrounding areas.</p>
                        </div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 -translate-y-2 bg-gradient-to-r from-green-600 to-green-700 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                            Nov 2024
                        </div>
                    </div>

                    <div class="relative flex justify-end">
                        <div class="w-5/12 bg-white p-8 rounded-2xl shadow-lg mr-8 border border-purple-100">
                            <h3 class="text-xl font-bold mb-3 text-slate-900">Youth Skills Training</h3>
                            <p class="text-slate-600 leading-relaxed">Computer literacy and vocational training program for barangay youth aged 16-25.</p>
                        </div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 -translate-y-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                            Oct 2024
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-gradient-to-br from-blue-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Simple. Secure. Accessible.</h2>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">Get started with digital barangay services in just four easy steps.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center group">
                    <div class="relative inline-block mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-br from-blue-700 to-blue-800 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
                            1
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900">Create Account</h3>
                    <p class="text-slate-600 leading-relaxed">Register with your basic information to access secure digital barangay services and stay connected with your community.</p>
                </div>

                <div class="text-center group">
                    <div class="relative inline-block mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-br from-green-700 to-green-800 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
                            2
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900">Secure Access</h3>
                    <p class="text-slate-600 leading-relaxed">Log in to your personalized dashboard with secure authentication and access your service history.</p>
                </div>

                <div class="text-center group">
                    <div class="relative inline-block mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V9a2 2 0 01-2 2H4a2 2 0 01-2-2V7a2 2 0 012-2h2m6 0h2a2 2 0 012 2v2a2 2 0 01-2 2h-2m-6 0v6a2 2 0 002 2h4a2 2 0 002-2v-6"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-br from-purple-700 to-purple-800 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
                            3
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900">Stay Informed</h3>
                    <p class="text-slate-600 leading-relaxed">Get real-time updates on community announcements, events, and important notices directly to your dashboard.</p>
                </div>

                <div class="text-center group">
                    <div class="relative inline-block mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-br from-orange-700 to-orange-800 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
                            4
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900">Access Services</h3>
                    <p class="text-slate-600 leading-relaxed">Request documents, submit concerns, and access various barangay services seamlessly through our digital platform.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Connect With Us</h2>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">Have questions or need assistance? We're here to help you navigate our digital services.</p>
            </div>
            <div class="grid lg:grid-cols-2 gap-16">
                <div>
                    <h3 class="text-2xl font-bold mb-8 text-slate-900">Reach Out Today</h3>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4 p-4 rounded-xl bg-blue-50">
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-600 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Barangay Hall</p>
                                <p class="text-slate-600">Main Street, District Centro</p>
                                <p class="text-slate-600">Your City, Philippines 1234</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 rounded-xl bg-green-50">
                            <div class="flex items-center justify-center w-12 h-12 bg-green-600 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Phone Support</p>
                                <p class="text-slate-600">+63 (02) 123-4567</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 rounded-xl bg-purple-50">
                            <div class="flex items-center justify-center w-12 h-12 bg-purple-600 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Email Support</p>
                                <p class="text-slate-600">support@barangaylink.gov.ph</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 rounded-xl bg-orange-50">
                            <div class="flex items-center justify-center w-12 h-12 bg-orange-600 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Office Hours</p>
                                <p class="text-slate-600">Monday - Friday: 8:00 AM - 5:00 PM</p>
                                <p class="text-slate-600">Saturday: 8:00 AM - 12:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'ContactForm.php'; renderContactForm(); ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-600 rounded-lg">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xl font-bold">BarangayLink</span>
                            <div class="text-xs text-blue-400 -mt-1">Your Direct Link to Local Updates and Services</div>
                        </div>
                    </div>
                    <p class="text-slate-400 mb-6 leading-relaxed">
                        Empowering communities through digital innovation, transparency, and accessible governance solutions.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold mb-6 text-white">Quick Navigation</h4>
                    <ul class="space-y-3 text-slate-400">
                        <li><button onclick="scrollToSection('home')" class="hover:text-blue-400 transition-colors hover:translate-x-1 transform duration-200">Home</button></li>
                        <li><button onclick="scrollToSection('announcements')" class="hover:text-blue-400 transition-colors hover:translate-x-1 transform duration-200">Announcements</button></li>
                        <li><button onclick="scrollToSection('events')" class="hover:text-blue-400 transition-colors hover:translate-x-1 transform duration-200">Events</button></li>
                        <li><button onclick="scrollToSection('contact')" class="hover:text-blue-400 transition-colors hover:translate-x-1 transform duration-200">Contact</button></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-6 text-white">Digital Services</h4>
                    <ul class="space-y-3 text-slate-400">
                        <li><a href="#" class="hover:text-blue-400 transition-colors hover:translate-x-1 transform duration-200">Document Requests</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors hover:translate-x-1 transform duration-200">Community Reports</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors hover:translate-x-1 transform duration-200">Online Certification</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors hover:translate-x-1 transform duration-200">24/7 Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-6 text-white">Community Info</h4>
                    <div class="text-slate-400 space-y-3">
                        <p><span class="font-semibold text-white">Captain:</span> Hon. Juan Dela Cruz</p>
                        <p><span class="font-semibold text-white">Population:</span> 15,247 residents</p>
                        <p><span class="font-semibold text-white">Area:</span> 2.5 square kilometers</p>
                        <p><span class="font-semibold text-white">Established:</span> March 1985</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-12 pt-8 text-center text-slate-400">
                <p>&copy; 2024 BarangayLink Your Direct Link to Local Updates and Services. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Auth Modal -->
    <?php include 'AuthModal.php'; renderAuthModal(); ?>
</div>

<script>
let isMenuOpen = false;

function toggleMobileMenu() {
    isMenuOpen = !isMenuOpen;
    const menu = document.getElementById('mobile-menu');
    const line1 = document.getElementById('menu-line-1');
    const line2 = document.getElementById('menu-line-2');
    const line3 = document.getElementById('menu-line-3');
    
    if (isMenuOpen) {
        menu.classList.remove('hidden');
        line1.style.transform = 'rotate(45deg) translateY(10px)';
        line2.style.opacity = '0';
        line3.style.transform = 'rotate(-45deg) translateY(-10px)';
    } else {
        menu.classList.add('hidden');
        line1.style.transform = '';
        line2.style.opacity = '1';
        line3.style.transform = '';
    }
}

function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
    if (isMenuOpen) {
        toggleMobileMenu();
    }
}

function openAuthModal() {
    // Will be implemented in AuthModal.php
    showAuthModal();
}
</script>
<?php
}
?>