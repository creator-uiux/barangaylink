<?php
/**
 * Admin Layout - SYNCHRONIZED with components/layouts/AdminLayout.tsx
 */
if (!isAuthenticated() || $user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarangayLink - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-shimmer {
            animation: shimmer 2s infinite;
        }
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
            pointer-events: none;
        }
        .toast {
            pointer-events: auto;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            margin-bottom: 0.5rem;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .toast.hiding {
            animation: slideOut 0.3s ease-out forwards;
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        .toast-success {
            border-left: 4px solid #10b981;
        }
        .toast-error {
            border-left: 4px solid #ef4444;
        }
        .toast-info {
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-gray-900 to-gray-800 border-b border-gray-700 shadow-lg sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <button onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div class="hidden sm:block ml-3">
                                <h1 class="text-xl font-semibold text-white">BarangayLink</h1>
                                <p class="text-sm text-gray-300">Administration</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="hidden md:flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($user['name'] ?? 'Admin User'); ?></p>
                                <p class="text-xs text-gray-300">System Administrator</p>
                            </div>
                        </div>

                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium"><?php echo strtoupper(substr($user['name'] ?? 'A', 0, 1)); ?></span>
                        </div>
                        
                        <form method="POST" action="index.php" class="inline">
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span class="hidden sm:inline">Sign out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex min-h-[calc(100vh-4rem)]">
            <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleMobileMenu()"></div>

            <!-- Sidebar -->
            <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 sm:w-72 bg-gray-900 border-r border-gray-700 shadow-lg transform transition-transform duration-300 lg:transform-none -translate-x-full lg:translate-x-0 top-16 lg:top-0 h-[calc(100vh-4rem)] lg:h-full">
                <div class="p-4 sm:p-6 h-full overflow-y-auto">
                    <div class="mb-6 sm:mb-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-white">Admin Panel</h3>
                                <p class="text-xs sm:text-sm text-gray-400">Management Console</p>
                            </div>
                        </div>
                    </div>
                    
                    <nav class="space-y-2">
                        <a href="?view=admin-dashboard" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'admin-dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Dashboard</span>
                                <p class="text-sm <?php echo ($view === 'admin-dashboard') ? 'text-blue-100' : 'text-gray-500'; ?>">Overview & Stats</p>
                            </div>
                        </a>

                        <a href="?view=manage-documents" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'manage-documents') ? 'bg-green-600 text-white' : 'text-gray-300 hover:bg-gray-800'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Documents</span>
                                <p class="text-sm <?php echo ($view === 'manage-documents') ? 'text-green-100' : 'text-gray-500'; ?>">Manage Requests</p>
                            </div>
                        </a>

                        <a href="?view=manage-concerns" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'manage-concerns') ? 'bg-yellow-600 text-white' : 'text-gray-300 hover:bg-gray-800'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Concerns</span>
                                <p class="text-sm <?php echo ($view === 'manage-concerns') ? 'text-yellow-100' : 'text-gray-500'; ?>">Resolve Issues</p>
                            </div>
                        </a>

                        <a href="?view=manage-users" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'manage-users') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-800'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Users</span>
                                <p class="text-sm <?php echo ($view === 'manage-users') ? 'text-purple-100' : 'text-gray-500'; ?>">User Management</p>
                            </div>
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-3 sm:p-4 lg:p-6 overflow-y-auto">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-6">
                        <?php
                        // Map view names to actual file names
                        $viewMap = [
                            'admin-dashboard' => 'dashboard',
                            'manage-documents' => 'document-management',
                            'manage-concerns' => 'concern-management',
                            'manage-users' => 'user-management'
                        ];
                        
                        $actualView = $viewMap[$view] ?? $view;
                        $viewFile = "views/admin/{$actualView}.php";
                        
                        if (file_exists($viewFile)) {
                            include $viewFile;
                        } else {
                            echo '<div class="text-center py-12">';
                            echo '<h2 class="text-2xl font-bold text-gray-800 mb-2">Page Not Found</h2>';
                            echo '<p class="text-gray-600">The requested page does not exist.</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Toast Notification System - EXACT MATCH with TSX toast
        function showToast(message, type = 'success', description = '') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icons = {
                success: '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                error: '<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                info: '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };
            
            toast.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        ${icons[type] || icons.info}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${message}</p>
                        ${description ? `<p class="text-sm text-gray-600 mt-1">${description}</p>` : ''}
                    </div>
                    <button onclick="this.closest('.toast').remove()" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }, 5000);
        }

        // Global toast function for compatibility
        window.toast = {
            success: (message, options = {}) => showToast(message, 'success', options.description || ''),
            error: (message, options = {}) => showToast(message, 'error', options.description || ''),
            info: (message, options = {}) => showToast(message, 'info', options.description || '')
        };
    </script>
</body>
</html>