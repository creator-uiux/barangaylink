<?php
/**
 * Announcements API Endpoint
 */

require_once '../init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $announcements = fetchAll("SELECT * FROM announcements ORDER BY created_at DESC");
    echo json_encode($announcements);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (!$title || !$content) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and content required']);
        exit;
    }
    
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO announcements (title, content, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$title, $content]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create announcement']);
    }
    exit;
}
?>
