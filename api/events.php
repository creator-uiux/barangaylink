<?php
/**
 * Events API Endpoint
 */

require_once '../init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = getDBConnection();
    $events = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC")->fetch_all(MYSQLI_ASSOC);
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
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO events (title, description, location, event_date, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $title, $description, $location, $eventDate);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create event']);
    }
    exit;
}
?>
