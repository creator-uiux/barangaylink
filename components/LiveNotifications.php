<?php
/**
 * Live Notifications Component - PHP Version
 * Real-time notification system with badge and dropdown
 */

function LiveNotifications($userEmail, $userRole) {
    $notifications = loadJsonData('notifications');
    
    // Filter notifications for current user
    $userNotifications = array_filter($notifications, function($n) use ($userEmail, $userRole) {
        // Admin sees all notifications, users see their own + general announcements
        return $userRole === 'admin' || !$n['userId'] || $n['userId'] === $userEmail;
    });
    
    // Count unread notifications
    $unreadCount = count(array_filter($userNotifications, function($n) {
        return !$n['read'];
    }));
    
    ob_start();
?>
<div class="relative" x-data="{ open: false }">
    <!-- Notification Bell -->
    <button 
        @click="open = !open"
        class="relative p-2 text-blue-400 hover:text-blue-600 transition-colors duration-200 rounded-lg hover:bg-blue-50"
        type="button"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.405-3.405A2.032 2.032 0 0116 12.596V11a8 8 0 10-16 0v1.596c0 .738-.267 1.452-.746 2.006L16 17h-1zm-6-5h2m-1 6v1a2 2 0 002 2 2 2 0 002-2v-1"/>
        </svg>
        <?php if ($unreadCount > 0): ?>
            <div class="absolute -top-1 -right-1 min-w-[20px] h-5 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                <span class="text-xs text-white font-medium px-1">
                    <?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?>
                </span>
            </div>
        <?php endif; ?>
    </button>

    <!-- Notification Dropdown -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.outside="open = false"
        class="absolute right-0 top-full mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-blue-100 z-50 max-h-96 overflow-hidden"
        style="display: none;"
    >
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="font-bold text-blue-900">
                📢 Notifications 
                <?php if ($unreadCount > 0): ?>
                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                        <?php echo $unreadCount; ?> new
                    </span>
                <?php endif; ?>
            </h3>
            <div class="flex items-center space-x-2">
                <?php if ($unreadCount > 0): ?>
                    <button 
                        onclick="markAllNotificationsRead()"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors"
                    >
                        Mark all read
                    </button>
                <?php endif; ?>
                <button 
                    @click="open = false"
                    class="text-blue-400 hover:text-blue-600 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-80 overflow-y-auto" id="notifications-list">
            <?php if (empty($userNotifications)): ?>
                <div class="p-8 text-center text-blue-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.405-3.405A2.032 2.032 0 0116 12.596V11a8 8 0 10-16 0v1.596c0 .738-.267 1.452-.746 2.006L16 17"/>
                    </svg>
                    <p class="font-medium">No notifications yet</p>
                    <p class="text-sm text-blue-400 mt-1">You'll see updates here</p>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($userNotifications, 0, 10) as $notification): ?>
                    <div 
                        class="p-4 border-b border-blue-50 hover:bg-blue-25 transition-colors cursor-pointer group <?php echo !$notification['read'] ? 'bg-blue-50/50 border-l-4 border-l-blue-500' : ''; ?>"
                        onclick="markNotificationRead('<?php echo $notification['id']; ?>')"
                    >
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0 mt-1">
                                <?php
                                $iconColor = match($notification['type']) {
                                    'success' => 'text-green-500',
                                    'warning' => 'text-yellow-500', 
                                    'error' => 'text-red-500',
                                    default => 'text-blue-500'
                                };
                                ?>
                                <div class="w-8 h-8 rounded-full flex items-center justify-center <?php echo match($notification['type']) {
                                    'success' => 'bg-green-100',
                                    'warning' => 'bg-yellow-100',
                                    'error' => 'bg-red-100', 
                                    default => 'bg-blue-100'
                                }; ?>">
                                    <?php if ($notification['type'] === 'success'): ?>
                                        <svg class="w-4 h-4 <?php echo $iconColor; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    <?php elseif ($notification['type'] === 'warning'): ?>
                                        <svg class="w-4 h-4 <?php echo $iconColor; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    <?php elseif ($notification['type'] === 'error'): ?>
                                        <svg class="w-4 h-4 <?php echo $iconColor; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-4 h-4 <?php echo $iconColor; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-blue-900 <?php echo !$notification['read'] ? 'font-bold' : ''; ?>">
                                        <?php echo htmlspecialchars($notification['title']); ?>
                                    </p>
                                    <?php if (!$notification['read']): ?>
                                        <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 animate-pulse"></div>
                                    <?php endif; ?>
                                </div>
                                <p class="text-sm text-blue-700 mt-1 line-clamp-2">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                </p>
                                <div class="flex items-center mt-2 text-xs text-blue-500">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <?php echo timeAgo($notification['timestamp']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <?php if (!empty($userNotifications)): ?>
            <div class="p-3 border-t border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <button class="w-full text-sm text-center text-blue-600 hover:text-blue-800 py-2 font-medium transition-colors rounded-lg hover:bg-blue-100">
                    🔍 View all notifications
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Notification management functions
function markNotificationRead(notificationId) {
    fetch('?ajax=true&action=mark_notifications_read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ids=' + JSON.stringify([notificationId])
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the notifications UI
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

function markAllNotificationsRead() {
    const allNotificationIds = Array.from(document.querySelectorAll('[onclick*="markNotificationRead"]'))
        .map(el => el.getAttribute('onclick').match(/'([^']+)'/)[1]);
    
    fetch('?ajax=true&action=mark_notifications_read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ids=' + JSON.stringify(allNotificationIds)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the notifications UI
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
    })
    .catch(error => console.error('Error marking all notifications as read:', error));
}

// Auto-refresh notifications every 30 seconds
setInterval(function() {
    fetch('?ajax=true&action=get_notifications')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                // Update notification badge if there are new notifications
                const currentCount = document.querySelector('.min-w-\\[20px\\]');
                const newUnreadCount = data.data.filter(n => !n.read).length;
                
                if (currentCount && newUnreadCount > 0) {
                    currentCount.querySelector('span').textContent = newUnreadCount > 99 ? '99+' : newUnreadCount;
                }
            }
        })
        .catch(error => console.log('Notification refresh failed:', error));
}, 30000);
</script>
<?php
    return ob_get_clean();
}
?>