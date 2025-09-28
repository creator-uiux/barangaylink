<?php
session_start();

function renderAuthModal() {
    $auth_message = '';
    $auth_error = '';
    $active_tab = 'login';
    
    // Handle authentication
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['login_form'])) {
            $active_tab = 'login';
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = $_POST['role'] ?? '';
            $remember_me = isset($_POST['rememberMe']);
            
            if (empty($role)) {
                $auth_error = 'Please select your role (Admin or User)';
            } elseif (handleLogin($email, $password, $role, $remember_me)) {
                $auth_message = 'Login successful! Redirecting...';
                echo '<script>setTimeout(() => { window.location.reload(); }, 1000);</script>';
            } else {
                $auth_error = 'Invalid credentials or email not registered';
            }
        } 
        elseif (isset($_POST['signup_form'])) {
            $active_tab = 'signup';
            $fullName = trim($_POST['fullName'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirmPassword'] ?? '');
            $role = $_POST['role'] ?? '';
            
            if (empty($role)) {
                $auth_error = 'Please select your role (Admin or User)';
            } elseif (strlen($password) < 6) {
                $auth_error = 'Password must be at least 6 characters long';
            } elseif ($password !== $confirmPassword) {
                $auth_error = 'Passwords do not match';
            } elseif (handleSignup($fullName, $email, $password, $role)) {
                $auth_message = 'Account created successfully! You can now login.';
                $active_tab = 'login';
            } else {
                $auth_error = 'Email already exists or invalid information';
            }
        }
        elseif (isset($_POST['reset_form'])) {
            $active_tab = 'reset';
            $email = trim($_POST['email'] ?? '');
            
            if (handleResetPassword($email)) {
                $auth_message = 'Password reset instructions sent to your email';
            } else {
                $auth_error = 'Email not found or error occurred';
            }
        }
    }
?>

<!-- Auth Modal Overlay -->
<div id="auth-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Welcome to BarangayLink</h2>
            <p class="text-slate-600">Sign in to your account or create a new one to access BarangayLink services.</p>
        </div>
        
        <?php if ($auth_message): ?>
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">
                <?php echo htmlspecialchars($auth_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($auth_error): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-md">
                <?php echo htmlspecialchars($auth_error); ?>
            </div>
        <?php endif; ?>
        
        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200 mb-6">
            <button onclick="switchTab('login')" id="login-tab" class="flex-1 py-2 px-4 text-center border-b-2 <?php echo $active_tab === 'login' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'; ?> font-medium">
                Login
            </button>
            <button onclick="switchTab('signup')" id="signup-tab" class="flex-1 py-2 px-4 text-center border-b-2 <?php echo $active_tab === 'signup' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'; ?> font-medium">
                Sign Up
            </button>
            <button onclick="switchTab('reset')" id="reset-tab" class="flex-1 py-2 px-4 text-center border-b-2 <?php echo $active_tab === 'reset' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'; ?> font-medium">
                Reset
            </button>
        </div>
        
        <!-- Login Form -->
        <div id="login-content" class="<?php echo $active_tab !== 'login' ? 'hidden' : ''; ?>">
            <form method="POST" class="space-y-4">
                <input type="hidden" name="login_form" value="1">
                
                <div class="space-y-2">
                    <label for="login-email" class="block font-medium text-slate-900">Email</label>
                    <input
                        id="login-email"
                        name="email"
                        type="email"
                        placeholder="Enter your email"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                        value="<?php echo isset($_POST['email']) && isset($_POST['login_form']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    />
                </div>
                
                <div class="space-y-2">
                    <label for="login-password" class="block font-medium text-slate-900">Password</label>
                    <input
                        id="login-password"
                        name="password"
                        type="password"
                        placeholder="Enter your password"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                    />
                </div>
                
                <div class="space-y-2">
                    <label class="block font-medium text-slate-900">Select your role</label>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="role-user" name="role" value="user" class="text-blue-600 focus:ring-blue-500" required <?php echo (isset($_POST['role']) && $_POST['role'] === 'user' && isset($_POST['login_form'])) ? 'checked' : ''; ?>>
                            <label for="role-user" class="text-slate-900">User</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="role-admin" name="role" value="admin" class="text-blue-600 focus:ring-blue-500" required <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin' && isset($_POST['login_form'])) ? 'checked' : ''; ?>>
                            <label for="role-admin" class="text-slate-900">Admin</label>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="rememberMe" name="rememberMe" class="text-blue-600 focus:ring-blue-500" <?php echo (isset($_POST['rememberMe']) && isset($_POST['login_form'])) ? 'checked' : ''; ?>>
                    <label for="rememberMe" class="text-sm text-slate-900">Remember me</label>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                    Login
                </button>
            </form>
        </div>
        
        <!-- Signup Form -->
        <div id="signup-content" class="<?php echo $active_tab !== 'signup' ? 'hidden' : ''; ?>">
            <form method="POST" class="space-y-4">
                <input type="hidden" name="signup_form" value="1">
                
                <div class="space-y-2">
                    <label for="signup-fullname" class="block font-medium text-slate-900">Full Name</label>
                    <input
                        id="signup-fullname"
                        name="fullName"
                        type="text"
                        placeholder="Enter your full name"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                        value="<?php echo isset($_POST['fullName']) && isset($_POST['signup_form']) ? htmlspecialchars($_POST['fullName']) : ''; ?>"
                    />
                </div>
                
                <div class="space-y-2">
                    <label for="signup-email" class="block font-medium text-slate-900">Email</label>
                    <input
                        id="signup-email"
                        name="email"
                        type="email"
                        placeholder="Enter your email"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                        value="<?php echo isset($_POST['email']) && isset($_POST['signup_form']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    />
                </div>
                
                <div class="space-y-2">
                    <label for="signup-password" class="block font-medium text-slate-900">Password</label>
                    <input
                        id="signup-password"
                        name="password"
                        type="password"
                        placeholder="Enter your password"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                    />
                    <p class="text-sm text-gray-500">Password must be at least 6 characters long</p>
                </div>
                
                <div class="space-y-2">
                    <label for="signup-confirm-password" class="block font-medium text-slate-900">Confirm Password</label>
                    <input
                        id="signup-confirm-password"
                        name="confirmPassword"
                        type="password"
                        placeholder="Confirm your password"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                    />
                </div>
                
                <div class="space-y-2">
                    <label class="block font-medium text-slate-900">Select your role</label>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="signup-role-user" name="role" value="user" class="text-blue-600 focus:ring-blue-500" required <?php echo (isset($_POST['role']) && $_POST['role'] === 'user' && isset($_POST['signup_form'])) ? 'checked' : ''; ?>>
                            <label for="signup-role-user" class="text-slate-900">User</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="signup-role-admin" name="role" value="admin" class="text-blue-600 focus:ring-blue-500" required <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin' && isset($_POST['signup_form'])) ? 'checked' : ''; ?>>
                            <label for="signup-role-admin" class="text-slate-900">Admin</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                    Register
                </button>
            </form>
        </div>
        
        <!-- Reset Form -->
        <div id="reset-content" class="<?php echo $active_tab !== 'reset' ? 'hidden' : ''; ?>">
            <form method="POST" class="space-y-4">
                <input type="hidden" name="reset_form" value="1">
                
                <p class="text-sm text-gray-600 mb-4">
                    Enter your email address and we'll send you instructions to reset your password.
                </p>
                
                <div class="space-y-2">
                    <label for="reset-email" class="block font-medium text-slate-900">Email</label>
                    <input
                        id="reset-email"
                        name="email"
                        type="email"
                        placeholder="Enter your email"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-input-background"
                        required
                        value="<?php echo isset($_POST['email']) && isset($_POST['reset_form']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    />
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                    Reset Password
                </button>
            </form>
        </div>
        
        <!-- Close Button -->
        <button onclick="hideAuthModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script>
function showAuthModal() {
    document.getElementById('auth-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideAuthModal() {
    document.getElementById('auth-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function switchTab(tab) {
    // Hide all content
    document.getElementById('login-content').classList.add('hidden');
    document.getElementById('signup-content').classList.add('hidden');
    document.getElementById('reset-content').classList.add('hidden');
    
    // Reset all tab styles
    const tabs = ['login-tab', 'signup-tab', 'reset-tab'];
    tabs.forEach(tabId => {
        const tabElement = document.getElementById(tabId);
        tabElement.classList.remove('border-blue-600', 'text-blue-600');
        tabElement.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected content and style
    document.getElementById(tab + '-content').classList.remove('hidden');
    const activeTab = document.getElementById(tab + '-tab');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-blue-600', 'text-blue-600');
}

// Show modal if there are auth messages
<?php if ($auth_message || $auth_error): ?>
    document.addEventListener('DOMContentLoaded', function() {
        showAuthModal();
        switchTab('<?php echo $active_tab; ?>');
    });
<?php endif; ?>

// Close modal when clicking outside
document.getElementById('auth-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideAuthModal();
    }
});
</script>

<?php
}

// Authentication functions
function handleLogin($email, $password, $role, $remember_me) {
    // In a real application, you would validate against a database
    // For now, we'll simulate with hardcoded users
    $users = getUserData();
    
    $user_key = $email . '_' . $role;
    if (isset($users[$user_key]) && password_verify($password, $users[$user_key]['password'])) {
        $_SESSION['user'] = [
            'email' => $email,
            'fullName' => $users[$user_key]['fullName'],
            'role' => $role,
            'id' => $users[$user_key]['id']
        ];
        
        if ($remember_me) {
            setcookie('remember_user', $user_key, time() + (86400 * 30), '/'); // 30 days
        }
        
        return true;
    }
    
    return false;
}

function handleSignup($fullName, $email, $password, $role) {
    $users = getUserData();
    $user_key = $email . '_' . $role;
    
    // Check if user already exists
    if (isset($users[$user_key])) {
        return false;
    }
    
    // Add new user
    $users[$user_key] = [
        'id' => uniqid(),
        'fullName' => $fullName,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    saveUserData($users);
    return true;
}

function handleResetPassword($email) {
    // In a real application, you would send an email
    // For now, we'll just check if the email exists
    $users = getUserData();
    
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return true;
        }
    }
    
    return false;
}

function getUserData() {
    $file = __DIR__ . '/../data/users.json';
    if (file_exists($file)) {
        $data = file_get_contents($file);
        return json_decode($data, true) ?: [];
    }
    return [];
}

function saveUserData($users) {
    $dir = __DIR__ . '/../data';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $file = $dir . '/users.json';
    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
}
?>