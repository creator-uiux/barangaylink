<?php
/**
 * User Management - SYNCHRONIZED with components/AdminUserManagement.tsx
 */

$conn = getDBConnection();
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Calculate stats
$thisMonth = date('Y-m');
$stats = [
    'total' => count($users),
    'active' => count(array_filter($users, fn($u) => $u['role'] === 'resident' || $u['role'] === 'user')),
    'admins' => count(array_filter($users, fn($u) => $u['role'] === 'admin')),
    'thisMonth' => count(array_filter($users, fn($u) => strpos($u['created_at'], $thisMonth) === 0))
];
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
            <!-- UserPlus Icon -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
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
        <div class="relative">
            <!-- Search Icon -->
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                type="text"
                id="searchInput"
                placeholder="Search by name, email, or address..."
                class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                onkeyup="filterUsers()"
            />
        </div>
    </div>

    <!-- Users Grid -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Users List -->
        <div class="space-y-3">
            <h3 class="text-blue-900">Registered Users (<?php echo count($users); ?>)</h3>
            <div class="space-y-3 max-h-[600px] overflow-y-auto">
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): 
                        $fullName = !empty($user['first_name']) && !empty($user['last_name']) 
                            ? trim($user['first_name'] . ' ' . ($user['middle_name'] ?? '') . ' ' . $user['last_name'])
                            : ($user['name'] ?? 'Unknown User');
                        $roleColor = $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700';
                    ?>
                        <div
                            class="user-card bg-white rounded-lg p-4 border border-blue-100 hover:border-blue-300 cursor-pointer transition-all"
                            data-user-email="<?php echo htmlspecialchars($user['email']); ?>"
                            data-search="<?php echo strtolower($fullName . ' ' . $user['email'] . ' ' . ($user['address'] ?? '')); ?>"
                            onclick="selectUser(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                        >
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="text-blue-900"><?php echo htmlspecialchars($fullName); ?></h4>
                                    <p class="text-sm text-blue-600"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                <span class="px-2 py-1 rounded-full text-xs <?php echo $roleColor; ?>">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </div>
                            <?php if (!empty($user['address'])): ?>
                                <p class="text-sm text-blue-600">📍 <?php echo htmlspecialchars($user['address']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($user['phone'])): ?>
                                <p class="text-sm text-blue-600">📞 <?php echo htmlspecialchars($user['phone']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-lg p-12 text-center border border-blue-100">
                        <!-- Users Icon -->
                        <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-blue-600">No users found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- User Details -->
        <div class="lg:sticky lg:top-6">
            <div id="userDetails" class="bg-white rounded-lg p-12 text-center border border-blue-100">
                <!-- Users Icon -->
                <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <p class="text-blue-600">Select a user to view details</p>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-blue-900" id="modalTitle">Add New User</h2>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="userForm" class="space-y-4">
            <input type="hidden" id="editingEmail" value="">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-2">First Name *</label>
                    <input
                        type="text"
                        id="firstName"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Juan"
                        required
                    />
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Last Name *</label>
                    <input
                        type="text"
                        id="lastName"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Dela Cruz"
                        required
                    />
                </div>
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Middle Name (Optional)</label>
                <input
                    type="text"
                    id="middleName"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Santos"
                />
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Email *</label>
                <input
                    type="email"
                    id="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
                <p class="text-xs text-gray-500 mt-1" id="emailNote"></p>
            </div>

            <div id="passwordField">
                <label class="block text-gray-700 mb-2">Password *</label>
                <input
                    type="password"
                    id="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Role</label>
                <select
                    id="role"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >
                    <option value="user">User</option>
                    <option value="resident">Resident</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Phone Number</label>
                <input
                    type="tel"
                    id="phone"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Address</label>
                <input
                    type="text"
                    id="address"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg"></div>

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
                >
                    <span id="submitButtonText">Add User</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let selectedUserEmail = null;

function filterUsers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.user-card');
    
    cards.forEach(card => {
        const searchData = card.getAttribute('data-search');
        if (searchData.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function selectUser(user) {
    selectedUserEmail = user.email;
    
    // Highlight selected card
    document.querySelectorAll('.user-card').forEach(card => {
        if (card.getAttribute('data-user-email') === user.email) {
            card.classList.add('border-blue-500', 'shadow-lg');
            card.classList.remove('border-blue-100');
        } else {
            card.classList.remove('border-blue-500', 'shadow-lg');
            card.classList.add('border-blue-100');
        }
    });
    
    const fullName = user.first_name && user.last_name 
        ? (user.first_name + ' ' + (user.middle_name || '') + ' ' + user.last_name).trim()
        : (user.name || 'Unknown User');
    
    const detailsHTML = `
        <div class="bg-white rounded-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-blue-900">User Details</h3>
                <div class="flex items-center space-x-2">
                    <button
                        onclick='editUser(${JSON.stringify(user)})'
                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                        title="Edit User"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button
                        onclick="deleteUser('${user.email}')"
                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                        title="Delete User"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Full Name</span>
                    </label>
                    <p class="text-blue-900">${fullName}</p>
                </div>

                <div>
                    <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>Email Address</span>
                    </label>
                    <p class="text-blue-900">${user.email}</p>
                </div>

                <div>
                    <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>Phone Number</span>
                    </label>
                    <p class="text-blue-900">${user.phone || 'Not provided'}</p>
                </div>

                <div>
                    <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Address</span>
                    </label>
                    <p class="text-blue-900">${user.address || 'Not provided'}</p>
                </div>

                <div>
                    <label class="text-sm text-blue-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Registered Date</span>
                    </label>
                    <p class="text-blue-900">${user.created_at ? new Date(user.created_at).toLocaleDateString() : 'Unknown'}</p>
                </div>

                <div>
                    <label class="text-sm text-blue-600 mb-1">Account Role</label>
                    <p class="text-blue-900 capitalize">${user.role}</p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('userDetails').innerHTML = detailsHTML;
}

function showAddUserModal() {
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('submitButtonText').textContent = 'Add User';
    document.getElementById('editingEmail').value = '';
    document.getElementById('userForm').reset();
    document.getElementById('email').disabled = false;
    document.getElementById('passwordField').style.display = 'block';
    document.getElementById('password').required = true;
    document.getElementById('emailNote').textContent = '';
    document.getElementById('errorMessage').classList.add('hidden');
    document.getElementById('userModal').classList.remove('hidden');
}

function editUser(user) {
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('submitButtonText').textContent = 'Save Changes';
    document.getElementById('editingEmail').value = user.email;
    document.getElementById('firstName').value = user.first_name || '';
    document.getElementById('middleName').value = user.middle_name || '';
    document.getElementById('lastName').value = user.last_name || '';
    document.getElementById('email').value = user.email;
    document.getElementById('email').disabled = true;
    document.getElementById('role').value = user.role;
    document.getElementById('phone').value = user.phone || '';
    document.getElementById('address').value = user.address || '';
    document.getElementById('passwordField').style.display = 'none';
    document.getElementById('password').required = false;
    document.getElementById('emailNote').textContent = 'Email cannot be changed';
    document.getElementById('errorMessage').classList.add('hidden');
    document.getElementById('userModal').classList.remove('hidden');
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.getElementById('userForm').reset();
}

function deleteUser(email) {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }
    
    fetch('/api/users.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'delete',
            email: email
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'User deleted', 'The user has been removed successfully.');
            // Update UI without reload - SYNCHRONIZED with TSX
            const userCard = document.querySelector(`[data-user-email="${email}"]`);
            if (userCard) {
                userCard.style.transition = 'opacity 0.3s';
                userCard.style.opacity = '0';
                setTimeout(() => {
                    userCard.remove();
                    updateUserStats();
                    
                    // If this was the selected user, clear the details
                    if (selectedUserEmail === email) {
                        document.getElementById('userDetails').innerHTML = `
                            <div class="bg-white rounded-lg p-12 text-center border border-blue-100">
                                <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-blue-600">Select a user to view details</p>
                            </div>
                        `;
                        selectedUserEmail = null;
                    }
                }, 300);
            }
        } else {
            showToast('error', 'Failed to delete user', data.error || 'Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to delete user', 'An error occurred. Please try again.');
    });
}

document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const editingEmail = document.getElementById('editingEmail').value;
    const firstName = document.getElementById('firstName').value.trim();
    const middleName = document.getElementById('middleName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const role = document.getElementById('role').value;
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();
    
    // Validation
    if (!firstName || !lastName || !email) {
        showError('First name, last name, and email are required');
        return;
    }
    
    const nameRegex = /^[a-zA-Z\s\-']+$/;
    if (!nameRegex.test(firstName)) {
        showError('First name should only contain letters');
        return;
    }
    if (middleName && !nameRegex.test(middleName)) {
        showError('Middle name should only contain letters');
        return;
    }
    if (!nameRegex.test(lastName)) {
        showError('Last name should only contain letters');
        return;
    }
    
    if (!editingEmail && !password) {
        showError('Password is required for new users');
        return;
    }
    
    if (!editingEmail && password.length < 8) {
        showError('Password must be at least 8 characters long');
        return;
    }
    
    // Submit form
    const formData = new URLSearchParams({
        action: editingEmail ? 'update' : 'create',
        first_name: firstName,
        middle_name: middleName,
        last_name: lastName,
        email: email,
        role: role,
        phone: phone,
        address: address
    });
    
    if (!editingEmail) {
        formData.append('password', password);
    }
    
    if (editingEmail) {
        formData.append('original_email', editingEmail);
    }
    
    fetch('/api/users.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', editingEmail ? 'User updated' : 'User created', 'The user has been saved successfully.');
            closeUserModal();
            
            // Update UI without reload - SYNCHRONIZED with TSX
            if (editingEmail) {
                // Update existing user card
                const userCard = document.querySelector(`[data-user-email="${editingEmail}"]`);
                if (userCard) {
                    const fullName = (firstName + ' ' + (middleName || '') + ' ' + lastName).trim();
                    const roleColor = role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700';
                    
                    userCard.innerHTML = `
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="text-blue-900">${fullName}</h4>
                                <p class="text-sm text-blue-600">${email}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs ${roleColor}">
                                ${role}
                            </span>
                        </div>
                        ${address ? `<p class="text-sm text-blue-600">📍 ${address}</p>` : ''}
                        ${phone ? `<p class="text-sm text-blue-600">📞 ${phone}</p>` : ''}
                    `;
                    
                    // Update search data attribute
                    userCard.setAttribute('data-search', (fullName + ' ' + email + ' ' + (address || '')).toLowerCase());
                    
                    // If this user is selected, update the details view
                    if (selectedUserEmail === editingEmail) {
                        selectUser({
                            first_name: firstName,
                            middle_name: middleName,
                            last_name: lastName,
                            email: email,
                            role: role,
                            phone: phone,
                            address: address,
                            created_at: new Date().toISOString()
                        });
                    }
                }
            } else {
                // Reload for new users to get them from database with proper IDs
                setTimeout(() => location.reload(), 1000);
            }
        } else {
            showError(data.error || 'Failed to save user. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred. Please try again.');
    });
});

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
}

// Toast notification function
function showToast(type, title, message) {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50 transform transition-all duration-300 ease-in-out ${type === 'success' ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'}`;
    
    toast.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${type === 'success' 
                        ? '<svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                        : '<svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                    }
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="font-medium text-gray-900">${title}</p>
                    <p class="mt-1 text-sm text-gray-500">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="this.closest('.fixed').remove()" class="inline-flex text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

function updateUserStats() {
    const totalUsers = document.querySelectorAll('.user-card').length;
    const activeResidents = document.querySelectorAll('.user-card[data-search*="resident"]').length;
    const admins = document.querySelectorAll('.user-card[data-search*="admin"]').length;
    const thisMonth = document.querySelectorAll('.user-card[data-search*="<?php echo $thisMonth; ?>"]').length;
    
    document.querySelector('.bg-blue-50 .text-2xl').textContent = totalUsers;
    document.querySelector('.bg-green-50 .text-2xl').textContent = activeResidents;
    document.querySelector('.bg-purple-50 .text-2xl').textContent = admins;
    document.querySelector('.bg-orange-50 .text-2xl').textContent = thisMonth;
}
</script>