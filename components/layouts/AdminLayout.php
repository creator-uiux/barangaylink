<?php
/**
 * Admin Layout - EXACT MATCH to AdminLayout.tsx
 * Receives EXACT same props as TSX version:
 * - user, currentView, onViewChange, onLogout, isMobileMenuOpen, setIsMobileMenuOpen
 * - children (rendered content)
 */

function AdminLayout($user, $currentView, $children = '') {
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
                            <p class="text-sm text-gray-600">Admin Portal</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></p>
                            <p class="text-xs text-gray-500">System Administrator</p>
                        </div>
                    </div>

                    <div class="relative">
                        <button class="p-2 text-gray-400 hover:text-gray-600 relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5-5 5h5z"/>
                            </svg>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                        </button>
                    </div>

                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Admin Panel</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Management Console</p>
                        </div>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <button onclick="onViewChange('admin-dashboard'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'admin-dashboard' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <div>
                            <span class="font-medium">Dashboard</span>
                            <p class="text-sm <?php echo $currentView === 'admin-dashboard' ? 'text-blue-100' : 'text-gray-500'; ?>">Overview & Analytics</p>
                        </div>
                    </button>

                    <button onclick="onViewChange('manage-documents'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'manage-documents' ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <span class="font-medium">Manage Documents</span>
                            <p class="text-sm <?php echo $currentView === 'manage-documents' ? 'text-green-100' : 'text-gray-500'; ?>">Document Requests</p>
                        </div>
                    </button>

                    <button onclick="onViewChange('manage-concerns'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'manage-concerns' ? 'bg-yellow-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <span class="font-medium">Manage Concerns</span>
                            <p class="text-sm <?php echo $currentView === 'manage-concerns' ? 'text-yellow-100' : 'text-gray-500'; ?>">Community Issues</p>
                        </div>
                    </button>

                    <button onclick="onViewChange('manage-users'); setIsMobileMenuOpen(false);" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo $currentView === 'manage-users' ? 'bg-purple-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197v1a6 6 0 01-3-5.197m0 0v-5.197m0 5.197H9m6 0a6 6 0 01-6 0" />
                        </svg>
                        <div>
                            <span class="font-medium">Manage Users</span>
                            <p class="text-sm <?php echo $currentView === 'manage-users' ? 'text-purple-100' : 'text-gray-500'; ?>">User Accounts</p>
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