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
        $conn = getDBConnection();
        
        if (!$conn) {
            error_log("Database connection failed");
            echo json_encode(['success' => false, 'error' => 'Database connection failed']);
            exit;
        }
        
        // Prepare statement
        $stmt = $conn->prepare("INSERT INTO concerns (user_id, category, subject, description, location, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Database prepare error: ' . $conn->error]);
            exit;
        }
        
        $stmt->bind_param("issss", $userId, $category, $subject, $description, $location);
        
        if ($stmt->execute()) {
            $insertId = $conn->insert_id;
            error_log("Concern created successfully with ID: $insertId");
            echo json_encode(['success' => true, 'id' => $insertId]);
        } else {
            error_log("Execute failed: " . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Database execute error: ' . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
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
        
        $conn = getDBConnection();
        
        // Only allow deletion of pending concerns
        $stmt = $conn->prepare("DELETE FROM concerns WHERE id = ? AND status = 'pending'");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                error_log("Concern deleted successfully");
                echo json_encode(['success' => true]);
            } else {
                error_log("Concern not found or not pending");
                echo json_encode(['success' => false, 'error' => 'Concern not found or cannot be deleted']);
            }
        } else {
            error_log("Delete failed: " . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
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
        
        // Normalize status to database format
        $dbStatus = str_replace('-', '_', $status);
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE concerns SET status = ?, admin_response = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $dbStatus, $response, $id);
        
        if ($stmt->execute()) {
            error_log("Concern status updated successfully");
            echo json_encode(['success' => true]);
        } else {
            error_log("Update failed: " . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
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
        
        $conn = getDBConnection();
        
        // Normalize status to database format
        $dbStatus = str_replace('-', '_', $status);
        
        // Update both response and status if status is provided
        if (!empty($status)) {
            $stmt = $conn->prepare("UPDATE concerns SET status = ?, admin_response = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssi", $dbStatus, $response, $id);
        } else {
            $stmt = $conn->prepare("UPDATE concerns SET admin_response = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("si", $response, $id);
        }
        
        if ($stmt->execute()) {
            error_log("Concern response submitted successfully");
            echo json_encode(['success' => true, 'message' => 'Response submitted successfully']);
        } else {
            error_log("Update failed: " . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        error_log("Exception in respondToConcern: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function listConcerns() {
    try {
        $userEmail = $_GET['userEmail'] ?? '';
        
        $conn = getDBConnection();
        
        if ($userEmail) {
            // Get concerns for specific user with user info
            $stmt = $conn->prepare("SELECT c.*, u.first_name, u.last_name, u.email, CONCAT(u.first_name, ' ', u.last_name) as submittedBy FROM concerns c JOIN users u ON c.user_id = u.id WHERE u.email = ? ORDER BY c.created_at DESC");
            $stmt->bind_param("s", $userEmail);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            // Get all concerns with user info
            $result = $conn->query("SELECT c.*, u.first_name, u.last_name, u.email, CONCAT(u.first_name, ' ', u.last_name) as submittedBy, u.email as submittedByEmail FROM concerns c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC");
        }
        
        $concerns = [];
        while ($row = $result->fetch_assoc()) {
            $concerns[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $concerns]);
        $conn->close();
    } catch (Exception $e) {
        error_log("Exception in listConcerns: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?>
