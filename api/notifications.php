<?php
/**
 * Notifications API Handler
 */

require_once '../init.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'list') {
            getNotifications();
        } elseif ($action === 'unread-count') {
            getUnreadCount();
        } else {
            jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
        }
        break;
    case 'POST':
        if ($action === 'mark-read') {
            markAsRead();
        } elseif ($action === 'mark-all-read') {
            markAllAsRead();
        } elseif ($action === 'create') {
            handleCreateNotification();
        } else {
            jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
        }
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

function getNotifications() {
    requireAuth();
    $user = getCurrentUser();

    // Allow fetching notifications for a specific user (for admin viewing)
    $userEmail = $_GET['user_email'] ?? null;
    $targetUserId = $user['id'];

    if ($userEmail) {
        // If user_email is provided, find that user's notifications (admin only)
        if ($user['role'] !== 'admin') {
            jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $userStmt = getDB()->prepare("SELECT id FROM users WHERE email = ?");
        $userStmt->execute([$userEmail]);
        $targetUser = $userStmt->fetch(PDO::FETCH_ASSOC);
        if (!$targetUser) {
            jsonResponse(['success' => false, 'message' => 'User not found'], 404);
        }
        $targetUserId = $targetUser['id'];
    }

    try {
        $limit = (int)($_GET['limit'] ?? 50);
        $sql = "SELECT id, user_id, type, title, message, is_read, related_type, related_id, action_url, created_at FROM notifications
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = getDB()->prepare($sql);
        $stmt->bindValue(':user_id', $targetUserId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert is_read to boolean for frontend compatibility
        foreach ($notifications as &$notification) {
            $notification['read'] = (bool)$notification['is_read'];
            unset($notification['is_read']);
        }

        jsonResponse(['success' => true, 'notifications' => $notifications]);
    } catch (Exception $e) {
        error_log("Get notifications error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch notifications'], 500);
    }
}

function getUnreadCount() {
    requireAuth();
    $user = getCurrentUser();

    try {
        $sql = "SELECT COUNT(*) as count FROM notifications
                WHERE user_id = :user_id AND is_read = 0";
        $result = fetchOne($sql, [':user_id' => $user['id']]);

        jsonResponse(['success' => true, 'count' => (int)$result['count']]);
    } catch (Exception $e) {
        error_log("Get unread count error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch unread count'], 500);
    }
}

function markAsRead() {
    requireAuth();
    $user = getCurrentUser();

    // Check if it's a POST request with form data or JSON
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['notification_id'])) {
            // Form data from user layout
            $id = (int)($_POST['notification_id'] ?? 0);
        } else {
            // JSON data
            $data = json_decode(file_get_contents('php://input'), true);
            $id = (int)($data['id'] ?? 0);
        }
    } else {
        jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
    }

    if (empty($id)) {
        jsonResponse(['success' => false, 'message' => 'Notification ID is required'], 400);
    }

    try {
        $sql = "UPDATE notifications
                SET is_read = 1
                WHERE id = :id AND user_id = :user_id";

        executeQuery($sql, [':id' => $id, ':user_id' => $user['id']]);

        jsonResponse(['success' => true, 'message' => 'Notification marked as read']);
    } catch (Exception $e) {
        error_log("Mark as read error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to mark notification as read'], 500);
    }
}

function markAllAsRead() {
    requireAuth();
    $user = getCurrentUser();

    try {
        $sql = "UPDATE notifications
                SET is_read = 1
                WHERE user_id = :user_id AND is_read = 0";

        executeQuery($sql, [':user_id' => $user['id']]);

        jsonResponse(['success' => true, 'message' => 'All notifications marked as read']);
    } catch (Exception $e) {
        error_log("Mark all as read error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to mark all notifications as read'], 500);
    }
}

function handleCreateNotification() {
    $data = json_decode(file_get_contents('php://input'), true);

    $userId = (int)($data['user_id'] ?? 0);
    $type = $data['type'] ?? 'info';
    $title = trim($data['title'] ?? '');
    $message = trim($data['message'] ?? '');
    $relatedType = $data['related_type'] ?? null;
    $relatedId = $data['related_id'] ?? null;
    $actionUrl = $data['action_url'] ?? null;

    if (!$userId || empty($title) || empty($message)) {
        jsonResponse(['success' => false, 'message' => 'Missing required fields'], 400);
    }

    try {
        $sql = "INSERT INTO notifications (user_id, type, title, message, related_type, related_id, action_url, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        executeQuery($sql, [$userId, $type, $title, $message, $relatedType, $relatedId, $actionUrl]);

        jsonResponse(['success' => true, 'message' => 'Notification created successfully']);
    } catch (Exception $e) {
        error_log("Create notification error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to create notification'], 500);
    }
}
