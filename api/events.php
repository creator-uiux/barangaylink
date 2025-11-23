<?php
/**
 * Events API Endpoint
 */

require_once '../init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $events = fetchAll("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC");
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
    $stmt = $db->prepare("INSERT INTO events (title, description, location, event_date, event_time, end_date, end_time, category, max_participants, is_active, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$title, $description, $location, $eventDate, $eventTime ?? null, $endDate ?? null, $endTime ?? null, $category ?? 'Community', $maxParticipants ?? null, $isActive ?? 1, getCurrentUser()['id']]);

    if ($stmt->rowCount() > 0) {
        $insertId = $db->lastInsertId();

        // Create broadcast notification for all users
        try {
            createBroadcastNotification('info', 'New Event', $title);
            error_log("Broadcast notification created for new event");
        } catch (Exception $e) {
            error_log("Failed to create broadcast notification: " . $e->getMessage());
        }

        echo json_encode(['success' => true, 'id' => $insertId]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create event']);
    }
    exit;
}
?>
