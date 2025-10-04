<?php
/**
 * Admin Dashboard Component - EXACT MATCH to AdminDashboard.tsx
 * Real-time admin overview with live statistics
 */

function AdminDashboard() {
    // Load real-time data
    $concerns = json_decode(file_get_contents(__DIR__ . '/../data/concerns.json'), true) ?: [];
    $users = json_decode(file_get_contents(__DIR__ . '/../data/users.json'), true) ?: [];
    $documentRequests = json_decode(file_get_contents(__DIR__ . '/../data/requests.json'), true) ?: [];
    
    $stats = [
        'totalUsers' => count($users),
        'pendingConcerns' => count(array_filter($concerns, fn($c) => $c['status'] === 'pending')),
        'resolvedConcerns' => count(array_filter($concerns, fn($c) => $c['status'] === 'resolved')),
        'pendingDocuments' => count(array_filter($documentRequests, fn($d) => $d['status'] === 'pending'))
    ];
    
    // Recent activity
    $recentActivity = [];
    foreach (array_slice(array_reverse($concerns), 0, 5) as $c) {
        $recentActivity[] = [
            'type' => 'concern',
            'title' => 'New concern: ' . $c['subject'],
            'time' => date('M d, Y h:i A', strtotime($c['createdAt'])),
            'status' => $c['status']
        ];
    }
    foreach (array_slice(array_reverse($documentRequests), 0, 5) as $d) {
        $recentActivity[] = [
            'type' => 'document',
            'title' => 'Document request: ' . $d['documentType'],
            'time' => date('M d, Y h:i A', strtotime($d['createdAt'])),
            'status' => $d['status']
        ];
    }
    usort($recentActivity, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));
    $recentActivity = array_slice($recentActivity, 0, 10);
    
    ob_start();
?>
<div class="space-y-4 sm:space-y-6">
    <!-- Real-time Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
        <div>
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900 mb-1">Admin Dashboard</h2>
            <p class="text-sm sm:text-base text-blue-600">Real-time overview of barangay operations and activities</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-green-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-sm text-green-600 font-medium">Live</span>
            </div>
            <div class="text-right">
                <p class="text-lg font-semibold text-gray-900" id="current-time"></p>
                <p class="text-xs text-gray-500" id="current-date"></p>
            </div>
        </div>
    </div>

    <!-- Real-time Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-blue-700">Total Users</h4>
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <p class="text-3xl text-blue-900"><?php echo $stats['totalUsers']; ?></p>
        </div>

        <!-- Pending Concerns -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-blue-700">Pending Concerns</h4>
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-3xl text-blue-900"><?php echo $stats['pendingConcerns']; ?></p>
        </div>

        <!-- Resolved Concerns -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-blue-700">Resolved Concerns</h4>
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-3xl text-blue-900"><?php echo $stats['resolvedConcerns']; ?></p>
        </div>

        <!-- Pending Documents -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-blue-700">Pending Documents</h4>
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-3xl text-blue-900"><?php echo $stats['pendingDocuments']; ?></p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Concerns by Status -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <h3 class="text-blue-900 mb-4">Concerns by Status</h3>
            <div class="space-y-3">
                <?php
                $total = count($concerns);
                $statuses = [
                    ['label' => 'Pending', 'filter' => 'pending', 'color' => 'bg-yellow-500'],
                    ['label' => 'In Progress', 'filter' => 'in-progress', 'color' => 'bg-blue-500'],
                    ['label' => 'Resolved', 'filter' => 'resolved', 'color' => 'bg-green-500'],
                    ['label' => 'Rejected', 'filter' => 'rejected', 'color' => 'bg-red-500']
                ];
                
                foreach ($statuses as $s):
                    $count = count(array_filter($concerns, fn($c) => $c['status'] === $s['filter']));
                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-blue-700"><?php echo $s['label']; ?></span>
                        <span class="text-sm text-blue-900"><?php echo $count; ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="<?php echo $s['color']; ?> h-2 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <h3 class="text-blue-900 mb-4">Recent Activity</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                <?php if (!empty($recentActivity)): ?>
                    <?php foreach ($recentActivity as $activity): ?>
                    <div class="border-b border-blue-50 pb-3 last:border-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-blue-900 text-sm"><?php echo htmlspecialchars($activity['title']); ?></p>
                                <p class="text-xs text-blue-600 mt-1"><?php echo htmlspecialchars($activity['time']); ?></p>
                            </div>
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'in-progress' => 'bg-blue-100 text-blue-700',
                                'resolved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'processing' => 'bg-blue-100 text-blue-700'
                            ];
                            $colorClass = $statusColors[$activity['status']] ?? $statusColors['pending'];
                            ?>
                            <span class="px-2 py-1 rounded-full text-xs <?php echo $colorClass; ?>">
                                <?php echo htmlspecialchars($activity['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-blue-600 text-center py-6">No recent activity</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <h3 class="text-blue-900 mb-4">Quick Actions</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <!-- View All Concerns -->
            <button onclick="window.location.href='?view=manage-concerns'" class="bg-red-50 text-red-600 hover:bg-red-100 p-4 rounded-lg transition-colors text-left w-full">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="mb-1">View All Concerns</h4>
                        <p class="text-xs opacity-80">Manage community concerns</p>
                    </div>
                </div>
            </button>

            <!-- View Document Requests -->
            <button onclick="window.location.href='?view=manage-documents'" class="bg-blue-50 text-blue-600 hover:bg-blue-100 p-4 rounded-lg transition-colors text-left w-full">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="mb-1">View Document Requests</h4>
                        <p class="text-xs opacity-80">Process pending requests</p>
                    </div>
                </div>
            </button>

            <!-- Manage Users -->
            <button onclick="window.location.href='?view=manage-users'" class="bg-green-50 text-green-600 hover:bg-green-100 p-4 rounded-lg transition-colors text-left w-full">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="mb-1">Manage Users</h4>
                        <p class="text-xs opacity-80">View registered users</p>
                    </div>
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
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }
}

// Update immediately and then every second
updateTime();
setInterval(updateTime, 1000);

// Auto-refresh page every 30 seconds for real-time data
setInterval(() => {
    window.location.reload();
}, 30000);
</script>
<?php
    return ob_get_clean();
}
?>