<?php
/**
 * Database Utility Functions for MySQL
 * Replaces JSON file operations with MySQL queries
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Generate unique ID for records
 */
function generateDatabaseId($prefix = 'id') {
    return $prefix . '_' . uniqid() . '_' . mt_rand(1000, 9999);
}

/**
 * Get all users (excluding deleted users by default)
 */
function getAllUsers($includeDeleted = false) {
    try {
        $db = getDB();
        $sql = "SELECT * FROM users";
        if (!$includeDeleted) {
            $sql .= " WHERE status != 'deleted'";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting users: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all users including deleted ones (for admin management)
 */
function getAllUsersWithDeleted() {
    return getAllUsers(true);
}

/**
 * Get user by email (excluding deleted users by default)
 */
function getUserByEmail($email, $includeDeleted = false) {
    try {
        $db = getDB();
        $sql = "SELECT * FROM users WHERE email = ?";
        if (!$includeDeleted) {
            $sql .= " AND status != 'deleted'";
        }
        $stmt = $db->query($sql, [$email]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error getting user: " . $e->getMessage());
        return null;
    }
}

/**
 * Create new user
 */
function createUser($userData) {
    try {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO users (email, password, first_name, middle_name, last_name, role, status, address, phone) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $result = $stmt->execute([
            $userData['email'],
            $userData['password'], // In production, hash this!
            $userData['first_name'],
            $userData['middle_name'] ?? '',
            $userData['last_name'],
            $userData['role'] ?? 'user',
            $userData['status'] ?? 'active',
            $userData['address'] ?? '',
            $userData['phone'] ?? ''
        ]);
        return $result && $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error creating user: " . $e->getMessage());
        return false;
    }
}

/**
 * Update user
 */
function updateUser($email, $updates) {
    try {
        $db = getDB();
        
        $fields = [];
        $values = [];
        
        $allowedFields = ['first_name', 'middle_name', 'last_name', 'address', 'phone', 'password'];
        foreach ($allowedFields as $field) {
            if (isset($updates[$field])) {
                $fields[] = "$field = ?";
                $values[] = $updates[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $email;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE email = ?";
        
        $stmt = $db->query($sql, $values);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error updating user: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete user (mark as deleted instead of permanent deletion)
 */
function deleteUser($email) {
    try {
        $db = getDB();
        $stmt = $db->query("UPDATE users SET status = 'deleted', updated_at = NOW() WHERE email = ?", [$email]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error deleting user: " . $e->getMessage());
        return false;
    }
}

/**
 * Restore deleted user
 */
function restoreUser($email) {
    try {
        $db = getDB();
        $stmt = $db->query("UPDATE users SET status = 'active', updated_at = NOW() WHERE email = ? AND status = 'deleted'", [$email]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error restoring user: " . $e->getMessage());
        return false;
    }
}

/**
 * Permanently delete user (use with caution)
 */
function permanentlyDeleteUser($email) {
    try {
        $db = getDB();
        $stmt = $db->query("DELETE FROM users WHERE email = ?", [$email]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error permanently deleting user: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all document requests
 */
function getAllDocumentRequests($email = null) {
    try {
        $db = getDB();
        if ($email) {
            $stmt = $db->query(
                "SELECT * FROM document_requests WHERE requested_by_email = ? ORDER BY created_at DESC",
                [$email]
            );
        } else {
            $stmt = $db->query("SELECT * FROM document_requests ORDER BY created_at DESC");
        }
        
        $requests = $stmt->fetchAll();
        
        // Convert database format to expected format
        foreach ($requests as &$request) {
            $request['documentType'] = $request['document_type'];
            $request['requestedBy'] = $request['requested_by'];
            $request['requestedByEmail'] = $request['requested_by_email'];
            $request['estimatedFee'] = (float)$request['estimated_fee'];
            $request['processingTime'] = $request['processing_time'];
            $request['adminNotes'] = $request['admin_notes'] ?? '';
            $request['createdAt'] = $request['created_at'];
            $request['updatedAt'] = $request['updated_at'];
        }
        
        return $requests;
    } catch (Exception $e) {
        error_log("Error getting document requests: " . $e->getMessage());
        return [];
    }
}

/**
 * Create document request
 */
function createDocumentRequest($requestData) {
    try {
        $db = getDB();
        $id = $requestData['id'] ?? generateDatabaseId('req');
        
        $stmt = $db->query(
            "INSERT INTO document_requests 
            (id, document_type, purpose, requested_by, requested_by_email, status, estimated_fee, processing_time) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $id,
                $requestData['documentType'],
                $requestData['purpose'],
                $requestData['requestedBy'],
                $requestData['requestedByEmail'],
                $requestData['status'] ?? 'pending',
                $requestData['estimatedFee'] ?? 0,
                $requestData['processingTime'] ?? ''
            ]
        );
        
        return $stmt->rowCount() > 0 ? $id : false;
    } catch (Exception $e) {
        error_log("Error creating document request: " . $e->getMessage());
        return false;
    }
}

/**
 * Update document request
 */
function updateDocumentRequest($id, $updates) {
    try {
        $db = getDB();
        
        $fields = [];
        $values = [];
        
        $fieldMapping = [
            'status' => 'status',
            'adminNotes' => 'admin_notes',
            'estimatedFee' => 'estimated_fee'
        ];
        
        foreach ($fieldMapping as $key => $dbField) {
            if (isset($updates[$key])) {
                $fields[] = "$dbField = ?";
                $values[] = $updates[$key];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE document_requests SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $db->query($sql, $values);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error updating document request: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete document request
 */
function deleteDocumentRequest($id) {
    try {
        $db = getDB();
        $stmt = $db->query("DELETE FROM document_requests WHERE id = ?", [$id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error deleting document request: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all concerns
 */
function getAllConcerns($email = null) {
    try {
        $db = getDB();
        if ($email) {
            $stmt = $db->query(
                "SELECT * FROM concerns WHERE submitted_by_email = ? ORDER BY created_at DESC",
                [$email]
            );
        } else {
            $stmt = $db->query("SELECT * FROM concerns ORDER BY created_at DESC");
        }
        
        $concerns = $stmt->fetchAll();
        
        // Convert database format to expected format
        foreach ($concerns as &$concern) {
            $concern['submittedBy'] = $concern['submitted_by'];
            $concern['submittedByEmail'] = $concern['submitted_by_email'];
            $concern['adminResponse'] = $concern['admin_response'] ?? '';
            $concern['createdAt'] = $concern['created_at'];
            $concern['updatedAt'] = $concern['updated_at'];
        }
        
        return $concerns;
    } catch (Exception $e) {
        error_log("Error getting concerns: " . $e->getMessage());
        return [];
    }
}

/**
 * Create concern
 */
function createConcern($concernData) {
    try {
        $db = getDB();
        $id = $concernData['id'] ?? generateDatabaseId('con');
        
        $stmt = $db->query(
            "INSERT INTO concerns 
            (id, subject, description, category, location, priority, submitted_by, submitted_by_email, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $id,
                $concernData['subject'],
                $concernData['description'],
                $concernData['category'],
                $concernData['location'] ?? '',
                $concernData['priority'] ?? 'medium',
                $concernData['submittedBy'],
                $concernData['submittedByEmail'],
                $concernData['status'] ?? 'pending'
            ]
        );
        
        return $stmt->rowCount() > 0 ? $id : false;
    } catch (Exception $e) {
        error_log("Error creating concern: " . $e->getMessage());
        return false;
    }
}

/**
 * Update concern
 */
function updateConcern($id, $updates) {
    try {
        $db = getDB();
        
        $fields = [];
        $values = [];
        
        $fieldMapping = [
            'status' => 'status',
            'priority' => 'priority',
            'adminResponse' => 'admin_response'
        ];
        
        foreach ($fieldMapping as $key => $dbField) {
            if (isset($updates[$key])) {
                $fields[] = "$dbField = ?";
                $values[] = $updates[$key];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE concerns SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $db->query($sql, $values);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error updating concern: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete concern
 */
function deleteConcern($id) {
    try {
        $db = getDB();
        $stmt = $db->query("DELETE FROM concerns WHERE id = ?", [$id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error deleting concern: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all notifications
 */
function getAllNotifications($userId = null, $unreadOnly = false) {
    try {
        $db = getDB();
        $sql = "SELECT * FROM notifications WHERE 1=1";
        $params = [];
        
        if ($userId) {
            $sql .= " AND (user_id = ? OR user_id IS NULL)";
            $params[] = $userId;
        }
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY timestamp DESC";
        
        $stmt = $db->query($sql, $params);
        $notifications = $stmt->fetchAll();
        
        // Convert database format to expected format
        foreach ($notifications as &$notification) {
            $notification['userId'] = $notification['user_id'];
            $notification['read'] = (bool)$notification['is_read'];
            $notification['readAt'] = $notification['read_at'];
        }
        
        return $notifications;
    } catch (Exception $e) {
        error_log("Error getting notifications: " . $e->getMessage());
        return [];
    }
}

/**
 * Create notification
 */
function createDatabaseNotification($type, $title, $message, $userId = null) {
    try {
        $db = getDB();
        $id = generateDatabaseId('not');
        
        $stmt = $db->query(
            "INSERT INTO notifications (id, type, title, message, user_id) 
             VALUES (?, ?, ?, ?, ?)",
            [$id, $type, $title, $message, $userId]
        );
        
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Mark notifications as read
 */
function markNotificationsAsRead($notificationIds) {
    try {
        $db = getDB();
        $placeholders = implode(',', array_fill(0, count($notificationIds), '?'));
        $sql = "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id IN ($placeholders)";
        
        $stmt = $db->query($sql, $notificationIds);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error marking notifications as read: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all announcements
 */
function getAllAnnouncements($activeOnly = false) {
    try {
        $db = getDB();
        $sql = "SELECT * FROM announcements";
        
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $db->query($sql);
        $announcements = $stmt->fetchAll();
        
        // Convert database format to expected format
        foreach ($announcements as &$announcement) {
            $announcement['isActive'] = (bool)$announcement['is_active'];
            $announcement['createdAt'] = $announcement['created_at'];
            $announcement['updatedAt'] = $announcement['updated_at'];
        }
        
        return $announcements;
    } catch (Exception $e) {
        error_log("Error getting announcements: " . $e->getMessage());
        return [];
    }
}

/**
 * Create announcement
 */
function createAnnouncement($announcementData) {
    try {
        $db = getDB();
        $id = $announcementData['id'] ?? generateDatabaseId('ann');
        
        $stmt = $db->query(
            "INSERT INTO announcements (id, title, content, type, priority, author, is_active) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $id,
                $announcementData['title'],
                $announcementData['content'],
                $announcementData['type'] ?? 'announcement',
                $announcementData['priority'] ?? 'medium',
                $announcementData['author'] ?? 'Admin',
                $announcementData['isActive'] ?? true
            ]
        );
        
        return $stmt->rowCount() > 0 ? $id : false;
    } catch (Exception $e) {
        error_log("Error creating announcement: " . $e->getMessage());
        return false;
    }
}

/**
 * Update announcement
 */
function updateAnnouncement($id, $updates) {
    try {
        $db = getDB();
        
        $fields = [];
        $values = [];
        
        $fieldMapping = [
            'title' => 'title',
            'content' => 'content',
            'type' => 'type',
            'priority' => 'priority',
            'isActive' => 'is_active'
        ];
        
        foreach ($fieldMapping as $key => $dbField) {
            if (isset($updates[$key])) {
                $fields[] = "$dbField = ?";
                $values[] = $updates[$key];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE announcements SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $db->query($sql, $values);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Error updating announcement: " . $e->getMessage());
        return false;
    }
}

/**
 * Get dashboard statistics from database
 */
function getDatabaseDashboardStats() {
    try {
        $db = getDB();
        
        // Total users
        $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
        $totalUsers = $stmt->fetchColumn();
        
        // Total requests
        $stmt = $db->query("SELECT COUNT(*) FROM document_requests");
        $totalRequests = $stmt->fetchColumn();
        
        // Pending requests
        $stmt = $db->query("SELECT COUNT(*) FROM document_requests WHERE status = 'pending'");
        $pendingRequests = $stmt->fetchColumn();
        
        // Total concerns
        $stmt = $db->query("SELECT COUNT(*) FROM concerns");
        $totalConcerns = $stmt->fetchColumn();
        
        // Active concerns
        $stmt = $db->query("SELECT COUNT(*) FROM concerns WHERE status IN ('pending', 'in-progress')");
        $activeConcerns = $stmt->fetchColumn();
        
        // Unread notifications
        $stmt = $db->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
        $unreadNotifications = $stmt->fetchColumn();
        
        // Recent requests (last 7 days)
        $stmt = $db->query("SELECT COUNT(*) FROM document_requests WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $recentRequests = $stmt->fetchColumn();
        
        return [
            'totalUsers' => (int)$totalUsers,
            'totalRequests' => (int)$totalRequests,
            'pendingRequests' => (int)$pendingRequests,
            'totalConcerns' => (int)$totalConcerns,
            'activeConcerns' => (int)$activeConcerns,
            'unreadNotifications' => (int)$unreadNotifications,
            'recentRequests' => (int)$recentRequests,
            'lastUpdated' => date('c')
        ];
    } catch (Exception $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
        return [
            'totalUsers' => 0,
            'totalRequests' => 0,
            'pendingRequests' => 0,
            'totalConcerns' => 0,
            'activeConcerns' => 0,
            'unreadNotifications' => 0,
            'recentRequests' => 0,
            'lastUpdated' => date('c')
        ];
    }
}
?>
