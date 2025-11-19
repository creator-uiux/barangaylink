<?php
/**
 * Profile Management - 100% SYNCHRONIZED with components/ProfileManagement.tsx
 * EXACT MATCH - Every feature, every field, every style
 */

$user = getCurrentUser();
if (!$user) {
    header('Location: ../index.php');
    exit;
}

$db = getDB();
$userId = $user['id'];

// Get user statistics
$totalConcerns = fetchOne("SELECT COUNT(*) as total FROM concerns WHERE user_id = ?", [$userId])['total'];
$pendingConcerns = fetchOne("SELECT COUNT(*) as total FROM concerns WHERE user_id = ? AND status = 'pending'", [$userId])['total'];
$resolvedConcerns = fetchOne("SELECT COUNT(*) as total FROM concerns WHERE user_id = ? AND status = 'resolved'", [$userId])['total'];
$totalDocuments = fetchOne("SELECT COUNT(*) as total FROM documents WHERE user_id = ?", [$userId])['total'];
$pendingDocuments = fetchOne("SELECT COUNT(*) as total FROM documents WHERE user_id = ? AND status = 'pending'", [$userId])['total'];
$approvedDocuments = fetchOne("SELECT COUNT(*) as total FROM documents WHERE user_id = ? AND status = 'approved'", [$userId])['total'];

// Generate initials
$firstName = $user['firstName'] ?? '';
$lastName = $user['lastName'] ?? '';
$middleName = $user['middleName'] ?? '';
$fullName = trim("$firstName $middleName $lastName");
$initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

// Check for success message
$saved = isset($_GET['saved']) && $_GET['saved'] === '1';
?>

<div class="space-y-6">
    <!-- Header with Profile Avatar - EXACT MATCH TSX Lines 127-148 -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg p-6 text-white">
        <div class="flex items-center space-x-4">
            <div class="w-20 h-20 bg-white/20 backdrop-blur-lg rounded-full flex items-center justify-center border-4 border-white/30">
                <span class="text-3xl"><?php echo $initials; ?></span>
            </div>
            <div>
                <h2 class="text-2xl mb-1"><?php echo htmlspecialchars($fullName); ?></h2>
                <p class="text-blue-100"><?php echo htmlspecialchars($user['email']); ?></p>
                <div class="flex items-center space-x-2 mt-2">
                    <div class="px-3 py-1 bg-white/20 rounded-full text-sm">
                        <?php echo $user['role'] === 'resident' ? 'Resident' : ($user['role'] === 'admin' ? 'Administrator' : $user['role']); ?>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-green-300">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message - EXACT MATCH TSX Lines 150-157 -->
    <?php if ($saved): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Profile updated successfully!</span>
        </div>
        <script>
            // Auto-hide after 3 seconds
            setTimeout(() => {
                window.location.href = '?view=profile';
            }, 3000);
        </script>
    <?php endif; ?>

    <!-- Profile Information - EXACT MATCH TSX Lines 160-308 -->
    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl">Personal Information</h3>
            <button
                onclick="toggleEdit()"
                id="editButton"
                class="flex items-center space-x-2 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Profile</span>
            </button>
            <div id="saveButtons" class="flex items-center space-x-2" style="display: none;">
                <button
                    onclick="cancelEdit()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    Cancel
                </button>
                <button
                    onclick="saveProfile()"
                    id="saveBtn"
                    class="flex items-center space-x-2 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>

    <form id="profileForm" method="POST" action="../api/users.php">
            <input type="hidden" name="action" value="update_profile">
            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- First Name - EXACT MATCH TSX Lines 204-216 -->
                <div>
                    <label class="block text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        First Name
                    </label>
                    <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-gray-900"><?php echo htmlspecialchars($firstName ?: 'Not set'); ?></p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Cannot be changed
                    </p>
                </div>

                <!-- Last Name - EXACT MATCH TSX Lines 218-230 -->
                <div>
                    <label class="block text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Last Name
                    </label>
                    <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-gray-900"><?php echo htmlspecialchars($lastName ?: 'Not set'); ?></p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Cannot be changed
                    </p>
                </div>

                <!-- Middle Name - EXACT MATCH TSX Lines 232-244 -->
                <div>
                    <label class="block text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Middle Name
                    </label>
                    <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-gray-900"><?php echo htmlspecialchars($middleName ?: 'Not set'); ?></p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Cannot be changed
                    </p>
                </div>

                <!-- Email - EXACT MATCH TSX Lines 246-255 -->
                <div>
                    <label class="block text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Address
                    </label>
                    <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-gray-900"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                </div>

                <!-- Phone - EXACT MATCH TSX Lines 257-280 -->
                <div>
                    <label class="block text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Phone Number
                    </label>
                    <div class="phone-display">
                        <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-gray-900"><?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></p>
                        </div>
                    </div>
                    <input
                        type="tel"
                        name="phone"
                        id="phoneInput"
                        value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                        class="phone-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="+63 912 345 6789"
                        style="display: none;"
                        required
                    />
                    <p class="phone-input text-xs text-gray-500 mt-1" style="display: none;">
                        Philippine format: +63 followed by your number
                    </p>
                </div>

                <!-- Address - EXACT MATCH TSX Lines 282-306 -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Complete Address
                    </label>
                    <div class="address-display">
                        <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-gray-900"><?php echo htmlspecialchars($user['address'] ?: 'Not provided'); ?></p>
                        </div>
                    </div>
                    <input
                        type="text"
                        name="address"
                        id="addressInput"
                        value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"
                        class="address-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="123 Main Street, Barangay Centro, City, Province"
                        minlength="10"
                        style="display: none;"
                    />
                    <p class="address-input text-xs text-gray-500 mt-1" style="display: none;">
                        Provide your complete address (at least 10 characters)
                    </p>
                </div>
            </div>
        </form>
    </div>

    <!-- Account Statistics - EXACT MATCH TSX Lines 311-334 -->
    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <h3 class="text-xl mb-6">Account Statistics</h3>
        
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Total Concerns -->
            <div class="bg-blue-50 border-blue-200 text-blue-600 rounded-lg p-4 border">
                <h4 class="text-gray-700 mb-2">Total Concerns</h4>
                <p class="text-3xl text-blue-900 mb-1"><?php echo $totalConcerns; ?></p>
                <p class="text-sm text-gray-600"><?php echo $pendingConcerns; ?> pending, <?php echo $resolvedConcerns; ?> resolved</p>
            </div>

            <!-- Total Documents -->
            <div class="bg-green-50 border-green-200 text-green-600 rounded-lg p-4 border">
                <h4 class="text-gray-700 mb-2">Total Documents</h4>
                <p class="text-3xl text-green-900 mb-1"><?php echo $totalDocuments; ?></p>
                <p class="text-sm text-gray-600"><?php echo $pendingDocuments; ?> pending, <?php echo $approvedDocuments; ?> approved</p>
            </div>

            <!-- Account Type -->
            <div class="bg-purple-50 border-purple-200 text-purple-600 rounded-lg p-4 border">
                <h4 class="text-gray-700 mb-2">Account Type</h4>
                <p class="text-3xl text-purple-900 mb-1"><?php echo $user['role'] === 'resident' ? 'Resident' : ($user['role'] === 'admin' ? 'Admin' : $user['role']); ?></p>
                <p class="text-sm text-gray-600">User account</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions - EXACT MATCH TSX Lines 337-364 -->
    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <h3 class="text-xl mb-4">Quick Actions</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <button
                onclick="alert('Password change feature coming soon')"
                class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all text-left group"
            >
                <svg class="w-5 h-5 text-gray-400 group-hover:text-black transition-colors mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-gray-900 mb-1">Change Password</h4>
                    <p class="text-sm text-gray-600">Update your account password</p>
                </div>
            </button>

            <button
                onclick="downloadData()"
                class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all text-left group"
            >
                <svg class="w-5 h-5 text-gray-400 group-hover:text-black transition-colors mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-gray-900 mb-1">Download My Data</h4>
                    <p class="text-sm text-gray-600">Export your account information</p>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
