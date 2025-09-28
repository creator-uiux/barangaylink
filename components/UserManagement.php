<?php
function renderUserManagement() {
    $searchTerm = $_GET['search'] ?? '';
    $roleFilter = $_GET['role'] ?? '';
    $statusFilter = $_GET['status'] ?? '';
    
    // Load users
    $allUsers = getAllUsers();
    
    // Apply filters
    $filteredUsers = $allUsers;
    
    if (!empty($searchTerm)) {
        $filteredUsers = array_filter($filteredUsers, function($user) use ($searchTerm) {
            return stripos($user['fullName'], $searchTerm) !== false ||
                   stripos($user['email'], $searchTerm) !== false;
        });
    }
    
    if (!empty($roleFilter)) {
        $filteredUsers = array_filter($filteredUsers, function($user) use ($roleFilter) {
            return $user['role'] === $roleFilter;
        });
    }
    
    if (!empty($statusFilter)) {
        $filteredUsers = array_filter($filteredUsers, function($user) use ($statusFilter) {
            $status = $user['status'] ?? 'active';
            return $status === $statusFilter;
        });
    }
    
    // Handle user actions
    $message = '';
    $error = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['toggle_user_status'])) {
            $userId = $_POST['user_id'];
            $currentStatus = $_POST['current_status'];
            $newStatus = $currentStatus === 'active' ? 'inactive' : 'active';
            
            if (updateUserStatus($userId, $newStatus)) {
                $message = 'User ' . ($newStatus === 'active' ? 'activated' : 'deactivated') . ' successfully';
                echo '<script>showToast("' . addslashes($message) . '", "success"); setTimeout(() => window.location.reload(), 1000);</script>';
            } else {
                $error = 'Failed to update user status';
            }
        }
        
        if (isset($_POST['delete_user'])) {
            $userId = $_POST['user_id'];
            $userName = $_POST['user_name'];
            
            if (deleteUserById($userId)) {
                $message = 'User deleted successfully';
                echo '<script>showToast("' . addslashes($message) . '", "success"); setTimeout(() => window.location.reload(), 1000);</script>';
            } else {
                $error = 'Failed to delete user';
            }
        }
    }
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold">User Management</h1>
            <p class="text-gray-600">Manage all registered barangay users</p>
        </div>
        <div class="flex items-center space-x-3 mt-4 md:mt-0">
            <button onclick="showAddUserModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add User
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            <form method="GET" class="grid md:grid-cols-4 gap-4">
                <input type="hidden" name="section" value="users">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        name="search"
                        placeholder="Search by name or email..."
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        class="pl-10 w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    />
                </div>
                <select
                    name="role"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                >
                    <option value="">All Roles</option>
                    <option value="user" <?php echo $roleFilter === 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
                <select
                    name="status"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                >
                    <option value="">All Status</option>
                    <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                        Filter
                    </button>
                    <a href="App.php?section=users" class="flex-1 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-md font-medium transition-colors text-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($filteredUsers)): ?>
                        <tr>
                            <td colspan="6" class="h-24 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <div class="text-gray-600">No users found</div>
                                    <div class="text-sm text-gray-500">No users match your current filters.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($filteredUsers as $user): ?>
                            <?php 
                            $userRequests = getUserRequestsCount($user['id']);
                            $userConcerns = getUserConcernsCount($user['id']);
                            $status = $user['status'] ?? 'active';
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="font-medium text-blue-600">
                                                <?php echo strtoupper(substr($user['fullName'], 0, 1)); ?>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($user['fullName']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-md text-sm <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-md text-sm <?php echo getStatusBadgeColor($status); ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-4 text-sm">
                                        <span class="text-blue-600"><?php echo $userRequests; ?> req</span>
                                        <span class="text-orange-600"><?php echo $userConcerns; ?> con</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-1">
                                        <button
                                            onclick="viewUser('<?php echo $user['id']; ?>')"
                                            class="h-8 w-8 p-0 hover:bg-gray-100 rounded-md flex items-center justify-center"
                                            title="View User"
                                        >
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="toggle_user_status" value="1">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $status; ?>">
                                            <button
                                                type="submit"
                                                class="h-8 w-8 p-0 hover:bg-gray-100 rounded-md flex items-center justify-center"
                                                title="<?php echo $status === 'active' ? 'Deactivate' : 'Activate'; ?> User"
                                                onclick="return confirm('Are you sure you want to <?php echo $status === 'active' ? 'deactivate' : 'activate'; ?> this user?')"
                                            >
                                                <?php if ($status === 'active'): ?>
                                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                <?php else: ?>
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-6 0h1m6 0h1M4 7h16M4 7v10a2 2 0 002 2h12a2 2 0 002-2V7M4 7l2-2h12l2 2"></path>
                                                    </svg>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="delete_user" value="1">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($user['fullName']); ?>">
                                            <button
                                                type="submit"
                                                class="h-8 w-8 p-0 hover:bg-gray-100 rounded-md flex items-center justify-center"
                                                title="Delete User"
                                                onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($user['fullName']); ?>? This action cannot be undone.')"
                                            >
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<?php include 'AddUserModal.php'; renderAddUserModal(); ?>

<script>
function viewUser(userId) {
    showToast('User details feature coming soon!', 'success');
}
</script>

<?php
}

function getUserRequestsCount($userId) {
    $file = __DIR__ . '/../data/requests.json';
    if (file_exists($file)) {
        $requests = json_decode(file_get_contents($file), true) ?: [];
        return count(array_filter($requests, function($req) use ($userId) {
            return $req['userId'] === $userId;
        }));
    }
    return 0;
}

function getUserConcernsCount($userId) {
    $file = __DIR__ . '/../data/concerns.json';
    if (file_exists($file)) {
        $concerns = json_decode(file_get_contents($file), true) ?: [];
        return count(array_filter($concerns, function($concern) use ($userId) {
            return $concern['userId'] === $userId;
        }));
    }
    return 0;
}

function updateUserStatus($userId, $newStatus) {
    include_once 'AuthModal.php';
    $users = getUserData();
    
    foreach ($users as $key => &$user) {
        if ($user['id'] === $userId) {
            $user['status'] = $newStatus;
            saveUserData($users);
            return true;
        }
    }
    
    return false;
}

function deleteUserById($userId) {
    include_once 'AuthModal.php';
    $users = getUserData();
    
    foreach ($users as $key => $user) {
        if ($user['id'] === $userId) {
            unset($users[$key]);
            saveUserData($users);
            return true;
        }
    }
    
    return false;
}

function getStatusBadgeColor($status) {
    switch ($status) {
        case 'active': return 'bg-green-100 text-green-800';
        case 'inactive': return 'bg-gray-100 text-gray-800';
        case 'suspended': return 'bg-red-100 text-red-800';
        default: return 'bg-green-100 text-green-800';
    }
}
?>