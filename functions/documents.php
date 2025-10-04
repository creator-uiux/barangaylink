<?php
/**
 * Document request handling functions
 */

function handleSubmitDocumentRequest($postData, $user) {
    try {
        $documentType = $postData['documentType'] ?? '';
        $purpose = $postData['purpose'] ?? '';
        $quantity = intval($postData['quantity'] ?? 1);
        $notes = $postData['notes'] ?? '';
        
        if (empty($documentType) || empty($purpose)) {
            return ['success' => false, 'message' => 'Please fill in all required fields'];
        }
        
        $requests = loadJsonData('requests');
        
        $newRequest = [
            'id' => time() . '_' . rand(1000, 9999),
            'documentType' => $documentType,
            'purpose' => $purpose,
            'quantity' => $quantity,
            'notes' => $notes,
            'requestedBy' => $user['name'],
            'requestedByEmail' => $user['email'],
            'status' => 'pending',
            'createdAt' => date('c')
        ];
        
        $requests[] = $newRequest;
        
        if (saveJsonData('requests', $requests)) {
            return ['success' => true, 'message' => 'Document request submitted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to save document request'];
        }
        
    } catch (Exception $e) {
        error_log('Error submitting document request: ' . $e->getMessage());
        return ['success' => false, 'message' => 'System error occurred'];
    }
}
?>