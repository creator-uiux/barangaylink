<?php
function renderAdminDashboard() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: App.php');
        exit;
    }
    
    $user = $_SESSION['user'];
    $currentSection = $_GET['section'] ?? 'overview';
    $searchTerm = $_GET['search'] ?? '';
    
    // Load data
    $allRequests = getAllRequests();
    $allConcerns = getAllConcerns();
    $allUsers = getAllUsers();
    
    // Handle status updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_request_status'])) {
            $requestId = intval($_POST['request_id']);
            $newStatus = $_POST['new_status'];
            if (updateRequestStatus($requestId, $newStatus)) {
                echo '<script>showToast("Request status updated successfully", "success");</script>';
            } else {
                echo '<script>showToast("Failed to update request status", "error");</script>';
            }
            $allRequests = getAllRequests(); // Reload data
        }
        
        if (isset($_POST['update_concern_status'])) {
            $concernId = intval($_POST['concern_id']);
            $newStatus = $_POST['new_status'];
            if (updateConcernStatus($concernId, $newStatus)) {
                echo '<script>showToast("Concern status updated successfully", "success");</script>';
            } else {
                echo '<script>showToast("Failed to update concern status", "error");</script>';
            }
            $allConcerns = getAllConcerns(); // Reload data
        }
    }
?>

<div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    <span class="text-xl font-semibold">BarangayLink Admin</span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="App.php?section=overview" class="hover:bg-white/10 px-3 py-2 rounded-md transition-colors <?php echo $currentSection === 'overview' ? 'bg-white/10' : ''; ?>">
                        Dashboard
                    </a>
                    <a href="App.php?section=requests" class="hover:bg-white/10 px-3 py-2 rounded-md transition-colors <?php echo $currentSection === 'requests' ? 'bg-white/10' : ''; ?>">
                        Document Requests
                    </a>
                    <a href="App.php?section=concerns" class="hover:bg-white/10 px-3 py-2 rounded-md transition-colors <?php echo $currentSection === 'concerns' ? 'bg-white/10' : ''; ?>">
                        Concerns
                    </a>
                    <a href="App.php?section=users" class="hover:bg-white/10 px-3 py-2 rounded-md transition-colors <?php echo $currentSection === 'users' ? 'bg-white/10' : ''; ?>">
                        Users
                    </a>
                    <a href="App.php?logout=1" class="text-white hover:bg-white/10 px-3 py-2 rounded-md transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>

                <!-- User Profile -->
                <div class="flex items-center space-x-3">
                    <div class="hidden md:flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Admin: <?php echo explode(' ', $user['fullName'])[0]; ?></span>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button onclick="toggleAdminMobileMenu()" class="p-2 rounded-md hover:bg-white/10">
                            <div class="w-6 h-6 flex flex-col justify-around">
                                <span id="admin-line-1" class="h-0.5 w-6 bg-white transform transition"></span>
                                <span id="admin-line-2" class="h-0.5 w-6 bg-white transition"></span>
                                <span id="admin-line-3" class="h-0.5 w-6 bg-white transform transition"></span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="admin-mobile-menu" class="md:hidden bg-blue-700 border-t border-blue-600 hidden">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="App.php?section=overview" class="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left">Dashboard</a>
                    <a href="App.php?section=requests" class="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left">Document Requests</a>
                    <a href="App.php?section=concerns" class="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left">Concerns</a>
                    <a href="App.php?section=users" class="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left">Users</a>
                    <a href="App.php?logout=1" class="w-full justify-start text-white hover:bg-white/10 px-3 py-2 rounded-md mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Section Content -->
    <?php if ($currentSection === 'overview'): ?>
        <?php renderAdminOverviewSection($allUsers, $allRequests, $allConcerns); ?>
    <?php elseif ($currentSection === 'requests'): ?>
        <?php renderAdminRequestsSection($allRequests, $searchTerm); ?>
    <?php elseif ($currentSection === 'concerns'): ?>
        <?php renderAdminConcernsSection($allConcerns, $searchTerm); ?>
    <?php elseif ($currentSection === 'users'): ?>
        <?php include 'UserManagement.php'; renderUserManagement(); ?>
    <?php endif; ?>
</div>

<script>
let isAdminMobileMenuOpen = false;

