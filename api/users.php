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
        $role = $_POST['role'] ?? 'user';
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
        
        $conn = getDBConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'A user with this email already exists']);
            exit;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, role, address, phone, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssssss", $firstName, $middleName, $lastName, $email, $hashedPassword, $role, $address, $phone);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create user']);
        }
        
        $stmt->close();
        $conn->close();
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
        $role = $_POST['role'] ?? 'user';
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        // Validation
        if (empty($originalEmail) || empty($firstName) || empty($lastName)) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        $conn = getDBConnection();
        
        // Update user
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, role = ?, address = ?, phone = ?, updated_at = NOW() WHERE email = ?");
        $stmt->bind_param("sssssss", $firstName, $middleName, $lastName, $role, $address, $phone, $originalEmail);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0 || $stmt->errno === 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'User not found']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update user']);
        }
        
        $stmt->close();
        $conn->close();
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
            $_SESSION['error'] = 'Invalid user ID';
            header('Location: ../index.php?view=profile');
            exit;
        }
        
        // Validate address
        if ($address && strlen($address) < 10) {
            $_SESSION['error'] = 'Please provide a complete address (at least 10 characters)';
            header('Location: ../index.php?view=profile');
            exit;
        }
        
        // Validate phone
        if ($phone) {
            $phoneRegex = '/^(\+\d{1,3}[- ]?)?\d{10,}$/';
            if (!preg_match($phoneRegex, str_replace(' ', '', $phone))) {
                $_SESSION['error'] = 'Please enter a valid phone number';
                header('Location: ../index.php?view=profile');
                exit;
            }
        }
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE users SET address = ?, phone = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $address, $phone, $userId);
        
        if ($stmt->execute()) {
            // Update session data
            if (isset($_SESSION['auth']['user'])) {
                $_SESSION['auth']['user']['address'] = $address;
                $_SESSION['auth']['user']['phone'] = $phone;
            }
            
            $_SESSION['success'] = 'Profile updated successfully!';
            header('Location: ../index.php?view=profile&saved=1');
        } else {
            $_SESSION['error'] = 'Failed to update profile';
            header('Location: ../index.php?view=profile');
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
        header('Location: ../index.php?view=profile');
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
        
        $conn = getDBConnection();
        
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
            exit;
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update password']);
        }
        
        $stmt->close();
        $conn->close();
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
        
        $conn = getDBConnection();
        
        // Get user ID from email if only email is provided
        if (!$userId && $email) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if ($user) {
                $userId = $user['id'];
            } else {
                echo json_encode(['success' => false, 'error' => 'User not found']);
                exit;
            }
        }
        
        // Delete user's related data first
        $conn->query("DELETE FROM documents WHERE user_id = $userId");
        $conn->query("DELETE FROM concerns WHERE user_id = $userId");
        $conn->query("DELETE FROM notifications WHERE user_id = $userId");
        
        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete user']);
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function listUsers() {
    try {
        $conn = getDBConnection();
        $result = $conn->query("SELECT id, first_name, middle_name, last_name, email, role, address, phone, created_at FROM users ORDER BY created_at DESC");
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $users]);
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>