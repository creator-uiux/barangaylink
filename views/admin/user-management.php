<?php
/**
 * User Management - SYNCHRONIZED with components/AdminUserManagement.tsx
 */

$db = getDB();
$users = fetchAll("SELECT * FROM users ORDER BY created_at DESC");

// Calculate stats
$thisMonth = date('Y-m');
    $stats = [
        'total' => count($users),
        'active' => count(array_filter($users, fn($u) => $u['role'] === 'resident')),
        'admins' => count(array_filter($users, fn($u) => $u['role'] === 'admin')),
        'thisMonth' => count(array_filter($users, fn($u) => strpos($u['created_at'], $thisMonth) === 0))
    ];
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                    <h2 class="text-gray-900 text-xl font-semibold">Resident Registry</h2>
                    <p class="text-gray-600">Manage registered users and residents</p>
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
    </div>

    <!-- Stats -->
        <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-600">Total Users</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['total']; ?></p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-600">Active Residents</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['active']; ?></p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-600">Administrators</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['admins']; ?></p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-600">Registered This Month</h4>
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['thisMonth']; ?></p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
        <div class="relative">
            <!-- Search Icon -->
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                type="text"
                id="searchInput"
                placeholder="Search by name, email, or address..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900 placeholder-gray-400"
                onkeyup="filterUsers()"
            />
        </div>
    </div>

    <!-- Users Grid -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Users List -->
        <div class="space-y-3">
            <h3 class="text-slate-800">Registered Users (<?php echo count($users); ?>)</h3>
            <div class="space-y-3 max-h-[600px] overflow-y-auto">
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user):
                        $fullName = !empty($user['first_name']) && !empty($user['last_name'])
                            ? trim($user['first_name'] . ' ' . ($user['middle_name'] ?? '') . ' ' . $user['last_name'])
                            : ($user['name'] ?? 'Unknown User');
                        $roleColor = $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700';
                    ?>
                        <div
                            class="user-card bg-white rounded-lg p-6 border border-gray-200 hover:border-gray-300 cursor-pointer transition-all duration-200"
                            data-user-email="<?php echo htmlspecialchars($user['email']); ?>"
                            data-search="<?php echo strtolower($fullName . ' ' . $user['email'] . ' ' . ($user['address'] ?? '')); ?>"
                            onclick="selectUser(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="text-gray-900 font-semibold"><?php echo htmlspecialchars($fullName); ?></h4>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $roleColor; ?>">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </div>
                            <?php if (!empty($user['address'])): ?>
                                <p class="text-sm text-slate-600 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($user['address']); ?></span>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($user['phone'])): ?>
                                <p class="text-sm text-slate-600 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($user['phone']); ?></span>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="professional-glass rounded-lg p-12 text-center border border-blue-200/30 shadow-sm">
                        <!-- Users Icon -->
                        <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-slate-600">No users found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- User Details -->
        <div class="lg:sticky lg:top-6">
            <div id="userDetails" class="bg-white rounded-2xl shadow-xl border border-slate-200/50 overflow-hidden">
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 via-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <p class="text-slate-600 font-medium">Select a user to view details</p>
                    <p class="text-slate-400 text-sm mt-1">Click on any user card to see their information</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="hidden fixed inset-0 bg-gradient-to-br from-slate-900/80 via-slate-800/70 to-slate-900/80 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4 animate-fade-in">
    <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full max-w-md sm:max-w-2xl border border-slate-200/50 transform transition-all duration-300 ease-out animate-slide-up max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 rounded-t-2xl px-6 py-5 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-purple-500/20"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-white" id="modalTitle">Add New User</h2>
                </div>
                <button onclick="closeUserModal()" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-all duration-200 backdrop-blur-sm group">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-6">

        <form id="userForm" class="space-y-5">
            <input type="hidden" id="editingEmail" value="">

            <!-- Name Fields - Landscape Layout -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>First Name *</span>
                    </label>
                    <input
                        type="text"
                        id="firstName"
                        class="w-full pl-4 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-slate-900 placeholder-slate-400 transition-all duration-200 hover:border-slate-400"
                        placeholder="Juan"
                        required
                    />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Middle Name</span>
                    </label>
                    <input
                        type="text"
                        id="middleName"
                        class="w-full pl-4 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-slate-900 placeholder-slate-400 transition-all duration-200 hover:border-slate-400"
                        placeholder="Santos"
                    />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Last Name *</span>
                    </label>
                    <input
                        type="text"
                        id="lastName"
                        class="w-full pl-4 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-slate-900 placeholder-slate-400 transition-all duration-200 hover:border-slate-400"
                        placeholder="Dela Cruz"
                        required
                    />
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>Email *</span>
                </label>
                <div class="relative">
                    <input
                        type="email"
                        id="email"
                        class="w-full pl-4 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-slate-900 placeholder-slate-400 transition-all duration-200 hover:border-slate-400"
                        required
                    />
                </div>
                <p class="text-xs text-slate-500 mt-1" id="emailNote"></p>
            </div>

            <div id="passwordField" class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Password *</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        class="w-full pl-4 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-slate-900 placeholder-slate-400 transition-all duration-200 hover:border-slate-400"
                    />
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Role</span>
                </label>
                <div class="relative">
                    <select
                        id="role"
                        class="w-full pl-4 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-slate-900 transition-all duration-200 hover:border-slate-400 appearance-none"
                    >
                        <option value="resident">Resident</option>
                        <option value="admin">Admin</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span>Phone Number</span>
                </label>
                <div class="relative">
                    <input
                        type="tel"
                        id="phone"
                        class="w-full pl-4 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-slate-900 placeholder-slate-400 transition-all duration-200 hover:border-slate-400"
                    />
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Address</span>
                </label>
                <div class="relative">
                    <input
                        type="text"
                        id="address"
                        class="w-full pl-4 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-slate-900 placeholder-slate-400 transition-all duration-200 hover:border-slate-400"
                    />
                </div>
            </div>

            <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg"></div>

            <!-- Action Buttons - Landscape Layout -->
            <div class="flex flex-col sm:flex-row gap-3 sm:space-x-3 pt-4">
                <button
                    type="button"
                    onclick="closeUserModal()"
                    class="flex-1 px-6 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-all duration-200 font-medium border border-slate-200"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-[1.02]"
                >
                    <span id="submitButtonText">Add User</span>
                </button>
            </div>
        </form>
        </div>
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
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">User Details</h3>
                <div class="flex items-center space-x-2">
                    <button
                        onclick='editUser(${JSON.stringify(user)})'
                        class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors"
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
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Full Name</span>
                    </label>
                    <p class="text-gray-900">${fullName}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>Email Address</span>
                    </label>
                    <p class="text-gray-900">${user.email}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>Phone Number</span>
                    </label>
                    <p class="text-gray-900">${user.phone || 'Not provided'}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Address</span>
                    </label>
                    <p class="text-gray-900">${user.address || 'Not provided'}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 flex items-center space-x-2 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Registered Date</span>
                    </label>
                    <p class="text-gray-900">${user.created_at ? new Date(user.created_at).toLocaleDateString() : 'Unknown'}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 mb-1">Account Role</label>
                    <p class="text-gray-900 capitalize">${user.role}</p>
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
                            <div class="bg-white rounded-lg p-12 text-center border border-gray-200 shadow-sm">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-600">Select a user to view details</p>
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
    
    // Validate role - only allow 'resident' or 'admin'
    if (role !== 'resident' && role !== 'admin') {
        showError('Invalid role. Only "Resident" and "Admin" roles are allowed.');
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
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="text-gray-900 font-semibold">${fullName}</h4>
                                <p class="text-sm text-gray-600">${email}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${roleColor}">
                                ${role}
                            </span>
                        </div>
                        ${address ? `<p class="text-sm text-gray-600 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>${address}</span>
                        </p>` : ''}
                        ${phone ? `<p class="text-sm text-gray-600 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>${phone}</span>
                        </p>` : ''}
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
                // Add new user card without page reload
                const newUser = {
                    first_name: firstName,
                    middle_name: middleName,
                    last_name: lastName,
                    email: email,
                    role: role,
                    phone: phone,
                    address: address,
                    created_at: new Date().toISOString()
                };

                // Create new user card HTML
                const fullName = (firstName + ' ' + (middleName || '') + ' ' + lastName).trim();
                const roleColor = role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700';

                const newCardHTML = `
                    <div
                        class="user-card bg-white rounded-lg p-6 border border-gray-200 hover:border-gray-300 cursor-pointer transition-colors hover:shadow-md shadow-sm"
                        data-user-email="${email}"
                        data-search="${(fullName + ' ' + email + ' ' + (address || '')).toLowerCase()}"
                        onclick="selectUser(${JSON.stringify(newUser)})"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="text-gray-900 font-semibold">${fullName}</h4>
                                <p class="text-sm text-gray-600">${email}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${roleColor}">
                                ${role}
                            </span>
                        </div>
                        ${address ? `<p class="text-sm text-gray-600 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>${address}</span>
                        </p>` : ''}
                        ${phone ? `<p class="text-sm text-gray-600 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>${phone}</span>
                        </p>` : ''}
                    </div>
                `;

                // Add to the users list
                const usersList = document.querySelector('.space-y-3');
                if (usersList) {
                    usersList.insertAdjacentHTML('afterbegin', newCardHTML);
                    // Update stats
                    updateUserStats();
                }
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