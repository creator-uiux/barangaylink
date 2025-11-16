<?php
/**
 * Announcements API Endpoint
 */

require_once '../init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = getDBConnection();
    $announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
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
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO announcements (title, content, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $title, $content);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create announcement']);
    }
    exit;
}
?>
