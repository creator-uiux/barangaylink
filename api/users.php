<?php
/**
 * Users API Endpoint
 * Handles all user-related operations
 */

require_once '../config.php';
require_once '../db.php';
require_once '../init.php';

header('Content-Type: application/json');

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            createUser();
            break;
            
        case 'update':
            updateUser();
            break;
            
        case 'delete':
            deleteUser();
            break;
            
        case 'update_profile':
            updateProfile();
            break;
            
        case 'change_password':
            changePassword();
            break;
            
        case 'delete_user':
            deleteUser();
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    listUsers();
}

function createUser() {
    try {
        $firstName = trim($_POST['first_name'] ?? '');
        $middleName = trim($_POST['middle_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'resident';
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        // Validation
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        if (strlen($password) < 8) {
            echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
            exit;
        }
        
        $db = getDB();

        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['success' => false, 'error' => 'A user with this email already exists']);
            exit;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        $stmt = $db->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, role, address, phone, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, datetime('now'))");
        $stmt->execute([$firstName, $middleName, $lastName, $email, $hashedPassword, $role, $address, $phone]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create user']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function updateUser() {
    try {
        $originalEmail = trim($_POST['original_email'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $middleName = trim($_POST['middle_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $role = $_POST['role'] ?? 'resident';
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        // Validation
        if (empty($originalEmail) || empty($firstName) || empty($lastName)) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        $db = getDB();

        // Update user
        $stmt = $db->prepare("UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, role = ?, address = ?, phone = ?, updated_at = datetime('now') WHERE email = ?");
        $stmt->execute([$firstName, $middleName, $lastName, $role, $address, $phone, $originalEmail]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'User not found or no changes made']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function updateProfile() {
    try {
        $userId = $_POST['user_id'] ?? 0;
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
            exit;
        }

        // Validate address
        if ($address && strlen($address) < 10) {
            echo json_encode(['success' => false, 'error' => 'Please provide a complete address (at least 10 characters)']);
            exit;
        }

        // Validate phone
        if ($phone) {
            $phoneRegex = '/^(\+\d{1,3}[- ]?)?\d{10,}$/';
            if (!preg_match($phoneRegex, str_replace(' ', '', $phone))) {
                echo json_encode(['success' => false, 'error' => 'Please enter a valid phone number']);
                exit;
            }
        }

        $db = getDB();
        $stmt = $db->prepare("UPDATE users SET address = ?, phone = ?, updated_at = datetime('now') WHERE id = ?");
        $stmt->execute([$address, $phone, $userId]);

        if ($stmt->rowCount() > 0) {
            // Update session data
            if (isset($_SESSION['auth']['user'])) {
                $_SESSION['auth']['user']['address'] = $address;
                $_SESSION['auth']['user']['phone'] = $phone;
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit;
}

function changePassword() {
    try {
        $userId = $_POST['user_id'] ?? 0;
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        
        if (!$userId || !$currentPassword || !$newPassword) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        if (strlen($newPassword) < 8) {
            echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
            exit;
        }
        
        $db = getDB();

        // Verify current password
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
            exit;
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = datetime('now') WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update password']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function deleteUser() {
    try {
        $userId = $_POST['user_id'] ?? 0;
        $email = trim($_POST['email'] ?? '');
        
        if (!$userId && !$email) {
            echo json_encode(['success' => false, 'error' => 'Missing user ID or email']);
            exit;
        }
        
        $db = getDB();

        // Get user ID from email if only email is provided
        if (!$userId && $email) {
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $userId = $user['id'];
            } else {
                echo json_encode(['success' => false, 'error' => 'User not found']);
                exit;
            }
        }

        // Delete user's related data first
        $db->exec("DELETE FROM documents WHERE user_id = $userId");
        $db->exec("DELETE FROM concerns WHERE user_id = $userId");
        $db->exec("DELETE FROM notifications WHERE user_id = $userId");

        // Delete user
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete user']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function listUsers() {
    try {
        $db = getDB();
        $users = fetchAll("SELECT id, first_name, middle_name, last_name, email, role, address, phone, created_at FROM users ORDER BY created_at DESC");

        echo json_encode(['success' => true, 'data' => $users]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>