// Edit/Save functionality - EXACT MATCH TSX behavior
let isEditing = false;

function toggleEdit() {
    isEditing = true;
    document.getElementById('editButton').style.display = 'none';
    document.getElementById('saveButtons').style.display = 'flex';
    
    // Show input fields, hide display fields
    document.querySelectorAll('.phone-input, .address-input').forEach(el => el.style.display = 'block');
    document.querySelectorAll('.phone-display, .address-display').forEach(el => el.style.display = 'none');
}

function cancelEdit() {
    isEditing = false;
    document.getElementById('editButton').style.display = 'flex';
    document.getElementById('saveButtons').style.display = 'none';
    
    // Hide input fields, show display fields
    document.querySelectorAll('.phone-input, .address-input').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.phone-display, .address-display').forEach(el => el.style.display = 'block');
    
    // Reset form
    document.getElementById('profileForm').reset();
}

function saveProfile() {
    const phone = document.getElementById('phoneInput').value;
    const address = document.getElementById('addressInput').value;
    
    // Validate phone
    if (phone) {
        const phoneRegex = /^(\+\d{1,3}[- ]?)?\d{10,}$/;
        if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
            alert('Please enter a valid phone number (e.g., +63 912 345 6789)');
            return;
        }
    }
    
    // Validate address
    if (address && address.trim().length < 10) {
        alert('Please provide a complete address (at least 10 characters)');
        return;
    }
    
    // Submit form
    document.getElementById('profileForm').submit();
}

function downloadData() {
    // Create data export
    const data = {
        user: {
            name: '<?php echo addslashes($fullName); ?>',
            email: '<?php echo addslashes($user['email']); ?>',
            address: '<?php echo addslashes($user['address'] ?? ''); ?>',
            phone: '<?php echo addslashes($user['phone'] ?? ''); ?>',
            role: '<?php echo $user['role']; ?>'
        },
        statistics: {
            totalConcerns: <?php echo $totalConcerns; ?>,
            pendingConcerns: <?php echo $pendingConcerns; ?>,
            resolvedConcerns: <?php echo $resolvedConcerns; ?>,
            totalDocuments: <?php echo $totalDocuments; ?>,
            pendingDocuments: <?php echo $pendingDocuments; ?>,
            approvedDocuments: <?php echo $approvedDocuments; ?>
        }
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'barangaylink-data-<?php echo $user['email']; ?>-<?php echo date('Y-m-d'); ?>.json';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
