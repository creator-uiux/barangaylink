<?php
/**
 * Profile management functions
 */

function handleUpdateProfile($postData, $user) {
    try {
        $first_name = $postData['first_name'] ?? '';
        $middle_name = $postData['middle_name'] ?? '';
        $last_name = $postData['last_name'] ?? '';
        $phone = $postData['phone'] ?? '';
        $address = $postData['address'] ?? '';
        
        if (empty($first_name) || empty($last_name)) {
            return ['success' => false, 'message' => 'First name and last name are required'];
        }
        
        // Create full name for backward compatibility
        $fullName = trim($first_name . ' ' . $middle_name . ' ' . $last_name);
        
        $users = loadJsonData('users');
        $updated = false;
        
        // Update user in users array
        foreach ($users as &$u) {
            if ($u['email'] === $user['email']) {
                $u['first_name'] = $first_name;
                $u['middle_name'] = $middle_name;
                $u['last_name'] = $last_name;
                $u['name'] = $fullName; // For backward compatibility
                $u['phone'] = $phone;
                $u['address'] = $address;
                $u['updatedAt'] = date('c');
                $updated = true;
                break;
            }
        }
        
        if ($updated && saveJsonData('users', $users)) {
            // Update session data
            $_SESSION['auth']['user']['first_name'] = $first_name;
            $_SESSION['auth']['user']['middle_name'] = $middle_name;
            $_SESSION['auth']['user']['last_name'] = $last_name;
            $_SESSION['auth']['user']['name'] = $fullName; // For backward compatibility
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