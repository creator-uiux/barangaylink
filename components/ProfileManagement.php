<?php
/**
 * Profile Management Component - EXACT MATCH to ProfileManagement.tsx
 * Allows users to edit their profile and view account statistics
 */

function ProfileManagement($user) {
    // Form processing is now handled in index.php to prevent header errors
    
    // Get user stats from JSON files
    $concernsData = json_decode(file_get_contents(__DIR__ . '/../data/concerns.json'), true) ?: [];
    $documentsData = json_decode(file_get_contents(__DIR__ . '/../data/requests.json'), true) ?: [];
    
    $concerns = array_filter($concernsData, fn($c) => $c['submittedByEmail'] === ($user['email'] ?? ''));
    $documents = array_filter($documentsData, fn($d) => $d['requestedByEmail'] === ($user['email'] ?? ''));
    
    $stats = [
        'totalConcerns' => count($concerns),
        'pendingConcerns' => count(array_filter($concerns, fn($c) => $c['status'] === 'pending')),
        'resolvedConcerns' => count(array_filter($concerns, fn($c) => $c['status'] === 'resolved')),
        'totalDocuments' => count($documents),
        'pendingDocuments' => count(array_filter($documents, fn($d) => $d['status'] === 'pending')),
        'approvedDocuments' => count(array_filter($documents, fn($d) => $d['status'] === 'approved'))
    ];
    
    $saved = isset($_SESSION['profile_saved']) ? $_SESSION['profile_saved'] : false;
    unset($_SESSION['profile_saved']);
    
    ob_start();
?>
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-blue-900 mb-2">Profile Management</h2>
        <p class="text-blue-600">Manage your personal information and view account statistics</p>
    </div>

    <?php if ($saved): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        Profile updated successfully!
    </div>
    <?php endif; ?>

    <!-- Profile Information -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-blue-900">Personal Information</h3>
            <button
                onclick="toggleEdit()"
                id="edit-btn"
                class="flex items-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Profile</span>
            </button>
            <div id="save-cancel-btns" class="hidden items-center space-x-2">
                <button
                    onclick="cancelEdit()"
                    class="px-4 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors"
                >
                    Cancel
                </button>
                <button
                    onclick="saveProfile()"
                    class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>

        <form id="profile-form" method="POST" action="">
            <input type="hidden" name="action" value="update_profile">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-blue-700 mb-2">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            First Name
                        </label>
                        <input
                            type="text"
                            name="first_name"
                            id="first_name-input"
                            value="<?php 
                                $nameParts = explode(' ', $user['name'] ?? '');
                                $firstName = !empty($nameParts) ? $nameParts[0] : '';
                                echo htmlspecialchars($user['first_name'] ?? $firstName); 
                            ?>"
                            class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 hidden"
                        />
                        <p id="first_name-display" class="text-blue-900 px-4 py-2 bg-blue-50 rounded-lg"><?php 
                            $nameParts = explode(' ', $user['name'] ?? '');
                            $firstName = !empty($nameParts) ? $nameParts[0] : '';
                            echo htmlspecialchars($user['first_name'] ?? $firstName); 
                        ?></p>
                    </div>
                    <div>
                        <label class="block text-blue-700 mb-2">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Middle Name
                        </label>
                        <input
                            type="text"
                            name="middle_name"
                            id="middle_name-input"
                            value="<?php 
                                $nameParts = explode(' ', $user['name'] ?? '');
                                $middleName = count($nameParts) > 2 ? $nameParts[1] : '';
                                echo htmlspecialchars($user['middle_name'] ?? $middleName); 
                            ?>"
                            class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 hidden"
                        />
                        <p id="middle_name-display" class="text-blue-900 px-4 py-2 bg-blue-50 rounded-lg"><?php 
                            $nameParts = explode(' ', $user['name'] ?? '');
                            $middleName = count($nameParts) > 2 ? $nameParts[1] : '';
                            echo htmlspecialchars($user['middle_name'] ?? $middleName); 
                        ?></p>
                    </div>
                    <div>
                        <label class="block text-blue-700 mb-2">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Last Name
                        </label>
                        <input
                            type="text"
                            name="last_name"
                            id="last_name-input"
                            value="<?php 
                                $nameParts = explode(' ', $user['name'] ?? '');
                                $lastName = count($nameParts) > 1 ? end($nameParts) : '';
                                echo htmlspecialchars($user['last_name'] ?? $lastName); 
                            ?>"
                            class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 hidden"
                        />
                        <p id="last_name-display" class="text-blue-900 px-4 py-2 bg-blue-50 rounded-lg"><?php 
                            $nameParts = explode(' ', $user['name'] ?? '');
                            $lastName = count($nameParts) > 1 ? end($nameParts) : '';
                            echo htmlspecialchars($user['last_name'] ?? $lastName); 
                        ?></p>
                    </div>
                </div>

                <div>
                    <label class="block text-blue-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Email Address
                    </label>
                    <p class="text-blue-900 px-4 py-2 bg-blue-50 rounded-lg"><?php echo htmlspecialchars($user['email'] ?? 'No email'); ?></p>
                    <p id="email-notice" class="text-xs text-blue-600 mt-1 hidden">Email cannot be changed</p>
                </div>

                <div>
                    <label class="block text-blue-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        Phone Number
                    </label>
                    <input
                        type="tel"
                        name="phone"
                        id="phone-input"
                        value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                        placeholder="Enter phone number"
                        class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 hidden"
                    />
                    <p id="phone-display" class="text-blue-900 px-4 py-2 bg-blue-50 rounded-lg">
                        <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?>
                    </p>
                </div>

                <div>
                    <label class="block text-blue-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Address
                    </label>
                    <input
                        type="text"
                        name="address"
                        id="address-input"
                        value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"
                        placeholder="Enter address"
                        class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 hidden"
                    />
                    <p id="address-display" class="text-blue-900 px-4 py-2 bg-blue-50 rounded-lg">
                        <?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?>
                    </p>
                </div>
            </div>
        </form>
    </div>

    <!-- Account Statistics -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <h3 class="text-blue-900 mb-6">Account Statistics</h3>
        
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <!-- Total Concerns -->
            <div class="bg-blue-50 border-blue-100 rounded-lg p-4 border">
                <h4 class="text-blue-700 mb-2">Total Concerns</h4>
                <p class="text-3xl text-blue-900 mb-1"><?php echo $stats['totalConcerns']; ?></p>
                <p class="text-sm text-blue-600"><?php echo $stats['pendingConcerns']; ?> pending, <?php echo $stats['resolvedConcerns']; ?> resolved</p>
            </div>

            <!-- Total Documents -->
            <div class="bg-green-50 border-green-100 rounded-lg p-4 border">
                <h4 class="text-blue-700 mb-2">Total Documents</h4>
                <p class="text-3xl text-blue-900 mb-1"><?php echo $stats['totalDocuments']; ?></p>
                <p class="text-sm text-blue-600"><?php echo $stats['pendingDocuments']; ?> pending, <?php echo $stats['approvedDocuments']; ?> approved</p>
            </div>

            <!-- Account Role -->
            <div class="bg-purple-50 border-purple-100 rounded-lg p-4 border">
                <h4 class="text-blue-700 mb-2">Account Role</h4>
                <p class="text-3xl text-blue-900 mb-1"><?php echo strtoupper($user['role'] ?? 'user'); ?></p>
                <p class="text-sm text-blue-600">User account</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <h3 class="text-blue-900 mb-4">Quick Actions</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <!-- Change Password -->
            <button
                onclick="alert('Password change feature coming soon')"
                class="flex items-start space-x-3 p-4 border border-blue-100 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all text-left"
            >
                <div class="flex-1">
                    <h4 class="text-blue-900 mb-1">Change Password</h4>
                    <p class="text-sm text-blue-600">Update your account password</p>
                </div>
            </button>

            <!-- Download My Data -->
            <button
                onclick="downloadData()"
                class="flex items-start space-x-3 p-4 border border-blue-100 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all text-left"
            >
                <div class="flex-1">
                    <h4 class="text-blue-900 mb-1">Download My Data</h4>
                    <p class="text-sm text-blue-600">Export your account information</p>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
let isEditing = false;

function toggleEdit() {
    isEditing = true;
    document.getElementById('edit-btn').classList.add('hidden');
    document.getElementById('save-cancel-btns').classList.remove('hidden');
    document.getElementById('save-cancel-btns').classList.add('flex');
    
    // Show inputs, hide displays for all name fields
    document.getElementById('first_name-input').classList.remove('hidden');
    document.getElementById('first_name-display').classList.add('hidden');
    document.getElementById('middle_name-input').classList.remove('hidden');
    document.getElementById('middle_name-display').classList.add('hidden');
    document.getElementById('last_name-input').classList.remove('hidden');
    document.getElementById('last_name-display').classList.add('hidden');
    document.getElementById('phone-input').classList.remove('hidden');
    document.getElementById('phone-display').classList.add('hidden');
    document.getElementById('address-input').classList.remove('hidden');
    document.getElementById('address-display').classList.add('hidden');
    document.getElementById('email-notice').classList.remove('hidden');
}

function cancelEdit() {
    isEditing = false;
    document.getElementById('edit-btn').classList.remove('hidden');
    document.getElementById('save-cancel-btns').classList.add('hidden');
    document.getElementById('save-cancel-btns').classList.remove('flex');
    
    // Hide inputs, show displays for all name fields
    document.getElementById('first_name-input').classList.add('hidden');
    document.getElementById('first_name-display').classList.remove('hidden');
    document.getElementById('middle_name-input').classList.add('hidden');
    document.getElementById('middle_name-display').classList.remove('hidden');
    document.getElementById('last_name-input').classList.add('hidden');
    document.getElementById('last_name-display').classList.remove('hidden');
    document.getElementById('phone-input').classList.add('hidden');
    document.getElementById('phone-display').classList.remove('hidden');
    document.getElementById('address-input').classList.add('hidden');
    document.getElementById('address-display').classList.remove('hidden');
    document.getElementById('email-notice').classList.add('hidden');
    
    // Reset form
    document.getElementById('profile-form').reset();
}

function saveProfile() {
    // Simply submit the form - no AJAX needed
    document.getElementById('profile-form').submit();
}

function downloadData() {
    const data = {
        user: <?php echo json_encode($user); ?>,
        concerns: <?php echo json_encode(array_values($concerns)); ?>,
        documents: <?php echo json_encode(array_values($documents)); ?>
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'barangaylink-data-<?php echo $user['email'] ?? 'user'; ?>.json';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
<?php
    return ob_get_clean();
}
?>