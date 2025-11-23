<?php
/**
 * BarangayLink - Main Entry Point
 * FULLY SYNCHRONIZED with App.tsx
 * 
 * This file handles routing, authentication, and view rendering
 * to match the exact functionality of the React application
 */

// Configure session BEFORE starting it
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
session_name('BARANGAYLINK_SESSION');

// Start session
session_start();

// Include configuration and database
require_once 'config.php';
require_once 'db.php';
require_once 'init.php';

// Handle POST requests (authentication actions)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'login':
            handleLogin();
            break;
            
        case 'signup':
            handleSignup();
            break;
            
        case 'logout':
            handleLogout();
            break;
            
        case 'reset_password':
            handlePasswordReset();
            break;
            
        default:
            // Invalid action
            header('Location: index.php');
            exit;
    }
}

// Handle GET requests (view routing)
$view = $_GET['view'] ?? 'landing';
$user = getCurrentUser();

// Determine which page to render
if (!isAuthenticated()) {
    // Not logged in - show landing page
    include 'views/landing.php';
    include 'views/partials/auth-modal.php';
} elseif ($user && $user['role'] === 'admin') {
    // Admin user - show admin dashboard
    $view = $_GET['view'] ?? 'admin-dashboard';
    include 'views/admin/layout.php';
} else {
    // Regular user - show user dashboard
    $view = $_GET['view'] ?? 'dashboard';
    include 'views/user/layout.php';
}

/**
 * Authentication Handler Functions
 */

function handleLogin() {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all required fields';
        header('Location: index.php');
        exit;
    }
    
    // Check for admin login
    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        $_SESSION['auth'] = [
            'isAuthenticated' => true,
            'user' => [
                'email' => ADMIN_EMAIL,
                'name' => 'Admin User',
                'role' => 'admin'
            ]
        ];
        header('Location: index.php?view=admin-dashboard');
        exit;
    }
    
    // Check for regular user login
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['auth'] = [
            'isAuthenticated' => true,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'firstName' => $user['first_name'],
                'middleName' => $user['middle_name'],
                'lastName' => $user['last_name'],
                'name' => trim($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']),
                'role' => $user['role'],
                'address' => $user['address'],
                'phone' => $user['phone']
            ]
        ];
        header('Location: index.php?view=dashboard');
        exit;
    }
    
    $_SESSION['error'] = 'Invalid email or password';
    header('Location: index.php');
    exit;
}

function handleSignup() {
    $firstName = trim($_POST['first_name'] ?? '');
    $middleName = trim($_POST['middle_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($address) || empty($phone)) {
        $_SESSION['error'] = 'Please fill in all required fields';
        header('Location: index.php');
        exit;
    }
    
    // Validate names (letters only)
    if (!preg_match('/^[a-zA-Z\s\-\']+$/', $firstName) || 
        (!empty($middleName) && !preg_match('/^[a-zA-Z\s\-\']+$/', $middleName)) ||
        !preg_match('/^[a-zA-Z\s\-\']+$/', $lastName)) {
        $_SESSION['error'] = 'Names should only contain letters';
        header('Location: index.php');
        exit;
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please use a valid email address';
        header('Location: index.php');
        exit;
    }
    
    // Validate password length
    if (strlen($password) < 8) {
        $_SESSION['error'] = 'Password must be at least 8 characters long';
        header('Location: index.php');
        exit;
    }
    
    // Validate address length
    if (strlen($address) < 10) {
        $_SESSION['error'] = 'Please provide a complete address (at least 10 characters)';
        header('Location: index.php');
        exit;
    }
    
    // Check if user already exists
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $_SESSION['error'] = 'User with this email already exists';
        header('Location: index.php');
        exit;
    }

    // Create new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, address, phone, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'resident', NOW())");
    $stmt->execute([$firstName, $middleName, $lastName, $email, $hashedPassword, $address, $phone]);

    if ($stmt->rowCount() > 0) {
        $userId = $db->lastInsertId();

        // Create welcome notification
        $notificationStmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, created_at) VALUES (?, 'success', 'Welcome to BarangayLink!', 'Your account has been created successfully. Welcome to our digital governance platform.', NOW())");
        $notificationStmt->execute([$userId]);

        // Set success message for popup and redirect to landing page for sign-in
        $_SESSION['signup_success_popup'] = 'You have been registered successfully, Login now!';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error'] = 'An error occurred during registration';
        header('Location: index.php');
        exit;
    }
}

function handleLogout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

function handlePasswordReset() {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $_SESSION['error'] = 'Please enter your email address';
        header('Location: index.php');
        exit;
    }
    
    // In a real application, send password reset email
    // For now, just show success message
    $_SESSION['success'] = "Password reset link sent to {$email}";
    header('Location: index.php');
    exit;
}
?>