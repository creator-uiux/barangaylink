<?php
/**
 * Authentication API Handler
 * Handles login, signup, and logout requests
 * SYNCHRONIZED with App.tsx authentication logic
 */

require_once '../init.php';

header('Content-Type: application/json');

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Handle POST requests
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'login':
            handleLogin($data);
            break;
        case 'signup':
            handleSignup($data);
            break;
        case 'logout':
            handleLogout();
            break;
        case 'reset-password':
            handlePasswordReset($data);
            break;
        default:
            jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

/**
 * Handle user login
 * EXACT MATCH with handleLogin in App.tsx
 */
function handleLogin($data) {
    $email = sanitizeInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'message' => 'Email and password are required'], 400);
    }
    
    // Check admin credentials (SAME as App.tsx)
    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        $user = [
            'email' => ADMIN_EMAIL,
            'name' => 'Admin User',
            'firstName' => 'Admin',
            'lastName' => 'User',
            'role' => 'admin'
        ];
        
        loginUser($user);
        jsonResponse([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'redirect' => 'admin-dashboard'
        ]);
    }
    
    // Check regular user credentials
    try {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1 LIMIT 1";
        $user = fetchOne($sql, [':email' => $email]);
        
        if ($user && $user['password'] === $password) {
            $userData = [
                'id' => $user['id'],
                'email' => $user['email'],
                'firstName' => $user['first_name'],
                'middleName' => $user['middle_name'],
                'lastName' => $user['last_name'],
                'name' => trim($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']),
                'role' => $user['role'],
                'address' => $user['address'],
                'phone' => $user['phone']
            ];
            
            loginUser($userData);
            
            // Log activity
            logActivity($user['id'], 'login', 'user', $user['id'], 'User logged in');
            
            jsonResponse([
                'success' => true,
                'message' => 'Login successful',
                'user' => $userData,
                'redirect' => 'dashboard'
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Invalid email or password'], 401);
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Login failed. Please try again.'], 500);
    }
}

/**
 * Handle user signup
 * EXACT MATCH with handleSignup in App.tsx
 */
function handleSignup($data) {
    $firstName = sanitizeInput($data['firstName'] ?? '');
    $middleName = sanitizeInput($data['middleName'] ?? '');
    $lastName = sanitizeInput($data['lastName'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $address = sanitizeInput($data['address'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'message' => 'First name, last name, email, and password are required'], 400);
    }
    
    if (!validateEmail($email)) {
        jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
    }
    
    try {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $existing = fetchOne($sql, [':email' => $email]);
        
        if ($existing) {
            jsonResponse(['success' => false, 'message' => 'Email already exists'], 409);
        }
        
        // Insert new user
        $sql = "INSERT INTO users (email, password, first_name, middle_name, last_name, role, address, phone, status, created_at) 
                VALUES (:email, :password, :first_name, :middle_name, :last_name, 'resident', :address, :phone, 'active', NOW())";
        
        executeQuery($sql, [
            ':email' => $email,
            ':password' => $password,
            ':first_name' => $firstName,
            ':middle_name' => $middleName,
            ':last_name' => $lastName,
            ':address' => $address,
            ':phone' => $phone
        ]);
        
        $userId = getLastInsertId();
        
        // Create welcome notification
        createNotification(
            $email,
            'success',
            'Welcome to BarangayLink!',
            'Your account has been created successfully. Welcome to our digital governance platform.'
        );
        
        // Prepare user data
        $userData = [
            'id' => $userId,
            'email' => $email,
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName,
            'name' => trim($firstName . ' ' . $middleName . ' ' . $lastName),
            'role' => 'resident',
            'address' => $address,
            'phone' => $phone
        ];
        
        // Login user
        loginUser($userData);
        
        // Log activity
        logActivity($userId, 'signup', 'user', $userId, 'New user registered');
        
        jsonResponse([
            'success' => true,
            'message' => 'Account created successfully',
            'user' => $userData,
            'redirect' => 'dashboard'
        ]);
    } catch (Exception $e) {
        error_log("Signup error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Signup failed. Please try again.'], 500);
    }
}

/**
 * Handle user logout
 */
function handleLogout() {
    $user = getCurrentUser();
    if ($user && isset($user['id'])) {
        logActivity($user['id'], 'logout', 'user', $user['id'], 'User logged out');
    }
    
    logoutUser();
    jsonResponse(['success' => true, 'message' => 'Logout successful']);
}

/**
 * Handle password reset
 * EXACT MATCH with handlePasswordReset in App.tsx
 */
function handlePasswordReset($data) {
    $email = sanitizeInput($data['email'] ?? '');
    
    if (empty($email)) {
        jsonResponse(['success' => false, 'message' => 'Email is required'], 400);
    }
    
    if (!validateEmail($email)) {
        jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
    }
    
    // In real application, send actual password reset email
    // For demo, just return success
    jsonResponse([
        'success' => true,
        'message' => "Password reset link sent to $email"
    ]);
}
