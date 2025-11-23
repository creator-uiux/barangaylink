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
        $db = getDB();

        // Prepare statement
        $stmt = $db->prepare("INSERT INTO documents (user_id, document_type, purpose, quantity, notes, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$userId, $documentType, $purpose, $quantity, $notes]);

        if ($stmt->rowCount() > 0) {
            $insertId = $db->lastInsertId();
            error_log("Document created successfully with ID: $insertId");

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
                    $notifStmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, related_type, related_id, created_at) VALUES (?, 'info', 'Document Request', ? , 'document', ?, NOW())");
                    $notifStmt->execute([$admin['id'], "$fullName has requested a document.", $insertId]);
                }
                error_log("Notifications created for " . count($admins) . " admins");
            } catch (Exception $e) {
                error_log("Failed to create admin notifications: " . $e->getMessage());
            }

            echo json_encode(['success' => true, 'id' => $insertId]);
        } else {
            error_log("Execute failed");
            echo json_encode(['success' => false, 'error' => 'Failed to create document']);
        }
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
        
        $db = getDB();

        // Only allow deletion of pending documents
        $stmt = $db->prepare("DELETE FROM documents WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            error_log("Document deleted successfully");
            echo json_encode(['success' => true]);
        } else {
            error_log("Document not found or not pending");
            echo json_encode(['success' => false, 'error' => 'Document not found or cannot be deleted']);
        }
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
        
        $db = getDB();
        $stmt = $db->prepare("UPDATE documents SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $adminNotes, $id]);

        if ($stmt->rowCount() > 0) {
            error_log("Document status updated successfully");

            // Create notification for the user about status change
            try {
                // Get document and user info
                $docStmt = $db->prepare("SELECT d.document_type, u.email FROM documents d JOIN users u ON d.user_id = u.id WHERE d.id = ?");
                $docStmt->execute([$id]);
                $docInfo = $docStmt->fetch(PDO::FETCH_ASSOC);

                if ($docInfo) {
                    $statusMessages = [
                        'processing' => 'Your document request is now being processed.',
                        'approved' => 'Your document request has been approved.',
                        'rejected' => 'Your document request has been rejected.',
                        'ready' => 'Your document is ready for pickup.',
                        'claimed' => 'Your document has been claimed.'
                    ];

                    $message = isset($statusMessages[$status]) ? $statusMessages[$status] : "Your document status has been updated to: " . ucfirst($status);

                    createNotification($docInfo['email'], 'info', 'Document Status Update', $message);
                    error_log("Status update notification sent to user");
                }
            } catch (Exception $e) {
                error_log("Failed to create status update notification: " . $e->getMessage());
            }

            echo json_encode(['success' => true]);
        } else {
            error_log("Document not found");
            echo json_encode(['success' => false, 'error' => 'Document not found']);
        }
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
        
        $db = getDB();
        $stmt = $db->prepare("UPDATE documents SET status = 'approved', admin_notes = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$adminNotes, $id]);

        if ($stmt->rowCount() > 0) {
            error_log("Document approved successfully");
            echo json_encode(['success' => true, 'message' => 'Document approved successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Document not found']);
        }
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
        
        $db = getDB();
        $stmt = $db->prepare("UPDATE documents SET status = 'rejected', admin_notes = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$adminNotes, $id]);

        if ($stmt->rowCount() > 0) {
            error_log("Document rejected successfully");
            echo json_encode(['success' => true, 'message' => 'Document rejected successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Document not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function listDocuments() {
    try {
        $userEmail = $_GET['userEmail'] ?? '';

        if ($userEmail) {
            // Get documents for specific user
            $documents = fetchAll("SELECT d.*, u.first_name, u.last_name, u.email, (u.first_name || ' ' || u.last_name) as requestedBy FROM documents d JOIN users u ON d.user_id = u.id WHERE u.email = ? ORDER BY d.created_at DESC", [$userEmail]);
        } else {
            // Get all documents with user info
            $documents = fetchAll("SELECT d.*, u.first_name, u.last_name, u.email, (u.first_name || ' ' || u.last_name) as requestedBy FROM documents d JOIN users u ON d.user_id = u.id ORDER BY d.created_at DESC");
        }

        echo json_encode(['success' => true, 'data' => $documents]);
    } catch (Exception $e) {
        error_log("Exception in listDocuments: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?>
