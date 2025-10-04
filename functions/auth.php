<?php
/**
 * Authentication Functions - PHP Version
 * 100% EXACT conversion matching App.tsx authentication patterns
 */

/**
 * Initialize enhanced session (matching App.tsx state management)
 * @return bool
 */
function initializeEnhancedSession() {
    try {
        // Configure session settings for security
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize auth state if not exists (matching App.tsx authState)
        if (!isset($_SESSION['auth'])) {
            $_SESSION['auth'] = [
                'isAuthenticated' => false,
                'user' => null
            ];
        }
        
        // Initialize user session for backward compatibility
        if (!isset($_SESSION['user'])) {
            $_SESSION['user'] = $_SESSION['auth']['user'];
        }
        
        return true;
    } catch (Exception $e) {
        error_log('Session initialization error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Check if user is authenticated (matching App.tsx isAuthenticated)
 * @return bool
 */
function isAuthenticated() {
    if (session_status() === PHP_SESSION_NONE) {
        return false;
    }
    
    // Check both auth state and legacy user session
    if (isset($_SESSION['auth']) && $_SESSION['auth']['isAuthenticated']) {
        return !empty($_SESSION['auth']['user']);
    }
    
    // Legacy check
    return isset($_SESSION['user']) && !empty($_SESSION['user']) && is_array($_SESSION['user']);
}

/**
 * Get current authenticated user (matching App.tsx user state)
 * @return array|null
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    // Return from auth state first
    if (isset($_SESSION['auth']['user'])) {
        return $_SESSION['auth']['user'];
    }
    
    // Legacy fallback
    return $_SESSION['user'] ?? null;
}

/**
 * Authenticate user with credentials (matching App.tsx handleLogin)
 * @param string $email
 * @param string $password
 * @return array
 */
function authenticateUser($email, $password) {
    try {
        // Sanitize inputs
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $password = trim($password);
        
        if (!$email || !$password) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        // Check admin credentials first (matching ADMIN_CREDENTIALS)
        if ($email === ADMIN_CREDENTIALS['email'] && $password === ADMIN_CREDENTIALS['password']) {
            $adminUser = [
                'email' => ADMIN_CREDENTIALS['email'],
                'name' => 'Admin User',
                'role' => 'admin'
            ];
            
            // Set auth state (matching App.tsx setAuthState)
            $_SESSION['auth'] = [
                'isAuthenticated' => true,
                'user' => $adminUser
            ];
            $_SESSION['user'] = $adminUser; // Legacy compatibility
            
            return [
                'success' => true, 
                'message' => 'Admin login successful',
                'user' => $adminUser,
                'redirect' => 'index.php?view=admin-dashboard'
            ];
        }
        
        // Check regular users (matching App.tsx user lookup)
        $users = loadJsonData('users');
        $foundUser = null;
        
        foreach ($users as $user) {
            if ($user['email'] === $email && $user['password'] === $password) {
                $foundUser = $user;
                break;
            }
        }
        
        if ($foundUser) {
            $userSession = [
                'email' => $foundUser['email'],
                'name' => $foundUser['name'],
                'role' => 'user',
                'address' => $foundUser['address'] ?? '',
                'phone' => $foundUser['phone'] ?? ''
            ];
            
            // Set auth state (matching App.tsx setAuthState)
            $_SESSION['auth'] = [
                'isAuthenticated' => true,
                'user' => $userSession
            ];
            $_SESSION['user'] = $userSession; // Legacy compatibility
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => $userSession,
                'redirect' => 'index.php?view=dashboard'
            ];
        }
        
        return ['success' => false, 'message' => 'Invalid email or password'];
        
    } catch (Exception $e) {
        error_log('Authentication error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Authentication error occurred'];
    }
}

/**
 * Register new user (matching App.tsx handleSignup)
 * @param array $userData
 * @return array
 */
function registerUser($userData) {
    try {
        // Validate required fields
        $requiredFields = ['name', 'email', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                return ['success' => false, 'message' => ucfirst($field) . ' is required'];
            }
        }
        
        // Sanitize and validate email
        $email = filter_var(trim($userData['email']), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        // Check if user already exists (matching App.tsx user check)
        $users = loadJsonData('users');
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return ['success' => false, 'message' => 'User with this email already exists'];
            }
        }
        
        // Create new user (matching App.tsx user creation)
        $newUser = [
            'email' => $email,
            'password' => trim($userData['password']), // In production, hash this!
            'name' => trim($userData['name']),
            'role' => 'user',
            'address' => trim($userData['address'] ?? ''),
            'phone' => trim($userData['phone'] ?? ''),
            'createdAt' => date('c')
        ];
        
        // Save user
        $users[] = $newUser;
        if (!saveJsonData('users', $users)) {
            return ['success' => false, 'message' => 'Failed to save user data'];
        }
        
        // Auto-login new user (matching App.tsx auto-login after signup)
        $userSession = [
            'email' => $newUser['email'],
            'name' => $newUser['name'],
            'role' => 'user',
            'address' => $newUser['address'],
            'phone' => $newUser['phone']
        ];
        
        $_SESSION['auth'] = [
            'isAuthenticated' => true,
            'user' => $userSession
        ];
        $_SESSION['user'] = $userSession; // Legacy compatibility
        
        // Create welcome notification
        createNotification('success', 'Welcome to BarangayLink!', 
            'Your account has been created successfully. Welcome to our digital governance platform.', 
            $email);
        
        return [
            'success' => true,
            'message' => 'Account created successfully',
            'user' => $userSession,
            'redirect' => 'index.php?view=dashboard'
        ];
        
    } catch (Exception $e) {
        error_log('Registration error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Registration error occurred'];
    }
}

