<?php
/**
 * User Layout - EXACT MATCH to UserLayout.tsx
 * Receives EXACT same props as TSX version:
 * - user, currentView, onViewChange, onLogout, isMobileMenuOpen, setIsMobileMenuOpen
 * - children (rendered content)
 */

function UserLayout($user, $currentView, $children = '') {
    ob_start();
?>
<div class="min-h-screen bg-gray-50">
    <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <button
                        onclick="setIsMobileMenuOpen(!window.AppState.isMobileMenuOpen)"
                        class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="hidden sm:block ml-3">
                            <h1 class="text-xl font-semibold text-gray-900">BarangayLink</h1>
                            <p class="text-sm text-gray-600">Digital Governance Platform</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') ?: $user['name'] ?? 'User'); ?></p>
                            <p class="text-xs text-gray-500">Verified Resident</p>
                        </div>
                    </div>

                    <?php
                    // Load LiveNotifications component
                    require_once __DIR__ . '/../LiveNotifications.php';
                    echo LiveNotifications($user['email'] ?? '', $user['role'] ?? 'user');
                    ?>

                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium"><?php echo strtoupper(substr($user['first_name'] ?? $user['name'] ?? 'U', 0, 1)); ?></span>
                    </div>
                    
                    <button
                        onclick="handleLogout()"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Sign out</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="flex min-h-[calc(100vh-4rem)]">
        <!-- Mobile overlay -->
        <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="setIsMobileMenuOpen(false)"></div>

        <!-- Sidebar -->
        <aside id="mobile-sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 sm:w-72 bg-white border-r border-gray-200 shadow-sm transform transition-transform duration-300 lg:transform-none -translate-x-full lg:translate-x-0 top-16 lg:top-0 h-[calc(100vh-4rem)] lg:h-full">
            <div class="p-4 sm:p-6 h-full overflow-y-auto">
                <div class="mb-6 sm:mb-8">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Resident Portal</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Your Digital Services</p>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-medium text-blue-700">System Online</span>
                        </div>
                        <p class="text-xs text-blue-600 mt-1">All services operational</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <button onclick="onViewChange('dashboard'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'dashboard' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <div>
                            <span class="font-medium">Dashboard</span>
                            <p class="text-sm <?php echo $currentView === 'dashboard' ? 'text-blue-100' : 'text-gray-500'; ?>">Overview & Activity</p>
                        </div>
                    </button>

                    <button onclick="onViewChange('document-request'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'document-request' ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <span class="font-medium">Request Document</span>
                            <p class="text-sm <?php echo $currentView === 'document-request' ? 'text-green-100' : 'text-gray-500'; ?>">Certificates & Permits</p>
                        </div>
                    </button>

                    <button onclick="onViewChange('submit-concern'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'submit-concern' ? 'bg-yellow-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <span class="font-medium">Submit Concern</span>
                            <p class="text-sm <?php echo $currentView === 'submit-concern' ? 'text-yellow-100' : 'text-gray-500'; ?>">Report Issues</p>
                        </div>
                    </button>

                    <button onclick="onViewChange('community-directory'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'community-directory' ? 'bg-purple-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <div>
                            <span class="font-medium">Community Directory</span>
                            <p class="text-sm <?php echo $currentView === 'community-directory' ? 'text-purple-100' : 'text-gray-500'; ?>">Contacts & Info</p>
                        </div>
                    </button>

                    <button onclick="onViewChange('emergency-alerts'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'emergency-alerts' ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <span class="font-medium">Emergency Alerts</span>
                            <p class="text-sm <?php echo $currentView === 'emergency-alerts' ? 'text-red-100' : 'text-gray-500'; ?>">Safety & Updates</p>
                        </div>
                    </button>

                    <button onclick="onViewChange('information-hub'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'information-hub' ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-medium">Information Hub</span>
                            <p class="text-sm <?php echo $currentView === 'information-hub' ? 'text-indigo-100' : 'text-gray-500'; ?>">News & Announcements</p>
                        </div>
                    </button>

                    <div class="border-t border-gray-200 my-4"></div>

                    <button onclick="onViewChange('profile'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'profile' ? 'bg-gray-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <div>
                            <span class="font-medium">My Profile</span>
                            <p class="text-sm <?php echo $currentView === 'profile' ? 'text-gray-100' : 'text-gray-500'; ?>">Account Settings</p>
                        </div>
                    </button>
                </nav>
            </div>
        </aside>

        <main class="flex-1 p-3 sm:p-4 lg:p-6 overflow-y-auto">
            <div class="max-w-7xl mx-auto">
                <!-- EXACT match to App.tsx children rendering with Suspense -->
                <?php echo $children; ?>
            </div>
        </main>
    </div>
</div>
<?php
    return ob_get_clean();
}
?>