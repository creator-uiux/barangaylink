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
    
    try {
        $limit = (int)($_GET['limit'] ?? 50);
        $sql = "SELECT * FROM notifications 
                WHERE user_email = :email 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $stmt = getDB()->prepare($sql);
        $stmt->bindValue(':email', $user['email'], PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $notifications = $stmt->fetchAll();
        
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
                WHERE user_email = :email AND is_read = 0";
        $result = fetchOne($sql, [':email' => $user['email']]);
        
        jsonResponse(['success' => true, 'count' => (int)$result['count']]);
    } catch (Exception $e) {
        error_log("Get unread count error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch unread count'], 500);
    }
}

function markAsRead() {
    requireAuth();
    $user = getCurrentUser();
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = (int)($data['id'] ?? 0);
    
    if (empty($id)) {
        jsonResponse(['success' => false, 'message' => 'Notification ID is required'], 400);
    }
    
    try {
        $sql = "UPDATE notifications 
                SET is_read = 1 
                WHERE id = :id AND user_email = :email";
        
        executeQuery($sql, [':id' => $id, ':email' => $user['email']]);
        
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
                WHERE user_email = :email AND is_read = 0";
        
        executeQuery($sql, [':email' => $user['email']]);
        
        jsonResponse(['success' => true, 'message' => 'All notifications marked as read']);
    } catch (Exception $e) {
        error_log("Mark all as read error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to mark all notifications as read'], 500);
    }
}
