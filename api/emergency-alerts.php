<?php
/**
 * Emergency Alerts API Endpoint
 */

require_once '../init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = getDBConnection();
    $alerts = $conn->query("SELECT * FROM emergency_alerts ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
    echo json_encode($alerts);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $title = $_POST['title'] ?? '';
    $message = $_POST['message'] ?? '';
    $severity = $_POST['severity'] ?? 'medium';
    $isActive = $_POST['is_active'] ?? 1;
    
    if (!$title || !$message) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and message required']);
        exit;
    }
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO emergency_alerts (title, message, severity, is_active, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssi", $title, $message, $severity, $isActive);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create alert']);
    }
    exit;
}
?>
