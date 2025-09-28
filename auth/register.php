<?php
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    errorResponse('Invalid JSON input');
}

// Validate CSRF token
if (!isset($input['csrf_token']) || !verifyCSRFToken($input['csrf_token'])) {
    errorResponse('Invalid CSRF token', 403);
}

// Validate input
$email = sanitizeInput($input['email'] ?? '');
$password = $input['password'] ?? '';
$fullName = sanitizeInput($input['full_name'] ?? '');
$address = sanitizeInput($input['address'] ?? '');
$phone = sanitizeInput($input['phone'] ?? '');

if (empty($email) || empty($password) || empty($fullName)) {
    errorResponse('Email, password, and full name are required');
}

if (!validateEmail($email)) {
    errorResponse('Invalid email format');
}

if (strlen($password) < 6) {
    errorResponse('Password must be at least 6 characters long');
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        errorResponse('Email already registered');
    }
    
    // Create user
    $hashedPassword = hashPassword($password);
    $stmt = $db->prepare("INSERT INTO users (email, password, full_name, address, phone, role) VALUES (?, ?, ?, ?, ?, 'user')");
    $stmt->execute([$email, $hashedPassword, $fullName, $address, $phone]);
    
    $userId = $db->lastInsertId();
    
    // Create session
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_role'] = 'user';
    $_SESSION['user_name'] = $fullName;
    $_SESSION['user_email'] = $email;
    $_SESSION['login_time'] = time();
    
    // Log activity
    logActivity($userId, 'register', 'User registered');
    
    successResponse('Registration successful', [
        'user' => [
            'id' => $userId,
            'email' => $email,
            'full_name' => $fullName,
            'role' => 'user'
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    errorResponse('Registration failed. Please try again.');
}
?>