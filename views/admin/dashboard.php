<?php
/**
 * Admin Dashboard - PERFECTLY SYNCHRONIZED with components/AdminDashboard.tsx
 * EXACT MATCH in design, layout, and functionality
 */

$db = getDB();

// Get all data for statistics
$users = fetchAll("SELECT * FROM users");
$documents = fetchAll("SELECT * FROM documents ORDER BY created_at DESC");
$concerns = fetchAll("SELECT * FROM concerns ORDER BY created_at DESC");

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

<div class="space-y-6 sm:space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-lg p-6 border border-gray-200 animate-fade-in">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="space-y-2">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">Admin Dashboard</h1>
                        <p class="text-sm sm:text-base text-gray-600 font-medium">Real-time overview of barangay operations</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-6">
                <div class="flex items-center space-x-2 px-4 py-2 bg-blue-50 rounded-full border border-blue-200">
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-semibold text-blue-700">Live Updates</span>
                </div>
                <div class="text-center sm:text-right">
                    <p class="text-xl font-bold text-gray-900" id="current-time">12:00:00 PM</p>
                    <p class="text-sm text-gray-500" id="current-date">Jan 1, 2024</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid with Clean Design -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 animate-fade-in">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">Total Users</h4>
                    <p class="text-xs text-gray-500">Registered residents</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <p class="text-4xl font-bold text-gray-900"><?php echo $stats['totalUsers']; ?></p>
                <div class="flex items-center space-x-1 text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span class="text-sm font-medium">+12%</span>
                </div>
            </div>
        </div>

        <!-- Pending Concerns -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 animate-fade-in" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">Pending Concerns</h4>
                    <p class="text-xs text-gray-500">Awaiting response</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <p class="text-4xl font-bold text-gray-900"><?php echo $stats['pendingConcerns']; ?></p>
                <div class="flex items-center space-x-1 text-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-sm font-medium">Needs attention</span>
                </div>
            </div>
        </div>

        <!-- Resolved Concerns -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 animate-fade-in" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">Resolved Concerns</h4>
                    <p class="text-xs text-gray-500">Successfully handled</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <p class="text-4xl font-bold text-gray-900"><?php echo $stats['resolvedConcerns']; ?></p>
                <div class="flex items-center space-x-1 text-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">+8%</span>
                </div>
            </div>
        </div>

        <!-- Pending Documents -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 animate-fade-in" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">Pending Documents</h4>
                    <p class="text-xs text-gray-500">Document requests</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <p class="text-4xl font-bold text-gray-900"><?php echo $stats['pendingDocuments']; ?></p>
                <div class="flex items-center space-x-1 text-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">Processing</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Concerns by Status -->
        <div class="bg-white rounded-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                    <h3 class="text-2xl font-bold text-gray-900">Concerns by Status</h3>
                </div>
                <div class="flex items-center space-x-2 px-3 py-1 bg-gray-100 rounded-full border border-gray-200">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-sm font-semibold text-green-700">Live</span>
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
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-slate-800">Pending</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-slate-800"><?php echo $concernsByStatus['pending']; ?></span>
                            <span class="text-xs text-slate-500">(<?php echo round($pendingPercentage); ?>%)</span>
                        </div>
                    </div>
                    <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-purple-500/50" style="width: <?php echo $pendingPercentage; ?>%"></div>
                    </div>
                </div>

                <!-- In Progress -->
                <?php $inProgressPercentage = $totalConcerns > 0 ? ($concernsByStatus['in-progress'] / $totalConcerns) * 100 : 0; ?>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            <span class="text-sm font-medium text-slate-800">In Progress</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-slate-800"><?php echo $concernsByStatus['in-progress']; ?></span>
                            <span class="text-xs text-slate-500">(<?php echo round($inProgressPercentage); ?>%)</span>
                        </div>
                    </div>
                    <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-blue-600 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-indigo-500/50" style="width: <?php echo $inProgressPercentage; ?>%"></div>
                    </div>
                </div>

                <!-- Resolved -->
                <?php $resolvedPercentage = $totalConcerns > 0 ? ($concernsByStatus['resolved'] / $totalConcerns) * 100 : 0; ?>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-slate-800">Resolved</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-slate-800"><?php echo $concernsByStatus['resolved']; ?></span>
                            <span class="text-xs text-slate-500">(<?php echo round($resolvedPercentage); ?>%)</span>
                        </div>
                    </div>
                    <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-emerald-500/50" style="width: <?php echo $resolvedPercentage; ?>%"></div>
                    </div>
                </div>

                <!-- Rejected -->
                <?php $rejectedPercentage = $totalConcerns > 0 ? ($concernsByStatus['rejected'] / $totalConcerns) * 100 : 0; ?>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-slate-800">Rejected</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-slate-800"><?php echo $concernsByStatus['rejected']; ?></span>
                            <span class="text-xs text-slate-500">(<?php echo round($rejectedPercentage); ?>%)</span>
                        </div>
                    </div>
                    <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-red-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-red-500/50" style="width: <?php echo $rejectedPercentage; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.5s">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-800">Recent Activity</h3>
            </div>
            <div class="space-y-4 max-h-80 overflow-y-auto">
                <?php if (count($recentActivity) > 0): ?>
                    <?php foreach ($recentActivity as $activity): ?>
                            <div class="border-b border-gray-100 pb-3 last:border-0">
                            <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?php echo date('M j, Y g:i A', strtotime($activity['time'])); ?></p>
                            </div>
                            <?php
                            $status = $activity['status'];
                            $statusColors = [
                                'pending' => 'bg-orange-100 text-gray-800 border-gray-300',
                                'in-progress' => 'bg-yellow-100 text-gray-800 border-gray-300',
                                'resolved' => 'bg-green-100 text-gray-800 border-gray-300',
                                'rejected' => 'bg-red-100 text-gray-800 border-gray-300',
                                'approved' => 'bg-green-100 text-gray-800 border-gray-300',
                                'processing' => 'bg-blue-100 text-gray-800 border-gray-300'
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
                    <p class="text-center py-6 text-gray-600">No recent activity</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Actions -->
    <div class="bg-white rounded-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.6s">
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">Quick Actions</h3>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <button onclick="window.location.href='?view=concern-management'" class="group bg-white p-6 rounded-lg transition-all duration-200 text-left w-full border border-gray-200 hover:border-gray-300">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg mb-2 text-slate-800">View All Concerns</h4>
                        <p class="text-sm text-slate-600">Manage community concerns and reports</p>
                        <div class="mt-3 flex items-center space-x-2">
                            <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-semibold border border-purple-200">Priority</span>
                            <span class="text-xs text-slate-500"><?php echo $stats['pendingConcerns']; ?> pending</span>
                        </div>
                    </div>
                </div>
            </button>

            <button onclick="window.location.href='?view=document-management'" class="group bg-white p-6 rounded-lg transition-all duration-200 text-left w-full border border-gray-200 hover:border-gray-300">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg mb-2 text-slate-800">Document Requests</h4>
                        <p class="text-sm text-slate-600">Process pending document requests</p>
                        <div class="mt-3 flex items-center space-x-2">
                            <span class="px-3 py-1 bg-gradient-to-r from-cyan-50 to-blue-50 text-cyan-700 rounded-full text-xs font-semibold border border-cyan-200/50">Active</span>
                            <span class="text-xs text-slate-500"><?php echo $stats['pendingDocuments']; ?> pending</span>
                        </div>
                    </div>
                </div>
            </button>

            <button onclick="window.location.href='?view=user-management'" class="group bg-white p-6 rounded-lg transition-all duration-200 text-left w-full border border-gray-200 hover:border-gray-300">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-lg flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg mb-2 text-slate-800">Manage Users</h4>
                        <p class="text-sm text-slate-600">View and manage registered users</p>
                        <div class="mt-3 flex items-center space-x-2">
                            <span class="px-3 py-1 bg-violet-50 text-violet-700 rounded-full text-xs font-semibold border border-violet-200">Users</span>
                            <span class="text-xs text-slate-500"><?php echo $stats['totalUsers']; ?> total</span>
                        </div>
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

    // Auto-refresh stats every 60 seconds without page reload
    setInterval(() => {
        // Refresh stats via AJAX
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Extract and update stats from the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newStats = doc.querySelectorAll('.grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4 > div .text-4xl');
            const currentStats = document.querySelectorAll('.grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4 > div .text-4xl');

            newStats.forEach((newStat, index) => {
                if (currentStats[index] && currentStats[index].textContent !== newStat.textContent) {
                    currentStats[index].textContent = newStat.textContent;
                }
            });
        })
        .catch(error => console.log('Stats refresh failed:', error));
    }, 60000);
</script>