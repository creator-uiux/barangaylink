<?php
/**
 * Emergency Alerts Component - Real-time alerts with news, weather, and emergency information
 * Displays important community alerts and emergency information with live data
 */

require_once __DIR__ . '/../functions/realtime_service.php';

function EmergencyAlerts() {
    // Get real-time alerts
    $realtimeAlerts = getRealtimeAlerts();
    
    // Get static alerts for fallback
    $staticAlerts = [
        [
            'id' => 'static_1',
            'type' => 'info',
            'title' => 'Community Assembly',
            'message' => 'Monthly community assembly scheduled for October 5, 2025 at 6:00 PM at the Multi-Purpose Hall.',
            'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'status' => 'active',
            'source' => 'community',
            'priority' => 'low'
        ],
        [
            'id' => 'static_2',
            'type' => 'resolved',
            'title' => 'Road Repair Completed',
            'message' => 'The road repair on Main Street has been completed. Normal traffic flow has resumed.',
            'timestamp' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'status' => 'resolved',
            'source' => 'community',
            'priority' => 'low'
        ]
    ];
    
    // Combine real-time and static alerts
    $alerts = array_merge($realtimeAlerts, $staticAlerts);
    
    // Sort by priority and timestamp
    usort($alerts, function($a, $b) {
        $priorityOrder = ['emergency' => 4, 'warning' => 3, 'info' => 2, 'resolved' => 1];
        $aPriority = $priorityOrder[$a['type']] ?? 0;
        $bPriority = $priorityOrder[$b['type']] ?? 0;
        
        if ($aPriority === $bPriority) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        }
        return $bPriority - $aPriority;
    });
    
    ob_start();
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-blue-900">Real-time Emergency Alerts</h2>
                    <p class="text-sm text-blue-600">Live updates on weather, news, and community alerts</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <div class="flex items-center space-x-1 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span>Live</span>
                </div>
                <button onclick="refreshAlerts()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Category Filters -->
    <div class="bg-white rounded-lg p-4 border border-blue-100">
        <div class="flex flex-wrap gap-2">
            <button onclick="filterAlerts('all')" class="filter-btn active px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700 border border-blue-200">
                All Alerts
            </button>
            <button onclick="filterAlerts('weather')" class="filter-btn px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200">
                Weather
            </button>
            <button onclick="filterAlerts('news')" class="filter-btn px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200">
                News
            </button>
            <button onclick="filterAlerts('emergency')" class="filter-btn px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200">
                Emergency
            </button>
            <button onclick="filterAlerts('community')" class="filter-btn px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200">
                Community
            </button>
        </div>
    </div>

    <!-- Active Alerts -->
    <div id="alerts-container" class="space-y-4">
        <?php foreach ($alerts as $alert): ?>
            <?php echo AlertCard($alert); ?>
        <?php endforeach; ?>
    </div>

    <!-- Safety Tips -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-blue-900 mb-4">Safety Tips</h3>
        <ul class="space-y-2 text-blue-700">
            <li class="flex items-start space-x-2">
                <span>•</span>
                <span>Keep emergency contact numbers readily available</span>
            </li>
            <li class="flex items-start space-x-2">
                <span>•</span>
                <span>Prepare an emergency kit with essentials (water, food, first aid, flashlight)</span>
            </li>
            <li class="flex items-start space-x-2">
                <span>•</span>
                <span>Stay informed by checking alerts regularly</span>
            </li>
            <li class="flex items-start space-x-2">
                <span>•</span>
                <span>Follow official instructions from barangay officials during emergencies</span>
            </li>
        </ul>
    </div>
</div>

<script>
// Auto-refresh functionality
let refreshInterval;
let currentFilter = 'all';

// Start auto-refresh every 5 minutes
function startAutoRefresh() {
    refreshInterval = setInterval(() => {
        refreshAlerts();
    }, 300000); // 5 minutes
}

// Stop auto-refresh
function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

// Refresh alerts
function refreshAlerts() {
    const container = document.getElementById('alerts-container');
    if (!container) return;
    
    // Show loading state
    container.innerHTML = '<div class="flex items-center justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
    
    // Fetch new alerts
    fetch('?action=get_realtime_alerts', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            container.innerHTML = data.html;
            applyFilter(currentFilter);
        } else {
            container.innerHTML = '<div class="text-center p-8 text-gray-500">Failed to load alerts. Please try again.</div>';
        }
    })
    .catch(error => {
        console.error('Error refreshing alerts:', error);
        container.innerHTML = '<div class="text-center p-8 text-gray-500">Error loading alerts. Please try again.</div>';
    });
}

