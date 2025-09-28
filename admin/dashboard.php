<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$pageTitle = 'Admin Dashboard';
$user = getCurrentUser();

// Get statistics
try {
    $db = Database::getInstance()->getConnection();
    
    // Total users
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
    $stmt->execute();
    $totalUsers = $stmt->fetch()['count'];
    
    // Total document requests
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM document_requests");
    $stmt->execute();
    $totalDocumentRequests = $stmt->fetch()['count'];
    
    // Pending document requests
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM document_requests WHERE status = 'pending'");
    $stmt->execute();
    $pendingDocumentRequests = $stmt->fetch()['count'];
    
    // Total concerns
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM concerns");
    $stmt->execute();
    $totalConcerns = $stmt->fetch()['count'];
    
    // Unresolved concerns
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM concerns WHERE status NOT IN ('resolved', 'closed')");
    $stmt->execute();
    $unresolvedConcerns = $stmt->fetch()['count'];
    
    // Recent activities
    $stmt = $db->prepare("
        SELECT al.*, u.full_name, u.email 
        FROM activity_logs al 
        JOIN users u ON al.user_id = u.id 
        ORDER BY al.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recentActivities = $stmt->fetchAll();
    
    // Recent document requests
    $stmt = $db->prepare("
        SELECT dr.*, u.full_name, u.email 
        FROM document_requests dr 
        JOIN users u ON dr.user_id = u.id 
        ORDER BY dr.requested_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recentDocumentRequests = $stmt->fetchAll();
    
    // Recent concerns
    $stmt = $db->prepare("
        SELECT c.*, u.full_name, u.email 
        FROM concerns c 
        JOIN users u ON c.user_id = u.id 
        ORDER BY c.submitted_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recentConcerns = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Admin dashboard error: " . $e->getMessage());
    $totalUsers = $totalDocumentRequests = $pendingDocumentRequests = 0;
    $totalConcerns = $unresolvedConcerns = 0;
    $recentActivities = $recentDocumentRequests = $recentConcerns = [];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 text-white p-2 rounded-lg">
                        <i data-lucide="shield" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">BarangayLink</h1>
                        <p class="text-sm text-gray-600">Admin Dashboard</p>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="/admin/dashboard.php" class="text-blue-600 font-medium">Dashboard</a>
                    <a href="/admin/users.php" class="text-gray-600 hover:text-gray-900">Users</a>
                    <a href="/admin/documents.php" class="text-gray-600 hover:text-gray-900">Documents</a>
                    <a href="/admin/concerns.php" class="text-gray-600 hover:text-gray-900">Concerns</a>
                    <a href="/admin/announcements.php" class="text-gray-600 hover:text-gray-900">Announcements</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, <?php echo htmlspecialchars($user['full_name']); ?></span>
                    <div class="relative">
                        <button type="button" onclick="toggleUserMenu()" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                            <div class="bg-blue-100 text-blue-600 p-2 rounded-full">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                        
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-2 z-50">
                            <button onclick="openModal('profileModal')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="user" class="w-4 h-4 inline mr-2"></i>
                                Profile
                            </button>
                            <a href="/auth/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="log-out" class="w-4 h-4 inline mr-2"></i>
                                Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <div class="md:hidden bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-6 py-2 overflow-x-auto">
                <a href="/admin/dashboard.php" class="text-blue-600 font-medium whitespace-nowrap">Dashboard</a>
                <a href="/admin/users.php" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Users</a>
                <a href="/admin/documents.php" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Documents</a>
                <a href="/admin/concerns.php" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Concerns</a>
                <a href="/admin/announcements.php" class="text-gray-600 hover:text-gray-900 whitespace-nowrap">Announcements</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600">Monitor and manage barangay services and community activities.</p>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo number_format($totalUsers); ?></h3>
                        <p class="text-sm text-gray-600">Total Users</p>
                    </div>
                </div>
            </div>
            
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo number_format($totalDocumentRequests); ?></h3>
                        <p class="text-sm text-gray-600">Document Requests</p>
                        <?php if ($pendingDocumentRequests > 0): ?>
                            <p class="text-xs text-orange-600"><?php echo $pendingDocumentRequests; ?> pending</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-lg">
                        <i data-lucide="alert-circle" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo number_format($totalConcerns); ?></h3>
                        <p class="text-sm text-gray-600">Total Concerns</p>
                        <?php if ($unresolvedConcerns > 0): ?>
                            <p class="text-xs text-red-600"><?php echo $unresolvedConcerns; ?> unresolved</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card p-6">
                <button onclick="openModal('announcementModal')" class="w-full text-left">
                    <div class="flex items-center">
                        <div class="bg-orange-100 text-orange-600 p-3 rounded-lg">
                            <i data-lucide="megaphone" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Create</h3>
                            <p class="text-sm text-gray-600">New Announcement</p>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Recent Document Requests -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Document Requests</h2>
                    <a href="/admin/documents.php" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                </div>
                
                <?php if (empty($recentDocumentRequests)): ?>
                    <div class="text-center py-8">
                        <i data-lucide="file-text" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-600">No document requests yet.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recentDocumentRequests as $request): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">
                                        <?php echo ucfirst(str_replace('_', ' ', $request['document_type'])); ?>
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        <?php echo htmlspecialchars($request['full_name']); ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <?php echo formatDate($request['requested_at'], 'M j, Y g:i A'); ?>
                                    </p>
                                </div>
                                <span class="badge <?php echo getStatusBadgeClass($request['status']); ?>">
                                    <?php echo ucfirst($request['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Concerns -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Concerns</h2>
                    <a href="/admin/concerns.php" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                </div>
                
                <?php if (empty($recentConcerns)): ?>
                    <div class="text-center py-8">
                        <i data-lucide="alert-circle" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-600">No concerns reported yet.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recentConcerns as $concern): ?>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($concern['title']); ?></h4>
                                    <div class="flex space-x-1">
                                        <span class="badge <?php echo getPriorityBadgeClass($concern['priority']); ?>">
                                            <?php echo ucfirst($concern['priority']); ?>
                                        </span>
                                        <span class="badge <?php echo getStatusBadgeClass($concern['status']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $concern['status'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">
                                    <?php echo htmlspecialchars($concern['full_name']); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?php echo formatDate($concern['submitted_at'], 'M j, Y g:i A'); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
            
            <?php if (empty($recentActivities)): ?>
                <div class="text-center py-8">
                    <i data-lucide="activity" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-600">No recent activity.</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="bg-blue-100 text-blue-600 p-2 rounded-full">
                                <i data-lucide="activity" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">
                                    <span class="font-medium"><?php echo htmlspecialchars($activity['full_name']); ?></span>
                                    <?php echo htmlspecialchars($activity['action']); ?>
                                </p>
                                <?php if ($activity['description']): ?>
                                    <p class="text-xs text-gray-600">
                                        <?php echo htmlspecialchars($activity['description']); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="text-xs text-gray-500">
                                    <?php echo formatDate($activity['created_at'], 'M j, Y g:i A'); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Include Modals -->
<?php 
require_once __DIR__ . '/modals/profile-modal.php';
require_once __DIR__ . '/modals/announcement-modal.php';
?>

<script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('hidden');
    }
    
    // Close user menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('userMenu');
        const button = event.target.closest('button');
        
        if (!button || !button.onclick || button.onclick.toString().indexOf('toggleUserMenu') === -1) {
            menu.classList.add('hidden');
        }
    });
    
    // Auto-refresh for real-time updates
    startAutoRefresh(60000); // Refresh every minute
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>