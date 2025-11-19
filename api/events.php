<?php
/**
 * Events API Endpoint
 */

require_once '../init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $events = fetchAll("SELECT * FROM events WHERE event_date >= date('now') ORDER BY event_date ASC");
    echo json_encode($events);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $eventDate = $_POST['event_date'] ?? '';
    
    if (!$title || !$description || !$location || !$eventDate) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields required']);
        exit;
    }
    
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO events (title, description, location, event_date, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
    $stmt->execute([$title, $description, $location, $eventDate]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create event']);
    }
    exit;
}
?>
