<?php
/**
 * Documents API Endpoint
 * SYNCHRONIZED with AdminDocumentManagement.tsx
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

// Log the request
error_log("Documents API called - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    error_log("Action: $action");
    
    switch ($action) {
        case 'create':
            createDocument();
            break;
            
        case 'delete':
            deleteDocument();
            break;
            
        case 'update':
        case 'update_status':
            updateDocumentStatus();
            break;
            
        case 'approve':
            approveDocument();
            break;
            
        case 'reject':
            rejectDocument();
            break;
            
        default:
            error_log("Invalid action: $action");
            echo json_encode(['success' => false, 'error' => 'Invalid action: ' . $action]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    listDocuments();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

function createDocument() {
    try {
        $userId = $_POST['user_id'] ?? 0;
        $documentType = trim($_POST['documentType'] ?? '');
        $purpose = trim($_POST['purpose'] ?? '');
        $quantity = intval($_POST['quantity'] ?? 1);
        $notes = trim($_POST['notes'] ?? '');
        
        error_log("Creating document - User: $userId, Type: $documentType, Purpose: $purpose");
        
        // Validation
        if (!$userId || empty($userId)) {
            error_log("Validation failed: Missing user_id");
            echo json_encode(['success' => false, 'error' => 'Missing user ID']);
            exit;
        }
        
        if (empty($documentType)) {
            error_log("Validation failed: Missing document type");
            echo json_encode(['success' => false, 'error' => 'Please select a document type']);
            exit;
        }
        
        if (empty($purpose)) {
            error_log("Validation failed: Missing purpose");
            echo json_encode(['success' => false, 'error' => 'Please enter the purpose']);
            exit;
        }
        
        if ($quantity < 1 || $quantity > 10) {
            error_log("Validation failed: Invalid quantity");
            echo json_encode(['success' => false, 'error' => 'Quantity must be between 1 and 10']);
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
        $stmt = $conn->prepare("INSERT INTO documents (user_id, document_type, purpose, quantity, notes, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Database prepare error: ' . $conn->error]);
            exit;
        }
        
        $stmt->bind_param("issis", $userId, $documentType, $purpose, $quantity, $notes);
        
        if ($stmt->execute()) {
            $insertId = $conn->insert_id;
            error_log("Document created successfully with ID: $insertId");
            echo json_encode(['success' => true, 'id' => $insertId]);
        } else {
            error_log("Execute failed: " . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Database execute error: ' . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        error_log("Exception in createDocument: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Exception: ' . $e->getMessage()]);
    }
    exit;
}

function deleteDocument() {
    try {
        $id = $_POST['id'] ?? 0;
        
        error_log("Deleting document ID: $id");
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Missing document ID']);
            exit;
        }
        
        $conn = getDBConnection();
        
        // Only allow deletion of pending documents
        $stmt = $conn->prepare("DELETE FROM documents WHERE id = ? AND status = 'pending'");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                error_log("Document deleted successfully");
                echo json_encode(['success' => true]);
            } else {
                error_log("Document not found or not pending");
                echo json_encode(['success' => false, 'error' => 'Document not found or cannot be deleted']);
            }
        } else {
            error_log("Delete failed: " . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        error_log("Exception in deleteDocument: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function updateDocumentStatus() {
    try {
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        $adminNotes = $_POST['admin_notes'] ?? '';
        
        error_log("Updating document ID: $id to status: $status");
        
        if (!$id || !$status) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE documents SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $status, $adminNotes, $id);
        
        if ($stmt->execute()) {
            error_log("Document status updated successfully");
            echo json_encode(['success' => true]);
        } else {
            error_log("Update failed: " . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        error_log("Exception in updateDocumentStatus: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function approveDocument() {
    try {
        $id = $_POST['document_id'] ?? $_POST['id'] ?? 0;
        $adminNotes = $_POST['admin_notes'] ?? '';
        
        error_log("Approving document ID: $id");
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Missing document ID']);
            exit;
        }
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE documents SET status = 'approved', admin_notes = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $adminNotes, $id);
        
        if ($stmt->execute()) {
            error_log("Document approved successfully");
            echo json_encode(['success' => true, 'message' => 'Document approved successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function rejectDocument() {
    try {
        $id = $_POST['document_id'] ?? $_POST['id'] ?? 0;
        $adminNotes = $_POST['admin_notes'] ?? '';
        
        error_log("Rejecting document ID: $id");
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Missing document ID']);
            exit;
        }
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE documents SET status = 'rejected', admin_notes = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $adminNotes, $id);
        
        if ($stmt->execute()) {
            error_log("Document rejected successfully");
            echo json_encode(['success' => true, 'message' => 'Document rejected successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function listDocuments() {
    try {
        $userEmail = $_GET['userEmail'] ?? '';
        
        $conn = getDBConnection();
        
        if ($userEmail) {
            // Get documents for specific user
            $stmt = $conn->prepare("SELECT d.*, u.first_name, u.last_name, u.email, CONCAT(u.first_name, ' ', u.last_name) as requestedBy FROM documents d JOIN users u ON d.user_id = u.id WHERE u.email = ? ORDER BY d.created_at DESC");
            $stmt->bind_param("s", $userEmail);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            // Get all documents with user info
            $result = $conn->query("SELECT d.*, u.first_name, u.last_name, u.email, CONCAT(u.first_name, ' ', u.last_name) as requestedBy FROM documents d JOIN users u ON d.user_id = u.id ORDER BY d.created_at DESC");
        }
        
        $documents = [];
        while ($row = $result->fetch_assoc()) {
            $documents[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $documents]);
        $conn->close();
    } catch (Exception $e) {
        error_log("Exception in listDocuments: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?>
