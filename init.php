<?php
/**
 * Initialization File
 * Sets up session, loads configuration, and initializes the application
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once 'config.php';
require_once 'db.php';

// Initialize session variables if not set
if (!isset($_SESSION['initialized'])) {
    $_SESSION['initialized'] = true;
    $_SESSION['auth'] = [
        'isAuthenticated' => false,
        'user' => null
    ];
}

// Helper function to check if user is authenticated
function isAuthenticated() {
    return isset($_SESSION['auth']['isAuthenticated']) && $_SESSION['auth']['isAuthenticated'] === true;
}

// Helper function to get current user
function getCurrentUser() {
    return isAuthenticated() ? $_SESSION['auth']['user'] : null;
}

// Helper function to check if user is admin
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

// Helper function to require authentication
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: index.php');
        exit;
    }
}

// Helper function to require admin
function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

// Helper function to login user
function loginUser($user) {
    $_SESSION['auth'] = [
        'isAuthenticated' => true,
        'user' => $user
    ];
    return true;
}

// Helper function to logout user
function logoutUser() {
    $_SESSION['auth'] = [
        'isAuthenticated' => false,
        'user' => null
    ];
    session_destroy();
    return true;
}

// Helper function to sanitize input
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Helper function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Helper function to send JSON response
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Helper function to generate random string
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

// Helper function to format date
function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

// Helper function to format date time
function formatDateTime($datetime, $format = 'F j, Y g:i A') {
    return date($format, strtotime($datetime));
}

// Helper function to get time ago
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return $diff . ' second' . ($diff != 1 ? 's' : '') . ' ago';
    }
    
    $diff = round($diff / 60);
    if ($diff < 60) {
        return $diff . ' minute' . ($diff != 1 ? 's' : '') . ' ago';
    }
    
    $diff = round($diff / 60);
    if ($diff < 24) {
        return $diff . ' hour' . ($diff != 1 ? 's' : '') . ' ago';
    }
    
    $diff = round($diff / 24);
    if ($diff < 7) {
        return $diff . ' day' . ($diff != 1 ? 's' : '') . ' ago';
    }
    
    $diff = round($diff / 7);
    if ($diff < 4) {
        return $diff . ' week' . ($diff != 1 ? 's' : '') . ' ago';
    }
    
    return formatDate($datetime);
}

// Helper function to create notification
function createNotification($userEmail, $type, $title, $message) {
    try {
        $sql = "INSERT INTO notifications (user_email, type, title, message, is_read, created_at) 
                VALUES (:user_email, :type, :title, :message, 0, NOW())";
        executeQuery($sql, [
            ':user_email' => $userEmail,
            ':type' => $type,
            ':title' => $title,
            ':message' => $message
        ]);
        return true;
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

// Helper function to log activity
function logActivity($userId, $action, $entityType = null, $entityId = null, $description = null) {
    try {
        $sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent, created_at) 
                VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip_address, :user_agent, NOW())";
        executeQuery($sql, [
            ':user_id' => $userId,
            ':action' => $action,
            ':entity_type' => $entityType,
            ':entity_id' => $entityId,
            ':description' => $description,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        return true;
    } catch (Exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
        return false;
    }
}

// CSRF Protection
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Generate CSRF token on initialization
generateCSRFToken();
