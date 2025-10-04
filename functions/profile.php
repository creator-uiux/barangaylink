<?php
/**
 * Profile management functions
 */

function handleUpdateProfile($postData, $user) {
    try {
        $name = $postData['name'] ?? '';
        $phone = $postData['phone'] ?? '';
        $address = $postData['address'] ?? '';
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Name is required'];
        }
        
        $users = loadJsonData('users');
        $updated = false;
        
        // Update user in users array
        foreach ($users as &$u) {
            if ($u['email'] === $user['email']) {
                $u['name'] = $name;
                $u['phone'] = $phone;
                $u['address'] = $address;
                $u['updatedAt'] = date('c');
                $updated = true;
                break;
            }
        }
        
        if ($updated && saveJsonData('users', $users)) {
            // Update session data
            $_SESSION['auth']['user']['name'] = $name;
            $_SESSION['auth']['user']['phone'] = $phone;
            $_SESSION['auth']['user']['address'] = $address;
            
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
        
    } catch (Exception $e) {
        error_log('Error updating profile: ' . $e->getMessage());
        return ['success' => false, 'message' => 'System error occurred'];
    }
}
?>