// Filter alerts by category
function filterAlerts(category) {
    currentFilter = category;
    
    // Update filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-100', 'text-blue-700', 'border-blue-200');
        btn.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');
    });
    
    event.target.classList.add('active', 'bg-blue-100', 'text-blue-700', 'border-blue-200');
    event.target.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-200');
    
    // Show/hide alerts based on category
    const alerts = document.querySelectorAll('.alert-card');
    alerts.forEach(alert => {
        const source = alert.getAttribute('data-source');
        if (category === 'all' || source === category) {
            alert.style.display = 'block';
        } else {
            alert.style.display = 'none';
        }
    });
}

// Apply current filter
function applyFilter(category) {
    const alerts = document.querySelectorAll('.alert-card');
    alerts.forEach(alert => {
        const source = alert.getAttribute('data-source');
        if (category === 'all' || source === category) {
            alert.style.display = 'block';
        } else {
            alert.style.display = 'none';
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
    
    // Add click handlers to filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.textContent.trim().toLowerCase();
            if (category === 'all alerts') {
                filterAlerts('all');
            } else {
                filterAlerts(category);
            }
        });
    });
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});
</script>
<?php
    return ob_get_clean();
}

function AlertCard($alert) {
    // Get alert styling based on type
    $styles = [
        'emergency' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'iconBg' => 'bg-red-100',
            'iconColor' => 'text-red-600',
            'titleColor' => 'text-red-900',
            'textColor' => 'text-red-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />'
        ],
        'warning' => [
            'bg' => 'bg-orange-50',
            'border' => 'border-orange-200',
            'iconBg' => 'bg-orange-100',
            'iconColor' => 'text-orange-600',
            'titleColor' => 'text-orange-900',
            'textColor' => 'text-orange-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />'
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'iconBg' => 'bg-blue-100',
            'iconColor' => 'text-blue-600',
            'titleColor' => 'text-blue-900',
            'textColor' => 'text-blue-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
        ],
        'resolved' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'iconBg' => 'bg-green-100',
            'iconColor' => 'text-green-600',
            'titleColor' => 'text-green-900',
            'textColor' => 'text-green-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
        ]
    ];
    
    $style = $styles[$alert['type']] ?? $styles['info'];
    
    // Get source icon and color
    $sourceInfo = getSourceInfo($alert['source'] ?? 'community');
    
    // Format timestamp
    $timestamp = formatTimestamp($alert['timestamp']);
    
    ob_start();
?>
<div class="alert-card <?php echo $style['bg']; ?> border <?php echo $style['border']; ?> rounded-lg p-6" data-source="<?php echo $alert['source'] ?? 'community'; ?>" data-type="<?php echo $alert['type']; ?>">
    <div class="flex items-start space-x-4">
        <div class="<?php echo $style['iconBg']; ?> rounded-lg p-3 flex-shrink-0">
            <svg class="w-6 h-6 <?php echo $style['iconColor']; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <?php echo $style['icon']; ?>
            </svg>
        </div>
        <div class="flex-1">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <h3 class="<?php echo $style['titleColor']; ?>"><?php echo htmlspecialchars($alert['title']); ?></h3>
                    <?php if (isset($alert['priority']) && $alert['priority'] === 'high'): ?>
                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">HIGH PRIORITY</span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs <?php echo $style['textColor']; ?>"><?php echo $timestamp; ?></span>
                    <div class="flex items-center space-x-1 text-xs <?php echo $sourceInfo['color']; ?>">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?php echo $sourceInfo['icon']; ?>
                        </svg>
                        <span><?php echo ucfirst($alert['source'] ?? 'community'); ?></span>
                    </div>
                </div>
            </div>
            <p class="<?php echo $style['textColor']; ?> mb-3"><?php echo htmlspecialchars($alert['message']); ?></p>
            <?php if (isset($alert['url']) && $alert['url']): ?>
                <a href="<?php echo htmlspecialchars($alert['url']); ?>" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                    Read more
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    return ob_get_clean();
}

function getSourceInfo($source) {
    $sources = [
        'weather' => [
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.998 2.998 0 00-2.12 4.5A4.002 4.002 0 003 15z" />',
            'color' => 'text-blue-600'
        ],
        'news' => [
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />',
            'color' => 'text-green-600'
        ],
        'emergency' => [
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
            'color' => 'text-red-600'
        ],
        'community' => [
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
            'color' => 'text-purple-600'
        ]
    ];
    
    return $sources[$source] ?? $sources['community'];
}

function formatTimestamp($timestamp) {
    $time = strtotime($timestamp);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $time);
    }
}
?>