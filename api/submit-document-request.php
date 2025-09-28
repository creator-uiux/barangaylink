<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    errorResponse('Invalid JSON input');
}

// Validate CSRF token
if (!isset($input['csrf_token']) || !verifyCSRFToken($input['csrf_token'])) {
    errorResponse('Invalid CSRF token', 403);
}

$userId = $_SESSION['user_id'];
$documentType = sanitizeInput($input['document_type'] ?? '');
$purpose = sanitizeInput($input['purpose'] ?? '');
$additionalInfo = sanitizeInput($input['additional_info'] ?? '');

// Validate required fields
if (empty($documentType) || empty($purpose)) {
    errorResponse('Document type and purpose are required');
}

// Validate document type
$validTypes = ['barangay_clearance', 'certificate_of_residency', 'business_permit', 'other'];
if (!in_array($documentType, $validTypes)) {
    errorResponse('Invalid document type');
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Insert document request
    $stmt = $db->prepare("
        INSERT INTO document_requests (user_id, document_type, purpose, additional_info, status) 
        VALUES (?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $userId,
        $documentType,
        $purpose,
        $additionalInfo ?: null
    ]);
    
    $requestId = $db->lastInsertId();
    
    // Log activity
    logActivity($userId, 'document_request', "Requested {$documentType}");
    
    // Get the created request for response
    $stmt = $db->prepare("SELECT * FROM document_requests WHERE id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch();
    
    successResponse('Document request submitted successfully', [
        'request' => $request
    ]);
    
} catch (Exception $e) {
    error_log("Document request error: " . $e->getMessage());
    errorResponse('Failed to submit document request. Please try again.');
}
?>