function toggleAdminMobileMenu() {
    isAdminMobileMenuOpen = !isAdminMobileMenuOpen;
    const menu = document.getElementById('admin-mobile-menu');
    const line1 = document.getElementById('admin-line-1');
    const line2 = document.getElementById('admin-line-2');
    const line3 = document.getElementById('admin-line-3');
    
    if (isAdminMobileMenuOpen) {
        menu.classList.remove('hidden');
        line1.style.transform = 'rotate(45deg) translateY(10px)';
        line2.style.opacity = '0';
        line3.style.transform = 'rotate(-45deg) translateY(-10px)';
    } else {
        menu.classList.add('hidden');
        line1.style.transform = '';
        line2.style.opacity = '1';
        line3.style.transform = '';
    }
}

function updateRequestStatus(requestId, newStatus) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="update_request_status" value="1">
        <input type="hidden" name="request_id" value="${requestId}">
        <input type="hidden" name="new_status" value="${newStatus}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function updateConcernStatus(concernId, newStatus) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="update_concern_status" value="1">
        <input type="hidden" name="concern_id" value="${concernId}">
        <input type="hidden" name="new_status" value="${newStatus}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php
}

function renderAdminOverviewSection($allUsers, $allRequests, $allConcerns) {
    $pendingRequests = array_filter($allRequests, function($r) { return $r['status'] === 'pending'; });
    
    // Combine recent requests and concerns
    $recentActivity = array_merge(
        array_slice($allRequests, 0, 3),
        array_slice($allConcerns, 0, 3)
    );
    
    // Sort by submission date
    usort($recentActivity, function($a, $b) {
        return strtotime($b['submittedAt']) - strtotime($a['submittedAt']);
    });
    
    $recentActivity = array_slice($recentActivity, 0, 5);
?>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Admin Dashboard</h1>
            <p class="text-gray-600">Monitor and manage all barangay activities</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-blue-600"><?php echo count($allUsers); ?></p>
                        </div>
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Document Requests</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo count($allRequests); ?></p>
                        </div>
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Concerns Submitted</p>
                            <p class="text-2xl font-bold text-orange-600"><?php echo count($allConcerns); ?></p>
                        </div>
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Requests</p>
                            <p class="text-2xl font-bold text-red-600"><?php echo count($pendingRequests); ?></p>
                        </div>
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold">Recent Activity</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($recentActivity as $item): ?>
                        <?php 
                        $user = getUserById($item['userId']);
                        $isRequest = isset($item['documentType']);
                        ?>
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="p-2 rounded-full <?php echo $isRequest ? 'bg-blue-100' : 'bg-orange-100'; ?>">
                                <?php if ($isRequest): ?>
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium">
                                    <?php if ($isRequest): ?>
                                        New document request: <?php echo getDocumentTypeLabel($item['documentType']); ?>
                                    <?php else: ?>
                                        New concern: <?php echo htmlspecialchars($item['concernTitle']); ?>
                                    <?php endif; ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    by <?php echo $user ? htmlspecialchars($user['fullName']) : 'Unknown User'; ?> - <?php echo formatDate($item['submittedAt']); ?>
                                </p>
                            </div>
                            <span class="px-2 py-1 rounded-md text-sm <?php echo getStatusBadgeColor($item['status']); ?>">
                                <?php echo ucfirst($item['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
}

function renderAdminRequestsSection($allRequests, $searchTerm) {
    // Filter requests based on search term
    $filteredRequests = $allRequests;
    if (!empty($searchTerm)) {
        $filteredRequests = array_filter($allRequests, function($request) use ($searchTerm) {
            $user = getUserById($request['userId']);
            return (
                stripos($user['fullName'] ?? '', $searchTerm) !== false ||
                stripos($user['email'] ?? '', $searchTerm) !== false ||
                stripos(getDocumentTypeLabel($request['documentType']), $searchTerm) !== false ||
                stripos($request['purpose'], $searchTerm) !== false
            );
        });
    }
?>

<section class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">Document Requests</h1>
                <p class="text-gray-600">Manage all user document requests</p>
            </div>
            <div class="flex items-center space-x-2 mt-4 md:mt-0">
                <form method="GET" class="relative">
                    <input type="hidden" name="section" value="requests">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        name="search"
                        placeholder="Search requests..."
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        class="pl-10 w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    />
                </form>
            </div>
        </div>

        <?php if (empty($filteredRequests)): ?>
            <div class="bg-white rounded-lg shadow-md text-center py-12">
                <div class="p-6">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold mb-2">No document requests</h3>
                    <p class="text-gray-600">No document requests have been submitted yet.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($filteredRequests as $request): ?>
                    <?php $user = getUserById($request['userId']); ?>
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold mb-1">
                                        <?php echo getDocumentTypeLabel($request['documentType']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Requested by: <?php echo $user ? htmlspecialchars($user['fullName']) : 'Unknown User'; ?> (<?php echo $user ? htmlspecialchars($user['email']) : 'N/A'; ?>)
                                    </p>
                                </div>
                                <div class="flex flex-col md:flex-row gap-2">
                                    <span class="px-2 py-1 rounded-md text-sm <?php echo getStatusBadgeColor($request['status']); ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Purpose</p>
                                    <p class="text-sm text-gray-900"><?php echo htmlspecialchars($request['purpose']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Contact Number</p>
                                    <p class="text-sm text-gray-900"><?php echo htmlspecialchars($request['contactNumber']); ?></p>
                                </div>
                                <?php if (!empty($request['additionalNotes'])): ?>
                                    <div class="md:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Additional Notes</p>
                                        <p class="text-sm text-gray-900"><?php echo htmlspecialchars($request['additionalNotes']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center justify-between pt-4 border-t gap-4">
                                <div class="flex gap-2 flex-wrap">
                                    <button onclick="updateRequestStatus(<?php echo $request['id']; ?>, 'pending')" 
                                            class="px-4 py-2 text-sm rounded-md font-medium transition-colors <?php echo $request['status'] === 'pending' ? 'bg-blue-600 text-white' : 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700'; ?>" 
                                            <?php echo $request['status'] === 'pending' ? 'disabled' : ''; ?>>
                                        Pending
                                    </button>
                                    <button onclick="updateRequestStatus(<?php echo $request['id']; ?>, 'approved')" 
                                            class="px-4 py-2 text-sm rounded-md font-medium transition-colors <?php echo $request['status'] === 'approved' ? 'bg-blue-600 text-white' : 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700'; ?>" 
                                            <?php echo $request['status'] === 'approved' ? 'disabled' : ''; ?>>
                                        Approve
                                    </button>
                                    <button onclick="updateRequestStatus(<?php echo $request['id']; ?>, 'completed')" 
                                            class="px-4 py-2 text-sm rounded-md font-medium transition-colors <?php echo $request['status'] === 'completed' ? 'bg-blue-600 text-white' : 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700'; ?>" 
                                            <?php echo $request['status'] === 'completed' ? 'disabled' : ''; ?>>
                                        Complete
                                    </button>
                                    <button onclick="updateRequestStatus(<?php echo $request['id']; ?>, 'rejected')" 
                                            class="px-4 py-2 text-sm rounded-md font-medium transition-colors <?php echo $request['status'] === 'rejected' ? 'bg-red-600 text-white' : 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700'; ?>" 
                                            <?php echo $request['status'] === 'rejected' ? 'disabled' : ''; ?>>
                                        Reject
                                    </button>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <span>Request ID: #<?php echo $request['id']; ?></span>
                                    <span class="mx-2">•</span>
                                    <span>Submitted: <?php echo formatDate($request['submittedAt']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
}

function renderAdminConcernsSection($allConcerns, $searchTerm) {
    // Filter concerns based on search term
    $filteredConcerns = $allConcerns;
    if (!empty($searchTerm)) {
        $filteredConcerns = array_filter($allConcerns, function($concern) use ($searchTerm) {
            $user = getUserById($concern['userId']);
            return (
                stripos($user['fullName'] ?? '', $searchTerm) !== false ||
                stripos($user['email'] ?? '', $searchTerm) !== false ||
                stripos($concern['concernTitle'], $searchTerm) !== false ||
                stripos($concern['concernDescription'], $searchTerm) !== false ||
                stripos(getConcernTypeLabel($concern['concernType']), $searchTerm) !== false
            );
        });
    }
?>

<section class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">Community Concerns</h1>
                <p class="text-gray-600">Manage all user submitted concerns</p>
            </div>
            <div class="flex items-center space-x-2 mt-4 md:mt-0">
                <form method="GET" class="relative">
                    <input type="hidden" name="section" value="concerns">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        name="search"
                        placeholder="Search concerns..."
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        class="pl-10 w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    />
                </form>
            </div>
        </div>

        <?php if (empty($filteredConcerns)): ?>
            <div class="bg-white rounded-lg shadow-md text-center py-12">
                <div class="p-6">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold mb-2">No concerns submitted</h3>
                    <p class="text-gray-600">No community concerns have been submitted yet.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($filteredConcerns as $concern): ?>
                    <?php $user = getUserById($concern['userId']); ?>
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold mb-1">
                                        <?php echo htmlspecialchars($concern['concernTitle']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Submitted by: <?php echo $user ? htmlspecialchars($user['fullName']) : 'Unknown User'; ?> (<?php echo $user ? htmlspecialchars($user['email']) : 'N/A'; ?>)
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <span class="px-2 py-1 rounded-md text-sm <?php echo getUrgencyBadgeColor($concern['urgencyLevel']); ?>">
                                        <?php echo ucfirst($concern['urgencyLevel']); ?>
                                    </span>
                                    <span class="px-2 py-1 rounded-md text-sm <?php echo getStatusBadgeColor($concern['status']); ?>">
                                        <?php echo ucfirst($concern['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Type</p>
                                    <p class="text-sm text-gray-900"><?php echo getConcernTypeLabel($concern['concernType']); ?></p>
                                </div>
                                <?php if (!empty($concern['concernLocation'])): ?>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Location</p>
                                        <p class="text-sm text-gray-900"><?php echo htmlspecialchars($concern['concernLocation']); ?></p>
                                    </div>
                                <?php endif; ?>
                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Description</p>
                                    <p class="text-sm text-gray-900"><?php echo htmlspecialchars($concern['concernDescription']); ?></p>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center justify-between pt-4 border-t gap-4">
                                <div class="flex gap-2 flex-wrap">
                                    <button onclick="updateConcernStatus(<?php echo $concern['id']; ?>, 'submitted')" 
                                            class="px-4 py-2 text-sm rounded-md font-medium transition-colors <?php echo $concern['status'] === 'submitted' ? 'bg-blue-600 text-white' : 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700'; ?>" 
                                            <?php echo $concern['status'] === 'submitted' ? 'disabled' : ''; ?>>
                                        Submitted
                                    </button>
                                    <button onclick="updateConcernStatus(<?php echo $concern['id']; ?>, 'in-progress')" 
                                            class="px-4 py-2 text-sm rounded-md font-medium transition-colors <?php echo $concern['status'] === 'in-progress' ? 'bg-blue-600 text-white' : 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700'; ?>" 
                                            <?php echo $concern['status'] === 'in-progress' ? 'disabled' : ''; ?>>
                                        In Progress
                                    </button>
                                    <button onclick="updateConcernStatus(<?php echo $concern['id']; ?>, 'completed')" 
                                            class="px-4 py-2 text-sm rounded-md font-medium transition-colors <?php echo $concern['status'] === 'completed' ? 'bg-blue-600 text-white' : 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700'; ?>" 
                                            <?php echo $concern['status'] === 'completed' ? 'disabled' : ''; ?>>
                                        Complete
                                    </button>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <span>Concern ID: #<?php echo $concern['id']; ?></span>
                                    <span class="mx-2">•</span>
                                    <span>Submitted: <?php echo formatDate($concern['submittedAt']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
}

function renderAdminUsersSection($allUsers, $searchTerm) {
    // Filter users based on search term
    $filteredUsers = $allUsers;
    if (!empty($searchTerm)) {
        $filteredUsers = array_filter($allUsers, function($user) use ($searchTerm) {
            return (
                stripos($user['fullName'], $searchTerm) !== false ||
                stripos($user['email'], $searchTerm) !== false ||
                stripos($user['role'], $searchTerm) !== false
            );
        });
    }
?>

<section class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">User Management</h1>
                <p class="text-gray-600">Manage all registered users</p>
            </div>
            <div class="flex items-center space-x-2 mt-4 md:mt-0">
                <form method="GET" class="relative">
                    <input type="hidden" name="section" value="users">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        name="search"
                        placeholder="Search users..."
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        class="pl-10 w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    />
                </form>
            </div>
        </div>

        <?php if (empty($filteredUsers)): ?>
            <div class="bg-white rounded-lg shadow-md text-center py-12">
                <div class="p-6">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold mb-2">No users found</h3>
                    <p class="text-gray-600">No users match your search criteria.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($filteredUsers as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="font-medium text-blue-600">
                                                    <?php echo strtoupper(substr($user['fullName'], 0, 1)); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($user['fullName']); ?></div>
                                            <div class="text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-md text-sm <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    <?php echo formatDate($user['created_at']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-md text-sm bg-green-100 text-green-800">
                                        Active
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
}

// Helper functions for AdminDashboard
function getAllRequests() {
    $file = __DIR__ . '/../data/requests.json';
    if (file_exists($file)) {
        $requests = json_decode(file_get_contents($file), true) ?: [];
        // Sort by submission date (newest first)
        usort($requests, function($a, $b) {
            return strtotime($b['submittedAt']) - strtotime($a['submittedAt']);
        });
        return $requests;
    }
    return [];
}

function getAllConcerns() {
    $file = __DIR__ . '/../data/concerns.json';
    if (file_exists($file)) {
        $concerns = json_decode(file_get_contents($file), true) ?: [];
        // Sort by submission date (newest first)
        usort($concerns, function($a, $b) {
            return strtotime($b['submittedAt']) - strtotime($a['submittedAt']);
        });
        return $concerns;
    }
    return [];
}

function getAllUsers() {
    include_once 'AuthModal.php';
    $users = getUserData();
    $userList = [];
    
    foreach ($users as $key => $userData) {
        $userList[] = $userData;
    }
    
    // Sort by creation date (newest first)
    usort($userList, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    return $userList;
}

function getUserById($userId) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['id'] === $userId) {
            return $user;
        }
    }
    return null;
}

function updateRequestStatus($requestId, $newStatus) {
    $dir = __DIR__ . '/../data';
    $file = $dir . '/requests.json';
    
    if (file_exists($file)) {
        $requests = json_decode(file_get_contents($file), true) ?: [];
        
        foreach ($requests as &$request) {
            if ($request['id'] === $requestId) {
                $request['status'] = $newStatus;
                break;
            }
        }
        
        return file_put_contents($file, json_encode($requests, JSON_PRETTY_PRINT)) !== false;
    }
    
    return false;
}

function updateConcernStatus($concernId, $newStatus) {
    $dir = __DIR__ . '/../data';
    $file = $dir . '/concerns.json';
    
    if (file_exists($file)) {
        $concerns = json_decode(file_get_contents($file), true) ?: [];
        
        foreach ($concerns as &$concern) {
            if ($concern['id'] === $concernId) {
                $concern['status'] = $newStatus;
                break;
            }
        }
        
        return file_put_contents($file, json_encode($concerns, JSON_PRETTY_PRINT)) !== false;
    }
    
    return false;
}

function formatDate($dateString) {
    return date('M j, Y g:i A', strtotime($dateString));
}

function getStatusBadgeColor($status) {
    switch ($status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'approved': return 'bg-green-100 text-green-800';
        case 'completed': return 'bg-blue-100 text-blue-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        case 'submitted': return 'bg-blue-100 text-blue-800';
        case 'in-progress': return 'bg-orange-100 text-orange-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getUrgencyBadgeColor($urgency) {
    switch ($urgency) {
        case 'low': return 'bg-gray-100 text-gray-800';
        case 'medium': return 'bg-yellow-100 text-yellow-800';
        case 'high': return 'bg-orange-100 text-orange-800';
        case 'emergency': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getDocumentTypeLabel($type) {
    $labels = [
        'barangay-clearance' => 'Barangay Clearance',
        'certificate-residency' => 'Certificate of Residency',
        'certificate-indigency' => 'Certificate of Indigency',
        'business-permit' => 'Business Permit',
        'id-replacement' => 'ID Replacement'
    ];
    return $labels[$type] ?? $type;
}

function getConcernTypeLabel($type) {
    $labels = [
        'infrastructure' => 'Infrastructure',
        'public-safety' => 'Public Safety',
        'sanitation' => 'Sanitation',
        'noise-complaint' => 'Noise Complaint',
        'community-service' => 'Community Service',
        'other' => 'Other'
    ];
    return $labels[$type] ?? $type;
}
?>