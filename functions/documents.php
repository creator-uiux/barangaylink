<?php
/**
 * Document request handling functions - Database & JSON compatible
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
        
        // Get document type info for fee and processing time
        $docTypes = DOCUMENT_TYPES;
        $docInfo = $docTypes[$documentType] ?? null;
        
        $newRequest = [
            'id' => 'req_' . time() . '_' . rand(1000, 9999),
            'documentType' => $documentType,
            'purpose' => $purpose,
            'quantity' => $quantity,
            'notes' => $notes,
            'requestedBy' => $user['name'],
            'requestedByEmail' => $user['email'],
            'status' => 'pending',
            'estimatedFee' => $docInfo ? $docInfo['fee'] * $quantity : 0,
            'processingTime' => $docInfo ? $docInfo['processingTime'] : '3-5 business days',
            'createdAt' => date('c')
        ];
        
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            $result = createDocumentRequest($newRequest);
            if ($result) {
                // Create notification
                createDatabaseNotification(
                    'info',
                    'Document Request Submitted',
                    "Your request for {$docInfo['name']} has been submitted successfully.",
                    $user['email']
                );
                return ['success' => true, 'message' => 'Document request submitted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to save document request'];
            }
        } else {
            $requests = loadJsonData('requests');
            $requests[] = $newRequest;
            
            if (saveJsonData('requests', $requests)) {
                // Create notification
                createNotification(
                    'info',
                    'Document Request Submitted',
                    "Your request for {$docInfo['name']} has been submitted successfully.",
                    $user['email']
                );
                return ['success' => true, 'message' => 'Document request submitted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to save document request'];
            }
        }
        
    } catch (Exception $e) {
        error_log('Error submitting document request: ' . $e->getMessage());
        return ['success' => false, 'message' => 'System error occurred'];
    }
}

/**
 * Get all document requests (with optional email filter)
 */
function getDocumentRequests($email = null) {
    try {
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            return getAllDocumentRequests($email);
        } else {
            $requests = loadJsonData('requests');
            
            if ($email) {
                return array_filter($requests, function($req) use ($email) {
                    return $req['requestedByEmail'] === $email;
                });
            }
            
            return $requests;
        }
    } catch (Exception $e) {
        error_log('Error getting document requests: ' . $e->getMessage());
        return [];
    }
}

/**
 * Update document request status (admin function)
 */
function updateDocumentRequestStatus($requestId, $status, $adminNotes = '') {
    try {
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            return updateDocumentRequest($requestId, [
                'status' => $status,
                'adminNotes' => $adminNotes
            ]);
        } else {
            $requests = loadJsonData('requests');
            $updated = false;
            
            foreach ($requests as &$request) {
                if ($request['id'] === $requestId) {
                    $request['status'] = $status;
                    if ($adminNotes) {
                        $request['adminNotes'] = $adminNotes;
                    }
                    $request['updatedAt'] = date('c');
                    $updated = true;
                    break;
                }
            }
            
            if ($updated) {
                return saveJsonData('requests', $requests);
            }
            
            return false;
        }
    } catch (Exception $e) {
        error_log('Error updating document request: ' . $e->getMessage());
        return false;
    }
}

/**
 * Delete document request (admin function)
 */
function removeDocumentRequest($requestId) {
    try {
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            return deleteDocumentRequest($requestId);
        } else {
            $requests = loadJsonData('requests');
            $filtered = array_filter($requests, function($req) use ($requestId) {
                return $req['id'] !== $requestId;
            });
            
            if (count($filtered) < count($requests)) {
                return saveJsonData('requests', $filtered);
            }
            
            return false;
        }
    } catch (Exception $e) {
        error_log('Error deleting document request: ' . $e->getMessage());
        return false;
    }
}
?>
