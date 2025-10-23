<?php
/**
 * User Dashboard Component - EXACT MATCH to UserDashboard.tsx
 * Real-time dashboard with live statistics and activity tracking
 */

function UserDashboard($user) {
    // Get real-time user statistics
    $concerns = json_decode(file_get_contents(__DIR__ . '/../data/concerns.json'), true) ?: [];
    $documents = json_decode(file_get_contents(__DIR__ . '/../data/requests.json'), true) ?: [];
    
    $userConcerns = array_filter($concerns, fn($c) => $c['submittedByEmail'] === $user['email']);
    $userDocuments = array_filter($documents, fn($d) => $d['requestedByEmail'] === $user['email']);
    
    $pendingConcerns = count(array_filter($userConcerns, fn($c) => $c['status'] === 'pending'));
    $processingDocs = count(array_filter($userDocuments, fn($d) => $d['status'] === 'processing'));
    
    $stats = [
        'concerns' => count($userConcerns),
        'documents' => count($userDocuments),
        'notifications' => $pendingConcerns + $processingDocs
    ];
    
    ob_start();
?>
<div class="space-y-6 sm:space-y-8">
    <!-- Real-time Welcome Section -->
    <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute top-0 right-0 w-32 sm:w-48 lg:w-64 h-32 sm:h-48 lg:h-64 bg-white/10 rounded-full -translate-y-16 sm:-translate-y-24 lg:-translate-y-32 translate-x-16 sm:translate-x-24 lg:translate-x-32"></div>
        <div class="absolute bottom-0 left-0 w-24 sm:w-36 lg:w-48 h-24 sm:h-36 lg:h-48 bg-white/5 rounded-full translate-y-12 sm:translate-y-18 lg:translate-y-24 -translate-x-12 sm:-translate-x-18 lg:-translate-x-24"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-6 space-y-4 lg:space-y-0">
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mb-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-white/20 rounded-xl sm:rounded-2xl flex items-center justify-center backdrop-blur-lg">
                            <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">
                                <?php echo strtoupper(substr($user['first_name'] ?? $user['name'] ?? 'U', 0, 1)); ?>
                            </span>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1">
                                Welcome back, <?php 
                                    $nameParts = explode(' ', $user['name'] ?? 'User');
                                    $firstName = !empty($nameParts) ? $nameParts[0] : 'User';
                                    echo $user['first_name'] ?? $firstName; 
                                ?>!
                            </h2>
                            <p class="text-sm sm:text-base text-blue-100">Let's make today productive</p>
                        </div>
                    </div>
                    <p class="text-sm sm:text-base lg:text-lg text-blue-100 max-w-2xl leading-relaxed">
                        Access all barangay services and stay connected with your community. 
                        Your digital governance journey continues here.
                    </p>
                </div>
                
                <div class="text-left lg:text-right">
                    <div class="flex items-center space-x-2 lg:justify-end mb-1">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg sm:text-xl lg:text-2xl font-bold text-white" id="current-time"></p>
                    </div>
                    <p class="text-xs sm:text-sm text-blue-200" id="current-date"></p>
                    <div class="flex items-center space-x-1 lg:justify-end mt-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-xs text-green-300">Live updates</span>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mt-6 sm:mt-8">
                <div class="bg-white/10 backdrop-blur-lg rounded-xl lg:rounded-2xl p-3 sm:p-4 border border-white/20">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-500/20 rounded-lg sm:rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm sm:text-base">Account Status</p>
                            <p class="text-green-400 text-xs sm:text-sm">Verified Resident</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white/10 backdrop-blur-lg rounded-xl lg:rounded-2xl p-3 sm:p-4 border border-white/20">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500/20 rounded-lg sm:rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm sm:text-base">Last Activity</p>
                            <p class="text-blue-300 text-xs sm:text-sm">Active now</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white/10 backdrop-blur-lg rounded-xl lg:rounded-2xl p-3 sm:p-4 border border-white/20 sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-500/20 rounded-lg sm:rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm sm:text-base">Notifications</p>
                            <p class="text-purple-300 text-xs sm:text-sm"><?php echo $stats['notifications']; ?> pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- My Concerns -->
        <div class="group bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-gray-200/50 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-gray-700 font-medium">My Concerns</h4>
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mb-3">
                <p class="text-4xl font-bold text-gray-800 mb-1"><?php echo $stats['concerns']; ?></p>
                <p class="text-sm text-gray-600"><?php echo $pendingConcerns; ?> pending</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="flex items-center space-x-1 text-xs font-medium text-green-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7" />
                    </svg>
                    <span>Real-time</span>
                </div>
            </div>
        </div>

        <!-- Document Requests -->
        <div class="group bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-gray-200/50 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-gray-700 font-medium">Document Requests</h4>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="mb-3">
                <p class="text-4xl font-bold text-gray-800 mb-1"><?php echo $stats['documents']; ?></p>
                <p class="text-sm text-gray-600"><?php echo $processingDocs; ?> processing</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="flex items-center space-x-1 text-xs font-medium text-gray-500">
                    <span>Updated now</span>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="group bg-white/80 backdrop-blur-lg rounded-2xl p-6 border border-gray-200/50 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-gray-700 font-medium">System Status</h4>
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="mb-3">
                <p class="text-4xl font-bold text-gray-800 mb-1">Online</p>
                <p class="text-sm text-gray-600">All services available</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="flex items-center space-x-1 text-xs font-medium text-green-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7" />
                    </svg>
                    <span>Live</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Recent Activity -->
    <div class="bg-white/80 backdrop-blur-lg rounded-3xl p-8 border border-gray-200/50 shadow-xl">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Recent Activity</h3>
                <p class="text-gray-600">Keep track of your latest interactions</p>
            </div>
            <button class="px-4 py-2 bg-blue-100 text-blue-600 rounded-xl hover:bg-blue-200 transition-colors text-sm font-medium">
                View All
            </button>
        </div>
        <div class="space-y-6">
            <!-- Activity Item 1 -->
            <div class="flex items-center justify-between p-4 bg-white/50 rounded-2xl border border-gray-200/50 hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Concern submitted: Street lighting issue</p>
                        <p class="text-sm text-gray-500">2 hours ago</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-medium border bg-yellow-100 text-yellow-700 border-yellow-200">
                    pending
                </span>
            </div>

            <!-- Activity Item 2 -->
            <div class="flex items-center justify-between p-4 bg-white/50 rounded-2xl border border-gray-200/50 hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Document request: Barangay Clearance</p>
                        <p class="text-sm text-gray-500">1 day ago</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-medium border bg-blue-100 text-blue-700 border-blue-200">
                    processing
                </span>
            </div>

            <!-- Activity Item 3 -->
            <div class="flex items-center justify-between p-4 bg-white/50 rounded-2xl border border-gray-200/50 hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Concern resolved: Garbage collection</p>
                        <p class="text-sm text-gray-500">3 days ago</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-medium border bg-green-100 text-green-700 border-green-200">
                    resolved
                </span>
            </div>

            <!-- Activity Item 4 -->
            <div class="flex items-center justify-between p-4 bg-white/50 rounded-2xl border border-gray-200/50 hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Profile information updated</p>
                        <p class="text-sm text-gray-500">1 week ago</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-medium border bg-emerald-100 text-emerald-700 border-emerald-200">
                    completed
                </span>
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Actions -->
    <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl p-8 border border-gray-200/50">
        <div class="text-center mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Quick Actions</h3>
            <p class="text-gray-600">Access your most used services instantly</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Submit Concern -->
            <button onclick="window.location.href='?view=submit-concern'" class="group bg-white/80 backdrop-blur-lg rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-200/50 hover:shadow-xl transition-all duration-300 transform hover:scale-105 text-left">
                <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">Submit Concern</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Report community issues</p>
                </div>
                <div class="mt-4 flex items-center text-gray-500 group-hover:text-blue-600 transition-colors">
                    <span class="text-xs font-medium">Access now</span>
                    <svg class="w-3 h-3 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>

            <!-- Request Document -->
            <button onclick="window.location.href='?view=document-request'" class="group bg-white/80 backdrop-blur-lg rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-200/50 hover:shadow-xl transition-all duration-300 transform hover:scale-105 text-left">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">Request Document</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Get official certificates</p>
                </div>
                <div class="mt-4 flex items-center text-gray-500 group-hover:text-blue-600 transition-colors">
                    <span class="text-xs font-medium">Access now</span>
                    <svg class="w-3 h-3 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>

            <!-- Community Directory -->
            <button onclick="window.location.href='?view=community-directory'" class="group bg-white/80 backdrop-blur-lg rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-200/50 hover:shadow-xl transition-all duration-300 transform hover:scale-105 text-left">
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">Community Directory</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Contact officials</p>
                </div>
                <div class="mt-4 flex items-center text-gray-500 group-hover:text-blue-600 transition-colors">
                    <span class="text-xs font-medium">Access now</span>
                    <svg class="w-3 h-3 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>

            <!-- Emergency Alerts -->
            <button onclick="window.location.href='?view=emergency-alerts'" class="group bg-white/80 backdrop-blur-lg rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-200/50 hover:shadow-xl transition-all duration-300 transform hover:scale-105 text-left">
                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-pink-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">Emergency Alerts</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Stay informed</p>
                </div>
                <div class="mt-4 flex items-center text-gray-500 group-hover:text-blue-600 transition-colors">
                    <span class="text-xs font-medium">Access now</span>
                    <svg class="w-3 h-3 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
// Real-time clock update
function updateTime() {
    const now = new Date();
    const timeElement = document.getElementById('current-time');
    const dateElement = document.getElementById('current-date');
    
    if (timeElement) {
        timeElement.textContent = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit'
        });
    }
    
    if (dateElement) {
        dateElement.textContent = now.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }
}

// Update immediately and then every second
updateTime();
setInterval(updateTime, 1000);
</script>
<?php
    return ob_get_clean();
}
?>