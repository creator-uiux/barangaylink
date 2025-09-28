<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

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

$userId = $_SESSION['user_id'];
$fullName = sanitizeInput($input['full_name'] ?? '');
$email = sanitizeInput($input['email'] ?? '');
$address = sanitizeInput($input['address'] ?? '');
$phone = sanitizeInput($input['phone'] ?? '');
$birthDate = sanitizeInput($input['birth_date'] ?? '');
$currentPassword = $input['current_password'] ?? '';
$newPassword = $input['new_password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

// Validate required fields
if (empty($fullName) || empty($email)) {
    errorResponse('Full name and email are required');
}

if (!validateEmail($email)) {
    errorResponse('Invalid email format');
}

// Validate password change if requested
if (!empty($newPassword)) {
    if (empty($currentPassword)) {
        errorResponse('Current password is required to change password');
    }
    
    if (strlen($newPassword) < 6) {
        errorResponse('New password must be at least 6 characters long');
    }
    
    if ($newPassword !== $confirmPassword) {
        errorResponse('New passwords do not match');
    }
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Get current user data
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $currentUser = $stmt->fetch();
    
    if (!$currentUser) {
        errorResponse('User not found');
    }
    
    // Check if email is already taken by another user
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        errorResponse('Email already taken by another user');
    }
    
    // Verify current password if changing password
    if (!empty($newPassword)) {
        if (!verifyPassword($currentPassword, $currentUser['password'])) {
            errorResponse('Current password is incorrect');
        }
    }
    
    // Update user data
    $updateFields = [];
    $updateValues = [];
    
    if ($fullName !== $currentUser['full_name']) {
        $updateFields[] = 'full_name = ?';
        $updateValues[] = $fullName;
    }
    
    if ($email !== $currentUser['email']) {
        $updateFields[] = 'email = ?';
        $updateValues[] = $email;
    }
    
    if ($address !== ($currentUser['address'] ?? '')) {
        $updateFields[] = 'address = ?';
        $updateValues[] = $address ?: null;
    }
    
    if ($phone !== ($currentUser['phone'] ?? '')) {
        $updateFields[] = 'phone = ?';
        $updateValues[] = $phone ?: null;
    }
    
    if ($birthDate !== ($currentUser['birth_date'] ?? '')) {
        $updateFields[] = 'birth_date = ?';
        $updateValues[] = $birthDate ?: null;
    }
    
    if (!empty($newPassword)) {
        $updateFields[] = 'password = ?';
        $updateValues[] = hashPassword($newPassword);
    }
    
    if (empty($updateFields)) {
        successResponse('No changes to update');
    }
    
    // Add updated_at timestamp
    $updateFields[] = 'updated_at = NOW()';
    $updateValues[] = $userId;
    
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($updateValues);
    
    // Update session data if name or email changed
    if ($fullName !== $currentUser['full_name']) {
        $_SESSION['user_name'] = $fullName;
    }
    
    if ($email !== $currentUser['email']) {
        $_SESSION['user_email'] = $email;
    }
    
    // Log activity
    $changes = [];
    if ($fullName !== $currentUser['full_name']) $changes[] = 'name';
    if ($email !== $currentUser['email']) $changes[] = 'email';
    if ($address !== ($currentUser['address'] ?? '')) $changes[] = 'address';
    if ($phone !== ($currentUser['phone'] ?? '')) $changes[] = 'phone';
    if ($birthDate !== ($currentUser['birth_date'] ?? '')) $changes[] = 'birth date';
    if (!empty($newPassword)) $changes[] = 'password';
    
    logActivity($userId, 'profile_update', 'Updated: ' . implode(', ', $changes));
    
    successResponse('Profile updated successfully');
    
} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    errorResponse('Failed to update profile. Please try again.');
}
?>