<?php
/**
 * Concerns handling functions - Database & JSON compatible
 */

function handleSubmitConcern($postData, $user) {
    try {
        $category = $postData['category'] ?? '';
        $subject = $postData['subject'] ?? '';
        $description = $postData['description'] ?? '';
        $location = $postData['location'] ?? '';
        $priority = $postData['priority'] ?? 'medium';
        
        if (empty($category) || empty($subject) || empty($description)) {
            return ['success' => false, 'message' => 'Please fill in all required fields'];
        }
        
        $newConcern = [
            'id' => 'con_' . time() . '_' . rand(1000, 9999),
            'category' => $category,
            'subject' => $subject,
            'description' => $description,
            'location' => $location,
            'priority' => $priority,
            'submittedBy' => $user['name'],
            'submittedByEmail' => $user['email'],
            'status' => 'pending',
            'createdAt' => date('c')
        ];
        
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            $result = createConcern($newConcern);
            if ($result) {
                // Create notification
                createDatabaseNotification(
                    'info',
                    'Concern Submitted',
                    "Your concern about '{$subject}' has been submitted successfully.",
                    $user['email']
                );
                return ['success' => true, 'message' => 'Concern submitted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to save concern'];
            }
        } else {
            $concerns = loadJsonData('concerns');
            $concerns[] = $newConcern;
            
            if (saveJsonData('concerns', $concerns)) {
                // Create notification
                createNotification(
                    'info',
                    'Concern Submitted',
                    "Your concern about '{$subject}' has been submitted successfully.",
                    $user['email']
                );
                return ['success' => true, 'message' => 'Concern submitted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to save concern'];
            }
        }
        
    } catch (Exception $e) {
        error_log('Error submitting concern: ' . $e->getMessage());
        return ['success' => false, 'message' => 'System error occurred'];
    }
}

/**
 * Get all concerns (with optional email filter)
 */
function getConcerns($email = null) {
    try {
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            return getAllConcerns($email);
        } else {
            $concerns = loadJsonData('concerns');
            
            if ($email) {
                return array_filter($concerns, function($concern) use ($email) {
                    return $concern['submittedByEmail'] === $email;
                });
            }
            
            return $concerns;
        }
    } catch (Exception $e) {
        error_log('Error getting concerns: ' . $e->getMessage());
        return [];
    }
}

/**
 * Update concern status (admin function)
 */
function updateConcernStatus($concernId, $status, $adminResponse = '', $priority = null) {
    try {
        $updates = ['status' => $status];
        
        if ($adminResponse) {
            $updates['adminResponse'] = $adminResponse;
        }
        
        if ($priority) {
            $updates['priority'] = $priority;
        }
        
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            return updateConcern($concernId, $updates);
        } else {
            $concerns = loadJsonData('concerns');
            $updated = false;
            
            foreach ($concerns as &$concern) {
                if ($concern['id'] === $concernId) {
                    $concern['status'] = $status;
                    if ($adminResponse) {
                        $concern['adminResponse'] = $adminResponse;
                    }
                    if ($priority) {
                        $concern['priority'] = $priority;
                    }
                    $concern['updatedAt'] = date('c');
                    $updated = true;
                    break;
                }
            }
            
            if ($updated) {
                return saveJsonData('concerns', $concerns);
            }
            
            return false;
        }
    } catch (Exception $e) {
        error_log('Error updating concern: ' . $e->getMessage());
        return false;
    }
}

/**
 * Delete concern (admin function)
 */
function removeConcern($concernId) {
    try {
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            return deleteConcern($concernId);
        } else {
            $concerns = loadJsonData('concerns');
            $filtered = array_filter($concerns, function($concern) use ($concernId) {
                return $concern['id'] !== $concernId;
            });
            
            if (count($filtered) < count($concerns)) {
                return saveJsonData('concerns', $filtered);
            }
            
            return false;
        }
    } catch (Exception $e) {
        error_log('Error deleting concern: ' . $e->getMessage());
        return false;
    }
}
?>
