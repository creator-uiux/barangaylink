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
 * DEPRECATED: Use App.php handleLogin() instead
 * This function is kept for backward compatibility but should not be used
 * @deprecated Use BarangayLinkApp::handleLogin() instead
 */
function loginUser($email, $password) {
    // Redirect to main App class for consistency
    $app = new BarangayLinkApp();
    return $app->handleLogin($email, $password);
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
 * DEPRECATED: Use App.php handleLogin() instead
 * This function is kept for backward compatibility but should not be used
 * @deprecated Use BarangayLinkApp::handleLogin() instead
 */
function authenticateUser($email, $password) {
    // Redirect to main App class for consistency
    $app = new BarangayLinkApp();
    return $app->handleLogin($email, $password);
}

/**
 * Register new user (matching App.tsx handleSignup)
 * @param array $userData
 * @return array
 */
function registerUser($userData) {
    try {
        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'password', 'address', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                return ['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
            }
        }
        
        // Sanitize and validate email
        $email = filter_var(trim($userData['email']), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        // Check if user already exists (matching App.tsx user check)
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            $existingUser = getUserByEmail($email);
            if ($existingUser) {
                return ['success' => false, 'message' => 'User with this email already exists'];
            }
        } else {
            $users = loadJsonData('users');
            foreach ($users as $user) {
                if ($user['email'] === $email) {
                    return ['success' => false, 'message' => 'User with this email already exists'];
                }
            }
        }
        
        // Create new user (matching App.tsx user creation)
        $newUser = [
            'email' => $email,
            'password' => trim($userData['password']), // In production, hash this!
            'first_name' => trim($userData['first_name']),
            'middle_name' => trim($userData['middle_name'] ?? ''),
            'last_name' => trim($userData['last_name']),
            'role' => 'user',
            'status' => 'active', // Set default status
            'address' => trim($userData['address'] ?? ''),
            'phone' => trim($userData['phone'] ?? ''),
            'createdAt' => date('c')
        ];
        
        // Save user
        if (USE_DATABASE) {
            if (!createUser($newUser)) {
                return ['success' => false, 'message' => 'Failed to save user data'];
            }
        } else {
            $users[] = $newUser;
            if (!saveJsonData('users', $users)) {
                return ['success' => false, 'message' => 'Failed to save user data'];
            }
        }
        
        // Auto-login new user (matching App.tsx auto-login after signup)
        $userSession = [
            'email' => $newUser['email'],
            'first_name' => $newUser['first_name'],
            'middle_name' => $newUser['middle_name'],
            'last_name' => $newUser['last_name'],
            'name' => trim($newUser['first_name'] . ' ' . $newUser['middle_name'] . ' ' . $newUser['last_name']), // For backward compatibility
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
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            createDatabaseNotification('success', 'Welcome to BarangayLink!', 
                'Your account has been created successfully. Welcome to our digital governance platform.', 
                $email);
        } else {
            createNotification('success', 'Welcome to BarangayLink!', 
                'Your account has been created successfully. Welcome to our digital governance platform.', 
                $email);
        }
        
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
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            $user = getUserByEmail($email);
            if ($user) {
                // Remove password from profile data
                unset($user['password']);
                return $user;
            }
            return null;
        } else {
            $users = loadJsonData('users');
            
            foreach ($users as $user) {
                if ($user['email'] === $email) {
                    // Remove password from profile data
                    unset($user['password']);
                    return $user;
                }
            }
            
            return null;
        }
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
        // Allowed fields for update
        $allowedFields = ['name', 'address', 'phone'];
        $cleanUpdates = [];
        
        foreach ($allowedFields as $field) {
            if (isset($updates[$field])) {
                $cleanUpdates[$field] = trim($updates[$field]);
            }
        }
        
        if (empty($cleanUpdates)) {
            return false;
        }
        
        $updated = false;
        
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            $updated = updateUser($email, $cleanUpdates);
        } else {
            $users = loadJsonData('users');
            
            foreach ($users as &$user) {
                if ($user['email'] === $email) {
                    foreach ($cleanUpdates as $field => $value) {
                        $user[$field] = $value;
                    }
                    $user['updatedAt'] = date('c');
                    $updated = true;
                    break;
                }
            }
            
            if ($updated) {
                $updated = saveJsonData('users', $users);
            }
        }
        
        if ($updated) {
            // Update session data
            $currentUser = getCurrentUser();
            if ($currentUser && $currentUser['email'] === $email) {
                $currentUser = array_merge($currentUser, $cleanUpdates);
                $_SESSION['auth']['user'] = $currentUser;
                $_SESSION['user'] = $currentUser; // Legacy compatibility
            }
        }
        
        return $updated;
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