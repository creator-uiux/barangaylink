<?php
/**
 * Emergency Alerts Component - EXACT MATCH to EmergencyAlerts.tsx
 * Displays important community alerts and emergency information
 */

function EmergencyAlerts() {
    $alerts = [
        [
            'id' => 1,
            'type' => 'emergency',
            'title' => 'Typhoon Warning',
            'message' => 'Signal No. 2 has been raised. Residents are advised to stay indoors and prepare emergency kits.',
            'timestamp' => '2 hours ago',
            'status' => 'active'
        ],
        [
            'id' => 2,
            'type' => 'warning',
            'title' => 'Water Interruption',
            'message' => 'Scheduled water interruption on October 3, 2025 from 9:00 AM to 3:00 PM for maintenance work.',
            'timestamp' => '5 hours ago',
            'status' => 'active'
        ],
        [
            'id' => 3,
            'type' => 'info',
            'title' => 'Community Assembly',
            'message' => 'Monthly community assembly scheduled for October 5, 2025 at 6:00 PM at the Multi-Purpose Hall.',
            'timestamp' => '1 day ago',
            'status' => 'active'
        ],
        [
            'id' => 4,
            'type' => 'resolved',
            'title' => 'Road Repair Completed',
            'message' => 'The road repair on Main Street has been completed. Normal traffic flow has resumed.',
            'timestamp' => '2 days ago',
            'status' => 'resolved'
        ]
    ];
    
    ob_start();
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h2 class="text-blue-900">Emergency Alerts</h2>
                <p class="text-sm text-blue-600">Stay informed about important community updates</p>
            </div>
        </div>
    </div>

    <!-- Active Alerts -->
    <div class="space-y-4">
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
    
    ob_start();
?>
<div class="<?php echo $style['bg']; ?> border <?php echo $style['border']; ?> rounded-lg p-6">
    <div class="flex items-start space-x-4">
        <div class="<?php echo $style['iconBg']; ?> rounded-lg p-3 flex-shrink-0">
            <svg class="w-6 h-6 <?php echo $style['iconColor']; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <?php echo $style['icon']; ?>
            </svg>
        </div>
        <div class="flex-1">
            <div class="flex items-start justify-between mb-2">
                <h3 class="<?php echo $style['titleColor']; ?>"><?php echo htmlspecialchars($alert['title']); ?></h3>
                <span class="text-xs <?php echo $style['textColor']; ?>"><?php echo htmlspecialchars($alert['timestamp']); ?></span>
            </div>
            <p class="<?php echo $style['textColor']; ?>"><?php echo htmlspecialchars($alert['message']); ?></p>
        </div>
    </div>
</div>
<?php
    return ob_get_clean();
}
?>