<?php
/**
 * Admin Dashboard - PERFECTLY SYNCHRONIZED with components/AdminDashboard.tsx
 * EXACT MATCH in design, layout, and functionality
 */

$conn = getDBConnection();

// Get all data for statistics
$usersResult = $conn->query("SELECT * FROM users");
$users = $usersResult ? $usersResult->fetch_all(MYSQLI_ASSOC) : [];

$documentsResult = $conn->query("SELECT * FROM documents ORDER BY created_at DESC");
$documents = $documentsResult ? $documentsResult->fetch_all(MYSQLI_ASSOC) : [];

$concernsResult = $conn->query("SELECT * FROM concerns ORDER BY created_at DESC");
$concerns = $concernsResult ? $concernsResult->fetch_all(MYSQLI_ASSOC) : [];

// Calculate statistics
$stats = [
    'totalUsers' => count($users),
    'pendingConcerns' => count(array_filter($concerns, function($c) { return $c['status'] === 'pending'; })),
    'resolvedConcerns' => count(array_filter($concerns, function($c) { return $c['status'] === 'resolved'; })),
    'pendingDocuments' => count(array_filter($documents, function($d) { return $d['status'] === 'pending'; }))
];

// Get concern status counts (handle both formats)
$concernsByStatus = [
    'pending' => count(array_filter($concerns, function($c) { return $c['status'] === 'pending'; })),
    'in-progress' => count(array_filter($concerns, function($c) { return $c['status'] === 'in-progress' || $c['status'] === 'in_progress'; })),
    'resolved' => count(array_filter($concerns, function($c) { return $c['status'] === 'resolved'; })),
    'rejected' => count(array_filter($concerns, function($c) { return $c['status'] === 'rejected'; }))
];

// Prepare recent activity
$recentActivity = [];
foreach (array_slice($concerns, 0, 5) as $concern) {
    $recentActivity[] = [
        'type' => 'concern',
        'title' => 'New concern: ' . $concern['category'],
        'time' => $concern['created_at'],
        'status' => $concern['status']
    ];
}
foreach (array_slice($documents, 0, 5) as $doc) {
    $recentActivity[] = [
        'type' => 'document',
        'title' => 'Document request: ' . $doc['document_type'],
        'time' => $doc['created_at'],
        'status' => $doc['status']
    ];
}

// Sort by time (most recent first)
usort($recentActivity, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});
$recentActivity = array_slice($recentActivity, 0, 10);
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
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
                <h4 class="text-sm font-semibold text-blue-700">Total Users</h4>
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-blue-900"><?php echo $stats['totalUsers']; ?></p>
        </div>

        <!-- Pending Concerns -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-blue-700">Pending Concerns</h4>
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-blue-900"><?php echo $stats['pendingConcerns']; ?></p>
        </div>

        <!-- Resolved Concerns -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-blue-700">Resolved Concerns</h4>
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-blue-900"><?php echo $stats['resolvedConcerns']; ?></p>
        </div>

        <!-- Pending Documents -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-blue-700">Pending Documents</h4>
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-blue-900"><?php echo $stats['pendingDocuments']; ?></p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Concerns by Status -->
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Concerns by Status</h3>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 animate-pulse text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Live</span>
                </div>
            </div>
            <div class="space-y-4">
                <!-- Pending -->
                <?php 
                $totalConcerns = count($concerns);
                $pendingPercentage = $totalConcerns > 0 ? ($concernsByStatus['pending'] / $totalConcerns) * 100 : 0;
                ?>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Pending</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900"><?php echo $concernsByStatus['pending']; ?></span>
                            <span class="text-xs text-gray-500">(<?php echo round($pendingPercentage); ?>%)</span>
                        </div>
                    </div>
                    <div class="relative w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-yellow-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-yellow-500/50" style="width: <?php echo $pendingPercentage; ?>%"></div>
                    </div>
                </div>

                <!-- In Progress -->
                <?php $inProgressPercentage = $totalConcerns > 0 ? ($concernsByStatus['in-progress'] / $totalConcerns) * 100 : 0; ?>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">In Progress</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900"><?php echo $concernsByStatus['in-progress']; ?></span>
                            <span class="text-xs text-gray-500">(<?php echo round($inProgressPercentage); ?>%)</span>
                        </div>
                    </div>
                    <div class="relative w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-blue-500/50" style="width: <?php echo $inProgressPercentage; ?>%"></div>
                    </div>
                </div>

                <!-- Resolved -->
                <?php $resolvedPercentage = $totalConcerns > 0 ? ($concernsByStatus['resolved'] / $totalConcerns) * 100 : 0; ?>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Resolved</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900"><?php echo $concernsByStatus['resolved']; ?></span>
                            <span class="text-xs text-gray-500">(<?php echo round($resolvedPercentage); ?>%)</span>
                        </div>
                    </div>
                    <div class="relative w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-green-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-green-500/50" style="width: <?php echo $resolvedPercentage; ?>%"></div>
                    </div>
                </div>

                <!-- Rejected -->
                <?php $rejectedPercentage = $totalConcerns > 0 ? ($concernsByStatus['rejected'] / $totalConcerns) * 100 : 0; ?>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Rejected</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900"><?php echo $concernsByStatus['rejected']; ?></span>
                            <span class="text-xs text-gray-500">(<?php echo round($rejectedPercentage); ?>%)</span>
                        </div>
                    </div>
                    <div class="relative w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-red-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-red-500/50" style="width: <?php echo $rejectedPercentage; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <h3 class="text-xl font-bold text-blue-900 mb-4">Recent Activity</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                <?php if (count($recentActivity) > 0): ?>
                    <?php foreach ($recentActivity as $activity): ?>
                        <div class="border-b border-blue-50 pb-3 last:border-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-blue-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                                    <p class="text-xs text-blue-600 mt-1"><?php echo date('M j, Y g:i A', strtotime($activity['time'])); ?></p>
                                </div>
                                <?php
                                $status = $activity['status'];
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'in-progress' => 'bg-blue-100 text-blue-700',
                                    'resolved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'processing' => 'bg-blue-100 text-blue-700'
                                ];
                                $statusClass = $statusColors[$status] ?? $statusColors['pending'];
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center py-6 text-blue-600">No recent activity</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <h3 class="text-xl font-bold text-blue-900 mb-4">Quick Actions</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <button onclick="window.location.href='?view=manage-concerns'" class="bg-red-50 text-red-600 hover:bg-red-100 p-4 rounded-lg transition-colors text-left w-full">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-1">View All Concerns</h4>
                        <p class="text-xs opacity-80">Manage community concerns</p>
                    </div>
                </div>
            </button>

            <button onclick="window.location.href='?view=manage-documents'" class="bg-blue-50 text-blue-600 hover:bg-blue-100 p-4 rounded-lg transition-colors text-left w-full">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-1">View Document Requests</h4>
                        <p class="text-xs opacity-80">Process pending requests</p>
                    </div>
                </div>
            </button>

            <button onclick="window.location.href='?view=manage-users'" class="bg-green-50 text-green-600 hover:bg-green-100 p-4 rounded-lg transition-colors text-left w-full">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-1">Manage Users</h4>
                        <p class="text-xs opacity-80">View registered users</p>
                    </div>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
// Real-time clock
function updateClock() {
    const now = new Date();
    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
    const dateOptions = { year: 'numeric', month: 'short', day: 'numeric' };
    
    document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions);
    document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions);
}

// Update clock immediately and every second
updateClock();
setInterval(updateClock, 1000);

// Auto-refresh page every 30 seconds to update stats
setTimeout(() => {
    location.reload();
}, 30000);
</script>