<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

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
$content = sanitizeInput($input['content'] ?? '');
$type = sanitizeInput($input['type'] ?? '');
$priority = sanitizeInput($input['priority'] ?? 'medium');
$status = sanitizeInput($input['status'] ?? 'draft');

// Validate required fields
if (empty($title) || empty($content) || empty($type)) {
    errorResponse('Title, content, and type are required');
}

// Validate type
$validTypes = ['general', 'event', 'emergency', 'maintenance'];
if (!in_array($type, $validTypes)) {
    errorResponse('Invalid announcement type');
}

// Validate priority
$validPriorities = ['low', 'medium', 'high'];
if (!in_array($priority, $validPriorities)) {
    errorResponse('Invalid priority level');
}

// Validate status
$validStatuses = ['draft', 'published'];
if (!in_array($status, $validStatuses)) {
    errorResponse('Invalid status');
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Insert announcement
    $publishedAt = ($status === 'published') ? 'NOW()' : 'NULL';
    
    $stmt = $db->prepare("
        INSERT INTO announcements (title, content, type, priority, status, created_by, published_at) 
        VALUES (?, ?, ?, ?, ?, ?, $publishedAt)
    ");
    
    $stmt->execute([
        $title,
        $content,
        $type,
        $priority,
        $status,
        $userId
    ]);
    
    $announcementId = $db->lastInsertId();
    
    // Log activity
    $action = ($status === 'published') ? 'published announcement' : 'created announcement draft';
    logActivity($userId, 'announcement_create', "$action: {$title}");
    
    // Get the created announcement for response
    $stmt = $db->prepare("SELECT * FROM announcements WHERE id = ?");
    $stmt->execute([$announcementId]);
    $announcement = $stmt->fetch();
    
    successResponse('Announcement created successfully', [
        'announcement' => $announcement
    ]);
    
} catch (Exception $e) {
    error_log("Announcement creation error: " . $e->getMessage());
    errorResponse('Failed to create announcement. Please try again.');
}
?>