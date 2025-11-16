<?php
/**
 * User Layout - SYNCHRONIZED with components/layouts/UserLayout.tsx
 */
if (!isAuthenticated() || !$user) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarangayLink - Resident Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 1s linear infinite; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <button
                            onclick="toggleMobileMenu()"
                            class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="hidden sm:block ml-3">
                                <h1 class="text-xl font-semibold text-gray-900">BarangayLink</h1>
                                <p class="text-sm text-gray-600">Resident Portal</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="hidden md:flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></p>
                                <p class="text-xs text-gray-500">Verified Resident</p>
                            </div>
                        </div>

                        <!-- Real-time Notifications -->
                        <div class="relative" id="notifications-container">
                            <button onclick="toggleNotifications()" class="relative p-2 text-gray-400 hover:text-gray-500 transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <div id="notification-badge" class="hidden absolute -top-1 -right-1 min-w-[20px] h-5 bg-red-500 rounded-full flex items-center justify-center">
                                    <span class="text-xs text-white font-medium px-1" id="notification-count">0</span>
                                </div>
                            </button>

                            <!-- Notification Dropdown -->
                            <div id="notification-dropdown" class="hidden absolute right-0 top-full mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden">
                                <!-- Header -->
                                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                                    <h3 class="font-semibold text-gray-900">
                                        Notifications (<span id="unread-count">0</span> new)
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        <button onclick="markAllAsRead()" id="mark-all-read-btn" class="hidden text-sm text-blue-600 hover:text-blue-700">
                                            Mark all read
                                        </button>
                                        <button onclick="toggleNotifications()" class="text-gray-400 hover:text-gray-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Notifications List -->
                                <div class="max-h-80 overflow-y-auto" id="notifications-list">
                                    <div class="p-6 text-center text-gray-500">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        <p>No notifications yet</p>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div id="notification-footer" class="hidden p-3 border-t border-gray-200 bg-gray-50">
                                    <button class="w-full text-sm text-center text-blue-600 hover:text-blue-700 py-2">
                                        View all notifications
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                        </div>
                        
                        <form method="POST" action="index.php" class="inline">
                            <input type="hidden" name="action" value="logout">
                            <button
                                type="submit"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
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
            <!-- Mobile Menu Overlay -->
            <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleMobileMenu()"></div>

            <!-- Sidebar -->
            <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 sm:w-72 bg-white border-r border-gray-200 shadow-sm transform transition-transform duration-300 lg:transform-none -translate-x-full lg:translate-x-0 top-16 lg:top-0 h-[calc(100vh-4rem)] lg:h-full">
                <div class="p-4 sm:p-6 h-full overflow-y-auto">
                    <div class="mb-6 sm:mb-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Services</h3>
                                <p class="text-xs sm:text-sm text-gray-600">Resident Portal</p>
                            </div>
                        </div>
                    </div>
                    
                    <nav class="space-y-2">
                        <a href="?view=dashboard" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'dashboard') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Dashboard</span>
                                <p class="text-sm <?php echo ($view === 'dashboard') ? 'text-blue-100' : 'text-gray-500'; ?>">Home & Updates</p>
                            </div>
                        </a>

                        <a href="?view=profile" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'profile') ? 'bg-purple-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">My Profile</span>
                                <p class="text-sm <?php echo ($view === 'profile') ? 'text-purple-100' : 'text-gray-500'; ?>">Account Settings</p>
                            </div>
                        </a>

                        <a href="?view=document-request" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'document-request') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Request Document</span>
                                <p class="text-sm <?php echo ($view === 'document-request') ? 'text-green-100' : 'text-gray-500'; ?>">Barangay Clearance</p>
                            </div>
                        </a>

                        <a href="?view=submit-concern" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'submit-concern') ? 'bg-yellow-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Submit Concern</span>
                                <p class="text-sm <?php echo ($view === 'submit-concern') ? 'text-yellow-100' : 'text-gray-500'; ?>">Report Issues</p>
                            </div>
                        </a>

                        <a href="?view=community-directory" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'community-directory') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Community Directory</span>
                                <p class="text-sm <?php echo ($view === 'community-directory') ? 'text-indigo-100' : 'text-gray-500'; ?>">Local Contacts</p>
                            </div>
                        </a>

                        <a href="?view=emergency-alerts" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'emergency-alerts') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Emergency Alerts</span>
                                <p class="text-sm <?php echo ($view === 'emergency-alerts') ? 'text-red-100' : 'text-gray-500'; ?>">Safety Updates</p>
                            </div>
                        </a>

                        <a href="?view=information-hub" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors <?php echo ($view === 'information-hub') ? 'bg-cyan-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <span class="font-medium">Information Hub</span>
                                <p class="text-sm <?php echo ($view === 'information-hub') ? 'text-cyan-100' : 'text-gray-500'; ?>">Policies & Guide</p>
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
                        // Map view names to actual file names (keep original names for user views)
                        $actualView = $view;
                        $viewFile = "views/user/{$actualView}.php";
                        
                        if (file_exists($viewFile)) {
                            include $viewFile;
                        } else {
                            echo '<div class="text-center py-12">';
                            echo '<h2 class="text-2xl font-bold text-gray-800 mb-2">Page Not Found</h2>';
                            echo '<p class="text-gray-600">The requested page does not exist.</p>';
                            echo '<p class="text-sm text-gray-500 mt-2">Looking for: ' . htmlspecialchars($viewFile) . '</p>';
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

        // Close mobile menu when clicking a link
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleMobileMenu();
                }
            });
        });

        // Real-time Notifications System - SYNCHRONIZED with LiveNotifications.tsx
        const userEmail = '<?php echo addslashes($user['email']); ?>';
        let notifications = [];
        let isNotificationOpen = false;

        function toggleNotifications() {
            const dropdown = document.getElementById('notification-dropdown');
            isNotificationOpen = !isNotificationOpen;
            
            if (isNotificationOpen) {
                dropdown.classList.remove('hidden');
                // Add backdrop
                if (!document.getElementById('notification-backdrop')) {
                    const backdrop = document.createElement('div');
                    backdrop.id = 'notification-backdrop';
                    backdrop.className = 'fixed inset-0 z-40';
                    backdrop.onclick = toggleNotifications;
                    document.body.appendChild(backdrop);
                }
            } else {
                dropdown.classList.add('hidden');
                const backdrop = document.getElementById('notification-backdrop');
                if (backdrop) backdrop.remove();
            }
        }

        function timeAgo(date) {
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + " years ago";
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + " months ago";
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + " days ago";
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + " hours ago";
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + " minutes ago";
            return "Just now";
        }

        function getNotificationIcon(type) {
            const icons = {
                success: '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                warning: '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                error: '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                info: '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
            };
            return icons[type] || icons.info;
        }

        function loadNotifications() {
            fetch('/api/notifications.php?user_email=' + encodeURIComponent(userEmail))
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.notifications) {
                        notifications = data.notifications;
                        updateNotificationUI();
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        function updateNotificationUI() {
            const unreadCount = notifications.filter(n => !n.read).length;
            const badge = document.getElementById('notification-badge');
            const countSpan = document.getElementById('notification-count');
            const unreadCountSpan = document.getElementById('unread-count');
            const markAllBtn = document.getElementById('mark-all-read-btn');
            const notificationsList = document.getElementById('notifications-list');
            const footer = document.getElementById('notification-footer');

            // Update badge
            if (unreadCount > 0) {
                badge.classList.remove('hidden');
                countSpan.textContent = unreadCount > 99 ? '99+' : unreadCount;
                markAllBtn.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
                markAllBtn.classList.add('hidden');
            }

            unreadCountSpan.textContent = unreadCount;

            // Update notifications list
            if (notifications.length === 0) {
                notificationsList.innerHTML = `
                    <div class="p-6 text-center text-gray-500">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <p>No notifications yet</p>
                    </div>
                `;
                footer.classList.add('hidden');
            } else {
                notificationsList.innerHTML = notifications.map(notification => `
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer ${!notification.read ? 'bg-blue-50/50' : ''}"
                         onclick="markAsRead('${notification.id}')">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                ${getNotificationIcon(notification.type)}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 ${!notification.read ? 'font-semibold' : ''}">
                                        ${notification.title}
                                    </p>
                                    ${!notification.read ? '<div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>' : ''}
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    ${notification.message}
                                </p>
                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    ${timeAgo(notification.timestamp)}
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
                footer.classList.remove('hidden');
            }
        }

        function markAsRead(notificationId) {
            fetch('/api/notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'mark_read',
                    notification_id: notificationId,
                    user_email: userEmail
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update local state
                    notifications = notifications.map(n => 
                        n.id === notificationId ? { ...n, read: true } : n
                    );
                    updateNotificationUI();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        function markAllAsRead() {
            fetch('/api/notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'mark_all_read',
                    user_email: userEmail
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update local state
                    notifications = notifications.map(n => ({ ...n, read: true }));
                    updateNotificationUI();
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        }

        // Load notifications on page load
        loadNotifications();

        // Refresh notifications every 30 seconds for real-time updates
        setInterval(loadNotifications, 30000);
    </script>
</body>
</html>