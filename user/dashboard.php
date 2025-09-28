<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

// Ensure user is not admin (admins should use admin dashboard)
if (isAdmin()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$pageTitle = 'Dashboard';
$user = getCurrentUser();

// Get user statistics
try {
    $db = Database::getInstance()->getConnection();
    
    // Document requests count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM document_requests WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $documentRequestsCount = $stmt->fetch()['count'];
    
    // Concerns count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM concerns WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $concernsCount = $stmt->fetch()['count'];
    
    // Recent document requests
    $stmt = $db->prepare("SELECT * FROM document_requests WHERE user_id = ? ORDER BY requested_at DESC LIMIT 5");
    $stmt->execute([$user['id']]);
    $recentRequests = $stmt->fetchAll();
    
    // Recent concerns
    $stmt = $db->prepare("SELECT * FROM concerns WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 5");
    $stmt->execute([$user['id']]);
    $recentConcerns = $stmt->fetchAll();
    
    // Recent announcements
    $stmt = $db->prepare("SELECT * FROM announcements WHERE status = 'published' ORDER BY published_at DESC LIMIT 3");
    $stmt->execute();
    $recentAnnouncements = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $documentRequestsCount = 0;
    $concernsCount = 0;
    $recentRequests = [];
    $recentConcerns = [];
    $recentAnnouncements = [];
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
                        <i data-lucide="building" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">BarangayLink</h1>
                        <p class="text-sm text-gray-600">Resident Dashboard</p>
                    </div>
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

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, <?php echo htmlspecialchars(explode(' ', $user['full_name'])[0]); ?>!</h1>
            <p class="text-gray-600">Manage your document requests, report concerns, and stay updated with community announcements.</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo $documentRequestsCount; ?></h3>
                        <p class="text-sm text-gray-600">Document Requests</p>
                    </div>
                </div>
            </div>
            
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg">
                        <i data-lucide="alert-circle" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo $concernsCount; ?></h3>
                        <p class="text-sm text-gray-600">Concerns Reported</p>
                    </div>
                </div>
            </div>
            
            <div class="card p-6">
                <button onclick="openModal('documentRequestModal')" class="w-full text-left">
                    <div class="flex items-center">
                        <div class="bg-purple-100 text-purple-600 p-3 rounded-lg">
                            <i data-lucide="plus" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Request</h3>
                            <p class="text-sm text-gray-600">New Document</p>
                        </div>
                    </div>
                </button>
            </div>
            
            <div class="card p-6">
                <button onclick="openModal('concernModal')" class="w-full text-left">
                    <div class="flex items-center">
                        <div class="bg-orange-100 text-orange-600 p-3 rounded-lg">
                            <i data-lucide="flag" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Report</h3>
                            <p class="text-sm text-gray-600">New Concern</p>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Document Requests -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Document Requests</h2>
                    <a href="/user/documents.php" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                </div>
                
                <?php if (empty($recentRequests)): ?>
                    <div class="text-center py-8">
                        <i data-lucide="file-text" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-600">No document requests yet.</p>
                        <button onclick="openModal('documentRequestModal')" class="mt-2 btn-primary">
                            Request Document
                        </button>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recentRequests as $request): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">
                                        <?php echo ucfirst(str_replace('_', ' ', $request['document_type'])); ?>
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        <?php echo formatDate($request['requested_at'], 'M j, Y'); ?>
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
                    <a href="/user/concerns.php" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                </div>
                
                <?php if (empty($recentConcerns)): ?>
                    <div class="text-center py-8">
                        <i data-lucide="alert-circle" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-600">No concerns reported yet.</p>
                        <button onclick="openModal('concernModal')" class="mt-2 btn-primary">
                            Report Concern
                        </button>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recentConcerns as $concern): ?>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($concern['title']); ?></h4>
                                    <span class="badge <?php echo getStatusBadgeClass($concern['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $concern['status'])); ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">
                                    <?php echo htmlspecialchars(substr($concern['description'], 0, 100) . (strlen($concern['description']) > 100 ? '...' : '')); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?php echo formatDate($concern['submitted_at'], 'M j, Y'); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Announcements -->
        <div class="mt-8">
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Latest Announcements</h2>
                
                <?php if (empty($recentAnnouncements)): ?>
                    <div class="text-center py-8">
                        <i data-lucide="megaphone" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-600">No announcements at this time.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recentAnnouncements as $announcement): ?>
                            <div class="border-l-4 border-blue-500 pl-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="badge <?php echo getPriorityBadgeClass($announcement['priority']); ?>">
                                        <?php echo ucfirst($announcement['priority']); ?>
                                    </span>
                                    <span class="badge bg-blue-100 text-blue-800">
                                        <?php echo ucfirst(str_replace('_', ' ', $announcement['type'])); ?>
                                    </span>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($announcement['title']); ?>
                                </h3>
                                <p class="text-gray-600 mb-2">
                                    <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                    <?php echo formatDate($announcement['published_at']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Include Modals -->
<?php 
require_once __DIR__ . '/modals/profile-modal.php';
require_once __DIR__ . '/modals/document-request-modal.php';
require_once __DIR__ . '/modals/concern-modal.php';
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
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>