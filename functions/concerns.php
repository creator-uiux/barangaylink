<?php
/**
 * Concerns handling functions
 */

function handleSubmitConcern($postData, $user) {
    try {
        $category = $postData['category'] ?? '';
        $subject = $postData['subject'] ?? '';
        $description = $postData['description'] ?? '';
        $location = $postData['location'] ?? '';
        
        if (empty($category) || empty($subject) || empty($description)) {
            return ['success' => false, 'message' => 'Please fill in all required fields'];
        }
        
        $concerns = loadJsonData('concerns');
        
        $newConcern = [
            'id' => time() . '_' . rand(1000, 9999),
            'category' => $category,
            'subject' => $subject,
            'description' => $description,
            'location' => $location,
            'submittedBy' => $user['name'],
            'submittedByEmail' => $user['email'],
            'status' => 'pending',
            'createdAt' => date('c')
        ];
        
        $concerns[] = $newConcern;
        
        if (saveJsonData('concerns', $concerns)) {
            return ['success' => true, 'message' => 'Concern submitted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to save concern'];
        }
        
    } catch (Exception $e) {
        error_log('Error submitting concern: ' . $e->getMessage());
        return ['success' => false, 'message' => 'System error occurred'];
    }
}
?>