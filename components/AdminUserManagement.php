<?php
/**
 * Admin User Management Component - EXACT MATCH to AdminUserManagement.tsx
 * Manage registered users and residents
 */

function AdminUserManagement() {
    // Load users from database or JSON based on configuration
    if (USE_DATABASE) {
        require_once __DIR__ . '/../functions/db_utils.php';
        $users = getAllUsersWithDeleted(); // Include deleted users for admin management
        
        // Convert database format to expected format for compatibility
        foreach ($users as &$user) {
            $user['name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['middle_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
            $user['createdAt'] = $user['created_at'];
        }
    } else {
        // Fallback to JSON files - include deleted users for admin management
        $users = json_decode(file_get_contents(__DIR__ . '/../data/users.json'), true) ?: [];
    }
    
    // Form processing is now handled in index.php to prevent header errors
    
    // Apply search filter
    $searchTerm = $_GET['search'] ?? '';
    $selectedEmail = $_GET['selected'] ?? '';
    
    $filteredUsers = array_filter($users, function($user) use ($searchTerm) {
        if (empty($searchTerm)) return true;
        return stripos(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') ?: $user['name'] ?? '', $searchTerm) !== false ||
               stripos($user['email'], $searchTerm) !== false ||
               stripos($user['address'] ?? '', $searchTerm) !== false;
    });
    
    // Get selected user
    $selectedUser = null;
    if ($selectedEmail) {
        foreach ($users as $u) {
            if ($u['email'] === $selectedEmail) {
                $selectedUser = $u;
                break;
            }
        }
    }
    
    // Calculate stats
    $currentMonth = date('n');
    $currentYear = date('Y');
    
    $stats = [
        'total' => count($users),
        'active' => count(array_filter($users, fn($u) => $u['role'] === 'user')),
        'admins' => count(array_filter($users, fn($u) => $u['role'] === 'admin')),
        'thisMonth' => count(array_filter($users, function($u) use ($currentMonth, $currentYear) {
            if (empty($u['createdAt'])) return false;
            $createdDate = strtotime($u['createdAt']);
            return date('n', $createdDate) == $currentMonth && date('Y', $createdDate) == $currentYear;
        }))
    ];
    
    ob_start();
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-blue-900 mb-2">Resident Registry</h2>
            <p class="text-blue-600">Manage registered users and residents</p>
        </div>
        <button
            onclick="showAddUserModal()"
            class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>Add New User</span>
        </button>
    </div>

    <!-- Stats -->
    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-blue-50 text-blue-700 rounded-lg p-4">
            <p class="text-sm mb-1">Total Users</p>
            <p class="text-2xl"><?php echo $stats['total']; ?></p>
        </div>
        <div class="bg-green-50 text-green-700 rounded-lg p-4">
            <p class="text-sm mb-1">Active Residents</p>
            <p class="text-2xl"><?php echo $stats['active']; ?></p>
        </div>
        <div class="bg-purple-50 text-purple-700 rounded-lg p-4">
            <p class="text-sm mb-1">Administrators</p>
            <p class="text-2xl"><?php echo $stats['admins']; ?></p>
        </div>
        <div class="bg-orange-50 text-orange-700 rounded-lg p-4">
            <p class="text-sm mb-1">Registered This Month</p>
            <p class="text-2xl"><?php echo $stats['thisMonth']; ?></p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg p-4 border border-blue-100">
        <form method="GET">
            <input type="hidden" name="view" value="manage-users">
            <?php if ($selectedEmail): ?>
            <input type="hidden" name="selected" value="<?php echo htmlspecialchars($selectedEmail); ?>">
            <?php endif; ?>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    name="search"
                    placeholder="Search by name, email, or address..."
                    value="<?php echo htmlspecialchars($searchTerm); ?>"
                    class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
        </form>
    </div>

    <!-- Users Grid -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Users List -->
        <div class="space-y-3">
            <h3 class="text-blue-900">Registered Users (<?php echo count($filteredUsers); ?>)</h3>
            <div class="space-y-3 max-h-[600px] overflow-y-auto">
                <?php if (count($filteredUsers) > 0): ?>
                    <?php foreach ($filteredUsers as $user): ?>
                    <a href="?view=manage-users&selected=<?php echo urlencode($user['email']); ?>&search=<?php echo urlencode($searchTerm); ?>"
                       class="block bg-white rounded-lg p-4 border cursor-pointer transition-all <?php echo $selectedUser && $selectedUser['email'] === $user['email'] ? 'border-blue-500 shadow-lg' : 'border-blue-100 hover:border-blue-300'; ?>">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="text-blue-900"><?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') ?: $user['name'] ?? 'User'); ?></h4>
                                <p class="text-sm text-blue-600"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 rounded-full text-xs <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'; ?>">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                                <?php 
                                $status = $user['status'] ?? 'active';
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-700',
                                    'inactive' => 'bg-yellow-100 text-yellow-700',
                                    'deleted' => 'bg-red-100 text-red-700',
                                    'suspended' => 'bg-orange-100 text-orange-700'
                                ];
                                $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs <?php echo $statusColor; ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </div>
                        </div>
                        <?php if (!empty($user['address'])): ?>
                        <p class="text-sm text-blue-600">📍 <?php echo htmlspecialchars($user['address']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($user['phone'])): ?>
                        <p class="text-sm text-blue-600">📞 <?php echo htmlspecialchars($user['phone']); ?></p>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-lg p-12 text-center border border-blue-100">
                        <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-blue-600">No users found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- User Details -->
        <div class="lg:sticky lg:top-6">
            <?php if ($selectedUser): ?>
            <div class="bg-white rounded-lg p-6 border border-blue-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-blue-900">User Details</h3>
                    <div class="flex items-center space-x-2">
                        <?php if (($selectedUser['status'] ?? 'active') !== 'deleted'): ?>
                        <button
                            onclick='showEditUserModal(<?php echo json_encode($selectedUser); ?>)'
                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                            title="Edit User"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <?php endif; ?>
                        
                        <?php if (($selectedUser['status'] ?? 'active') === 'deleted'): ?>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to restore this user?');">
                            <input type="hidden" name="restore_user" value="1">
                            <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($selectedUser['email']); ?>">
                            <button
                                type="submit"
                                class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                                title="Restore User"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </form>
                        <?php else: ?>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="delete_user" value="1">
                            <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($selectedUser['email']); ?>">
                            <button
                                type="submit"
                                class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                title="Delete User"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Full Name</span>
                        </label>
                        <p class="text-blue-900"><?php echo htmlspecialchars(($selectedUser['first_name'] ?? '') . ' ' . ($selectedUser['last_name'] ?? '') ?: $selectedUser['name'] ?? 'User'); ?></p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Email Address</span>
                        </label>
                        <p class="text-blue-900"><?php echo htmlspecialchars($selectedUser['email']); ?></p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>Phone Number</span>
                        </label>
                        <p class="text-blue-900"><?php echo htmlspecialchars($selectedUser['phone'] ?? 'Not provided'); ?></p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Address</span>
                        </label>
                        <p class="text-blue-900"><?php echo htmlspecialchars($selectedUser['address'] ?? 'Not provided'); ?></p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Registered Date</span>
                        </label>
                        <p class="text-blue-900">
                            <?php echo !empty($selectedUser['createdAt']) ? date('M d, Y', strtotime($selectedUser['createdAt'])) : 'Unknown'; ?>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600 mb-1">Account Role</label>
                        <p class="text-blue-900 capitalize"><?php echo htmlspecialchars($selectedUser['role']); ?></p>
                    </div>

                    <div>
                        <label class="text-sm text-blue-600 mb-1">Account Status</label>
                        <?php 
                        $status = $selectedUser['status'] ?? 'active';
                        $statusColors = [
                            'active' => 'text-green-700 bg-green-100',
                            'inactive' => 'text-yellow-700 bg-yellow-100',
                            'deleted' => 'text-red-700 bg-red-100',
                            'suspended' => 'text-orange-700 bg-orange-100'
                        ];
                        $statusColor = $statusColors[$status] ?? 'text-gray-700 bg-gray-100';
                        ?>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?php echo $statusColor; ?>">
                            <?php echo htmlspecialchars(ucfirst($status)); ?>
                        </span>
                        <?php if ($status === 'deleted' && !empty($selectedUser['deletedAt'])): ?>
                        <p class="text-xs text-gray-500 mt-1">Deleted on: <?php echo date('M d, Y H:i', strtotime($selectedUser['deletedAt'])); ?></p>
                        <?php endif; ?>
                        <?php if ($status === 'active' && !empty($selectedUser['restoredAt'])): ?>
                        <p class="text-xs text-green-600 mt-1">Restored on: <?php echo date('M d, Y H:i', strtotime($selectedUser['restoredAt'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-lg p-12 text-center border border-blue-100">
                <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <p class="text-blue-600">Select a user to view details</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- User Modal (Add/Edit) -->
<div id="userModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-blue-900" id="modalTitle">Add New User</h2>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" class="space-y-4" id="userForm">
            <input type="hidden" name="save_user" value="1">
            <input type="hidden" name="original_email" id="originalEmail" value="">
            
            <div>
                <label class="block text-gray-700 mb-2">Full Name *</label>
                <input
                    type="text"
                    name="name"
                    id="userName"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Email *</label>
                <input
                    type="email"
                    name="email"
                    id="userEmail"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
                <p class="text-xs text-gray-500 mt-1 hidden" id="emailNotice">Email cannot be changed</p>
            </div>

            <div id="passwordField">
                <label class="block text-gray-700 mb-2">Password *</label>
                <input
                    type="password"
                    name="password"
                    id="userPassword"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Role</label>
                <select
                    name="role"
                    id="userRole"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Phone Number</label>
                <input
                    type="tel"
                    name="phone"
                    id="userPhone"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Address</label>
                <input
                    type="text"
                    name="address"
                    id="userAddress"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div class="flex space-x-3">
                <button
                    type="button"
                    onclick="closeUserModal()"
                    class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    id="submitButton"
                >
                    Add User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddUserModal() {
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('submitButton').textContent = 'Add User';
    document.getElementById('userForm').reset();
    document.getElementById('originalEmail').value = '';
    document.getElementById('userEmail').disabled = false;
    document.getElementById('passwordField').classList.remove('hidden');
    document.getElementById('userPassword').required = true;
    document.getElementById('emailNotice').classList.add('hidden');
    document.getElementById('userModal').classList.remove('hidden');
}

function showEditUserModal(user) {
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('submitButton').textContent = 'Save Changes';
    document.getElementById('userName').value = user.name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userEmail').disabled = true;
    document.getElementById('originalEmail').value = user.email;
    document.getElementById('userRole').value = user.role;
    document.getElementById('userPhone').value = user.phone || '';
    document.getElementById('userAddress').value = user.address || '';
    document.getElementById('passwordField').classList.add('hidden');
    document.getElementById('userPassword').required = false;
    document.getElementById('emailNotice').classList.remove('hidden');
    document.getElementById('userModal').classList.remove('hidden');
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.getElementById('userForm').reset();
}
</script>
<?php
    return ob_get_clean();
}
?>