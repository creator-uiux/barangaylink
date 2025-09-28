<?php
function renderAddUserModal() {
    $message = '';
    $error = '';
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
        $fullName = trim($_POST['fullName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = $_POST['role'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $password = $_POST['password'] ?? '';
        $status = $_POST['status'] ?? 'active';
        
        if (empty($fullName) || empty($email) || empty($role) || empty($password)) {
            $error = 'Please fill in all required fields';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address';
        } elseif (userExists($email, $role)) {
            $error = 'A user with this email already exists';
        } else {
            if (createUser($fullName, $email, $phone, $role, $address, $password, $status)) {
                $message = 'User created successfully!';
                echo '<script>hideAddUserModal(); showToast("' . addslashes($message) . '", "success"); window.location.reload();</script>';
            } else {
                $error = 'Failed to create user. Please try again.';
            }
        }
    }
?>

<!-- Add User Modal -->
<div id="add-user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900 mb-2">Add New User</h2>
            <p class="text-slate-600">Manually register a new user in the system</p>
        </div>
        
        <?php if ($message): ?>
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-md">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4" id="add-user-form">
            <input type="hidden" name="add_user" value="1">
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Full Name *</label>
                    <input
                        name="fullName"
                        type="text"
                        placeholder="Enter full name"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                        value="<?php echo isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : ''; ?>"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Email Address *</label>
                    <input
                        name="email"
                        type="email"
                        placeholder="Enter email address"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Phone Number</label>
                    <input
                        name="phone"
                        type="tel"
                        placeholder="Enter phone number"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Role *</label>
                    <select
                        name="role"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                    >
                        <option value="">Select role</option>
                        <option value="user" <?php echo (isset($_POST['role']) && $_POST['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900 mb-2">Address</label>
                <textarea
                    name="address"
                    placeholder="Enter address"
                    rows="3"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background resize-none"
                ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900 mb-2">Temporary Password *</label>
                <input
                    name="password"
                    type="password"
                    placeholder="Enter temporary password"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                    required
                />
                <p class="text-sm text-gray-600 mt-1">
                    User will be prompted to change password on first login
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900 mb-2">Initial Status</label>
                <select
                    name="status"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                >
                    <option value="active" <?php echo (!isset($_POST['status']) || $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div class="flex space-x-3 pt-4">
                <button 
                    type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors"
                    id="add-user-submit-btn"
                >
                    Create User
                </button>
                <button 
                    type="button" 
                    onclick="hideAddUserModal()"
                    class="flex-1 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-6 py-2 rounded-md font-medium transition-colors"
                >
                    Cancel
                </button>
            </div>
        </form>
        
        <!-- Close Button -->
        <button onclick="hideAddUserModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script>
function showAddUserModal() {
    document.getElementById('add-user-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideAddUserModal() {
    document.getElementById('add-user-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Reset form if no errors
    <?php if (!$error): ?>
    document.getElementById('add-user-form').reset();
    <?php endif; ?>
}

// Show modal if there are errors
<?php if ($error && isset($_POST['add_user'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        showAddUserModal();
    });
<?php endif; ?>

// Close modal when clicking outside
document.getElementById('add-user-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideAddUserModal();
    }
});

// Form submission handling
document.getElementById('add-user-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('add-user-submit-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';
    
    // Re-enable button after timeout as fallback
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create User';
    }, 5000);
});
</script>

<?php
}

function userExists($email, $role) {
    include_once 'AuthModal.php';
    $users = getUserData();
    $userKey = $email . '_' . $role;
    return isset($users[$userKey]);
}

function createUser($fullName, $email, $phone, $role, $address, $password, $status) {
    include_once 'AuthModal.php';
    $users = getUserData();
    $userKey = $email . '_' . $role;
    
    // Check if user already exists
    if (isset($users[$userKey])) {
        return false;
    }
    
    // Add new user
    $users[$userKey] = [
        'id' => uniqid(),
        'fullName' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
        'status' => $status,
        'created_at' => date('Y-m-d H:i:s'),
        'createdByAdmin' => true
    ];
    
    saveUserData($users);
    return true;
}
?>