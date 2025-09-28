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
$title = sanitizeInput($input['title'] ?? '');
$description = sanitizeInput($input['description'] ?? '');
$category = sanitizeInput($input['category'] ?? '');
$priority = sanitizeInput($input['priority'] ?? 'medium');

// Validate required fields
if (empty($title) || empty($description) || empty($category)) {
    errorResponse('Title, description, and category are required');
}

// Validate category
$validCategories = ['infrastructure', 'security', 'health', 'environment', 'other'];
if (!in_array($category, $validCategories)) {
    errorResponse('Invalid category');
}

// Validate priority
$validPriorities = ['low', 'medium', 'high', 'urgent'];
if (!in_array($priority, $validPriorities)) {
    errorResponse('Invalid priority level');
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Insert concern
    $stmt = $db->prepare("
        INSERT INTO concerns (user_id, title, description, category, priority, status) 
        VALUES (?, ?, ?, ?, ?, 'submitted')
    ");
    
    $stmt->execute([
        $userId,
        $title,
        $description,
        $category,
        $priority
    ]);
    
    $concernId = $db->lastInsertId();
    
    // Log activity
    logActivity($userId, 'concern_submitted', "Submitted concern: {$title}");
    
    // Get the created concern for response
    $stmt = $db->prepare("SELECT * FROM concerns WHERE id = ?");
    $stmt->execute([$concernId]);
    $concern = $stmt->fetch();
    
    successResponse('Concern submitted successfully', [
        'concern' => $concern
    ]);
    
} catch (Exception $e) {
    error_log("Concern submission error: " . $e->getMessage());
    errorResponse('Failed to submit concern. Please try again.');
}
?>