/**
 * Logout user (matching App.tsx handleLogout)
 * @return bool
 */
function logoutUser() {
    try {
        // Clear auth state (matching App.tsx logout)
        $_SESSION['auth'] = [
            'isAuthenticated' => false,
            'user' => null
        ];
        $_SESSION['user'] = null; // Legacy compatibility
        
        // Destroy session
        session_destroy();
        
        return true;
    } catch (Exception $e) {
        error_log('Logout error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Check user permissions (role-based access)
 * @param string $requiredRole
 * @return bool
 */
function hasPermission($requiredRole = 'user') {
    if (!isAuthenticated()) {
        return false;
    }
    
    $user = getCurrentUser();
    if (!$user) {
        return false;
    }
    
    $userRole = $user['role'] ?? 'user';
    
    // Admin has access to everything
    if ($userRole === 'admin') {
        return true;
    }
    
    // Check specific role
    return $userRole === $requiredRole;
}

/**
 * Require authentication (middleware function)
 * @param string $redirectUrl
 * @return void
 */
function requireAuth($redirectUrl = 'index.php?auth=true') {
    if (!isAuthenticated()) {
        header('Location: ' . $redirectUrl);
        exit;
    }
}

/**
 * Require admin permission (middleware function)
 * @param string $redirectUrl
 * @return void
 */
function requireAdmin($redirectUrl = 'index.php?error=insufficient_permissions') {
    if (!hasPermission('admin')) {
        header('Location: ' . $redirectUrl);
        exit;
    }
}

/**
 * Get user profile data (matching React profile management)
 * @param string $email
 * @return array|null
 */
function getUserProfile($email) {
    try {
        $users = loadJsonData('users');
        
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                // Remove password from profile data
                unset($user['password']);
                return $user;
            }
        }
        
        return null;
    } catch (Exception $e) {
        error_log('Error getting user profile: ' . $e->getMessage());
        return null;
    }
}

/**
 * Update user profile (matching React profile updates)
 * @param string $email
 * @param array $updates
 * @return bool
 */
function updateUserProfile($email, $updates) {
    try {
        $users = loadJsonData('users');
        $updated = false;
        
        foreach ($users as &$user) {
            if ($user['email'] === $email) {
                // Allowed fields for update
                $allowedFields = ['name', 'address', 'phone'];
                
                foreach ($allowedFields as $field) {
                    if (isset($updates[$field])) {
                        $user[$field] = trim($updates[$field]);
                        $updated = true;
                    }
                }
                
                if ($updated) {
                    $user['updatedAt'] = date('c');
                    
                    // Update session data
                    $currentUser = getCurrentUser();
                    if ($currentUser && $currentUser['email'] === $email) {
                        $currentUser = array_merge($currentUser, array_intersect_key($updates, array_flip($allowedFields)));
                        $_SESSION['auth']['user'] = $currentUser;
                        $_SESSION['user'] = $currentUser; // Legacy compatibility
                    }
                }
                break;
            }
        }
        
        if ($updated) {
            return saveJsonData('users', $users);
        }
        
        return false;
    } catch (Exception $e) {
        error_log('Error updating user profile: ' . $e->getMessage());
        return false;
    }
}

/**
 * Validate session integrity (security function)
 * @return bool
 */
function validateSession() {
    if (!isAuthenticated()) {
        return false;
    }
    
    $user = getCurrentUser();
    if (!$user) {
        return false;
    }
    
    // Additional security checks can be added here
    // e.g., IP validation, user agent validation, etc.
    
    return true;
}

/**
 * Refresh session (extend session lifetime)
 * @return bool
 */
function refreshSession() {
    if (isAuthenticated()) {
        // Update session timestamp
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    return false;
}

/**
 * Get session info for debugging
 * @return array
 */
function getSessionInfo() {
    return [
        'session_id' => session_id(),
        'session_status' => session_status(),
        'is_authenticated' => isAuthenticated(),
        'user' => getCurrentUser(),
        'session_data' => $_SESSION ?? []
    ];
}

// Auto-initialize session if not already done
if (session_status() === PHP_SESSION_NONE) {
    initializeEnhancedSession();
}
?>