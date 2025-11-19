<?php
/**
 * Concerns API Endpoint
 * SYNCHRONIZED with AdminConcernManagement.tsx
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set JSON header first
header('Content-Type: application/json');

// Error handler to ensure we always return JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error: $errstr in $errfile on line $errline");
    echo json_encode(['success' => false, 'error' => 'Server error occurred']);
    exit;
});

try {
    require_once '../config.php';
    require_once '../db.php';
    require_once '../init.php';
} catch (Exception $e) {
    error_log("Include error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Configuration error: ' . $e->getMessage()]);
    exit;
}

// Log the request for debugging
error_log("Concerns API called - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    error_log("Action: $action");
    
    switch ($action) {
        case 'create':
            createConcern();
            break;
            
        case 'delete':
            deleteConcern();
            break;
            
        case 'update':
        case 'update_status':
            updateConcernStatus();
            break;
            
        case 'respond':
            respondToConcern();
            break;
            
        default:
            error_log("Invalid action: $action");
            echo json_encode(['success' => false, 'error' => 'Invalid action: ' . $action]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    listConcerns();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

function createConcern() {
    try {
        $userId = $_POST['user_id'] ?? 0;
        $category = trim($_POST['category'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        
        error_log("Creating concern - User: $userId, Category: $category, Subject: $subject");
        
        // Validation
        if (!$userId || empty($userId)) {
            error_log("Validation failed: Missing user_id");
            echo json_encode(['success' => false, 'error' => 'Missing user ID']);
            exit;
        }
        
        if (empty($category)) {
            error_log("Validation failed: Missing category");
            echo json_encode(['success' => false, 'error' => 'Please select a category']);
            exit;
        }
        
        if (empty($subject)) {
            error_log("Validation failed: Missing subject");
            echo json_encode(['success' => false, 'error' => 'Please enter a subject']);
            exit;
        }
        
        if (empty($description)) {
            error_log("Validation failed: Missing description");
            echo json_encode(['success' => false, 'error' => 'Please enter a description']);
            exit;
        }
        
        // Connect to database
        $db = getDB();

        // Prepare statement
        $stmt = $db->prepare("INSERT INTO concerns (user_id, category, subject, description, location, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', datetime('now'))");
        $stmt->execute([$userId, $category, $subject, $description, $location]);

        if ($stmt->rowCount() > 0) {
            $insertId = $db->lastInsertId();
            error_log("Concern created successfully with ID: $insertId");

            // Create notifications for all admins
            try {
                // Get the user's full name
                $userStmt = $db->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                $userStmt->execute([$userId]);
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                $fullName = $user['first_name'] . ' ' . $user['last_name'];

                $adminStmt = $db->prepare("SELECT id FROM users WHERE role = 'admin'");
                $adminStmt->execute();
                $admins = $adminStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($admins as $admin) {
                    $notifStmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, related_type, related_id, created_at) VALUES (?, 'info', 'Concern Submitted', ?, 'concern', ?, NOW())");
                    $notifStmt->execute([$admin['id'], "$fullName has submitted a concern.", $insertId]);
                }
                error_log("Notifications created for " . count($admins) . " admins");
            } catch (Exception $e) {
                error_log("Failed to create admin notifications: " . $e->getMessage());
            }

            echo json_encode(['success' => true, 'id' => $insertId]);
        } else {
            error_log("Execute failed");
            echo json_encode(['success' => false, 'error' => 'Failed to create concern']);
        }
    } catch (Exception $e) {
        error_log("Exception in createConcern: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Exception: ' . $e->getMessage()]);
    }
    exit;
}

function deleteConcern() {
    try {
        $id = $_POST['id'] ?? 0;
        
        error_log("Deleting concern ID: $id");
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Missing concern ID']);
            exit;
        }
        
        $db = getDB();

        // Only allow deletion of pending concerns
        $stmt = $db->prepare("DELETE FROM concerns WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            error_log("Concern deleted successfully");
            echo json_encode(['success' => true]);
        } else {
            error_log("Concern not found or not pending");
            echo json_encode(['success' => false, 'error' => 'Concern not found or cannot be deleted']);
        }
    } catch (Exception $e) {
        error_log("Exception in deleteConcern: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function updateConcernStatus() {
    try {
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        $response = $_POST['response'] ?? '';
        
        error_log("Updating concern ID: $id to status: $status");
        
        if (!$id || !$status) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        // Status is already in correct format (with dashes)
        $dbStatus = $status;
        error_log("Using status: '$dbStatus'");

        $db = getDB();
        $stmt = $db->prepare("UPDATE concerns SET status = ?, admin_response = ?, updated_at = datetime('now') WHERE id = ?");
        $stmt->execute([$dbStatus, $response, $id]);

        if ($stmt->rowCount() > 0) {
            error_log("Concern status updated successfully");
            echo json_encode(['success' => true]);
        } else {
            error_log("Concern not found");
            echo json_encode(['success' => false, 'error' => 'Concern not found']);
        }
    } catch (Exception $e) {
        error_log("Exception in updateConcernStatus: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function respondToConcern() {
    try {
        $id = $_POST['concern_id'] ?? $_POST['id'] ?? 0;
        $response = $_POST['response'] ?? '';
        $status = $_POST['status'] ?? '';

        error_log("Responding to concern ID: $id with response: $response, status: $status");

        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Missing concern ID']);
            exit;
        }

        if (empty($response)) {
            echo json_encode(['success' => false, 'error' => 'Please enter a response']);
            exit;
        }

        $db = getDB();

        // Status is already in correct format (with dashes)
        $dbStatus = $status;

        // Update both response and status if status is provided
        if (!empty($status)) {
            $stmt = $db->prepare("UPDATE concerns SET status = ?, admin_response = ?, updated_at = datetime('now') WHERE id = ?");
            $stmt->execute([$dbStatus, $response, $id]);
        } else {
            $stmt = $db->prepare("UPDATE concerns SET admin_response = ?, updated_at = datetime('now') WHERE id = ?");
            $stmt->execute([$response, $id]);
        }

        if ($stmt->rowCount() > 0) {
            error_log("Concern response submitted successfully");
            echo json_encode(['success' => true, 'message' => 'Response submitted successfully']);
        } else {
            error_log("Concern not found");
            echo json_encode(['success' => false, 'error' => 'Concern not found']);
        }
    } catch (Exception $e) {
        error_log("Exception in respondToConcern: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function listConcerns() {
    try {
        $userEmail = $_GET['userEmail'] ?? '';

        if ($userEmail) {
            // Get concerns for specific user with user info
            $concerns = fetchAll("SELECT c.*, u.first_name, u.last_name, u.email, (u.first_name || ' ' || u.last_name) as submittedBy FROM concerns c JOIN users u ON c.user_id = u.id WHERE u.email = ? ORDER BY c.created_at DESC", [$userEmail]);
        } else {
            // Get all concerns with user info
            $concerns = fetchAll("SELECT c.*, u.first_name, u.last_name, u.email, (u.first_name || ' ' || u.last_name) as submittedBy, u.email as submittedByEmail FROM concerns c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC");
        }

        echo json_encode(['success' => true, 'data' => $concerns]);
    } catch (Exception $e) {
        error_log("Exception in listConcerns: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?>
