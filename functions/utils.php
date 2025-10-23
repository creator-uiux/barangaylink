<?php
/**
 * Enhanced Utility Functions - PHP Version
 * 100% EXACT conversion matching App.tsx localStorage operations and functionality
 */

// Define script start time for performance monitoring
if (!defined('SCRIPT_START')) {
    define('SCRIPT_START', microtime(true));
}

/**
 * Get data file path (matching STORAGE_KEYS pattern)
 * @param string $key
 * @return string
 */
function getDataFilePath($key) {
    return __DIR__ . '/../data/' . $key . '.json';
}

/**
 * Load JSON data from file (matching getStorageItem from App.tsx)
 * @param string $filename
 * @return array
 */
function loadJsonData($filename) {
    $filepath = getDataFilePath($filename);
    
    // Ensure data directory exists
    $dataDir = dirname($filepath);
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    if (!file_exists($filepath)) {
        // Initialize with empty array (matching defaultValue pattern)
        $defaultData = [];
        saveJsonData($filename, $defaultData);
        return $defaultData;
    }
    
    try {
        $content = file_get_contents($filepath);
        if ($content === false) {
            error_log("Failed to read file: {$filepath}");
            return [];
        }
        
        $data = json_decode($content, true);
        
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error for {$filepath}: " . json_last_error_msg());
            return [];
        }
        
        return is_array($data) ? $data : [];
    } catch (Exception $e) {
        error_log("Error loading JSON data from {$filepath}: " . $e->getMessage());
        return [];
    }
}

/**
 * Save JSON data to file (matching setStorageItem from App.tsx)
 * @param string $filename
 * @param array $data
 * @return bool
 */
function saveJsonData($filename, $data) {
    $filepath = getDataFilePath($filename);
    
    // Ensure data directory exists
    $dataDir = dirname($filepath);
    if (!is_dir($dataDir)) {
        if (!mkdir($dataDir, 0755, true)) {
            error_log("Failed to create data directory: {$dataDir}");
            return false;
        }
    }
    
    try {
        $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        if ($jsonContent === false) {
            error_log("JSON encode error for {$filename}: " . json_last_error_msg());
            return false;
        }
        
        $result = file_put_contents($filepath, $jsonContent, LOCK_EX);
        
        if ($result === false) {
            error_log("Failed to write file: {$filepath}");
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Error saving JSON data to {$filepath}: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate unique ID (matching generateId patterns in React)
 * @return string
 */
function generateId() {
    return 'id_' . uniqid() . '_' . mt_rand(1000, 9999);
}

/**
 * Get current time formatted (matching getCurrentTime from React)
 * @return string
 */
function getCurrentTime() {
    return date('g:i A'); // 12-hour format with AM/PM
}

/**
 * Get current date formatted (matching date displays)
 * @return string
 */
function getCurrentDate() {
    return date('l, F j, Y'); // Full day and date
}

/**
 * Time ago function (matching React timeAgo utilities)
 * @param string $dateString
 * @return string
 */
function timeAgo($dateString) {
    $now = new DateTime();
    $then = new DateTime($dateString);
    $diff = $now->getTimestamp() - $then->getTimestamp();
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 2592000) return floor($diff / 86400) . 'd ago';
    if ($diff < 31536000) return floor($diff / 2592000) . 'mo ago';
    return floor($diff / 31536000) . 'y ago';
}

/**
 * Get dashboard statistics (matching React dashboard data)
 * @return array
 */
function getDashboardStats() {
    try {
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            return getDatabaseDashboardStats();
        } else {
            $users = loadJsonData('users');
            $requests = loadJsonData('requests');
            $concerns = loadJsonData('concerns');
            $notifications = loadJsonData('notifications');
            
            // Calculate stats matching React patterns
            $totalUsers = count($users);
            $totalRequests = count($requests);
            $totalConcerns = count($concerns);
            $unreadNotifications = count(array_filter($notifications, function($n) {
                return !isset($n['read']) || !$n['read'];
            }));
            
            // Pending requests
            $pendingRequests = count(array_filter($requests, function($r) {
                return $r['status'] === 'pending';
            }));
            
            // Active concerns
            $activeConcerns = count(array_filter($concerns, function($c) {
                return in_array($c['status'], ['pending', 'in-progress']);
            }));
            
            // Recent activity (last 7 days)
            $weekAgo = date('Y-m-d', strtotime('-7 days'));
            $recentRequests = count(array_filter($requests, function($r) use ($weekAgo) {
                return $r['createdAt'] >= $weekAgo;
            }));
            
            return [
                'totalUsers' => $totalUsers,
                'totalRequests' => $totalRequests,
                'totalConcerns' => $totalConcerns,
                'pendingRequests' => $pendingRequests,
                'activeConcerns' => $activeConcerns,
                'unreadNotifications' => $unreadNotifications,
                'recentRequests' => $recentRequests,
                'lastUpdated' => date('c')
            ];
        }
    } catch (Exception $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
        return [
            'totalUsers' => 0,
            'totalRequests' => 0,
            'totalConcerns' => 0,
            'pendingRequests' => 0,
            'activeConcerns' => 0,
            'unreadNotifications' => 0,
            'recentRequests' => 0,
            'lastUpdated' => date('c')
        ];
    }
}

/**
 * Create notification (matching React notification system)
 * @param string $type
 * @param string $title
 * @param string $message
 * @param string $userId
 * @return bool
 */
function createNotification($type, $title, $message, $userId = null) {
    try {
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            return createDatabaseNotification($type, $title, $message, $userId);
        } else {
            $notifications = loadJsonData('notifications');
            
            $notification = [
                'id' => generateId(),
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'userId' => $userId,
                'read' => false,
                'timestamp' => date('c'),
                'createdAt' => date('c')
            ];
            
            $notifications[] = $notification;
            return saveJsonData('notifications', $notifications);
        }
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Mark notifications as read (matching React notification management)
 * @param array $notificationIds
 * @return bool
 */
function markNotificationsRead($notificationIds) {
    try {
        if (USE_DATABASE) {
            require_once __DIR__ . '/db_utils.php';
            return markNotificationsAsRead($notificationIds);
        } else {
            $notifications = loadJsonData('notifications');
            
            foreach ($notifications as &$notification) {
                if (in_array($notification['id'], $notificationIds)) {
                    $notification['read'] = true;
                    $notification['readAt'] = date('c');
                }
            }
            
            return saveJsonData('notifications', $notifications);
        }
    } catch (Exception $e) {
        error_log("Error marking notifications as read: " . $e->getMessage());
        return false;
    }
}

/**
 * Clean old data (maintenance function)
 * @return bool
 */
function cleanOldData() {
    try {
        // Clean old notifications (older than 30 days)
        $notifications = loadJsonData('notifications');
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        
        $notifications = array_filter($notifications, function($n) use ($thirtyDaysAgo) {
            return $n['createdAt'] >= $thirtyDaysAgo;
        });
        
        saveJsonData('notifications', $notifications);
        
        return true;
    } catch (Exception $e) {
        error_log("Error cleaning old data: " . $e->getMessage());
        return false;
    }
}

/**
 * Sanitize input data (security function)
 * @param mixed $data
 * @return mixed
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    if (is_string($data)) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Validate email format
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Format currency (Philippine Peso)
 * @param float $amount
 * @return string
 */
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

/**
 * Get script execution time (performance monitoring)
 * @return float
 */
function getExecutionTime() {
    return round((microtime(true) - SCRIPT_START) * 1000, 2); // in milliseconds
}

/**
 * Log performance metrics
 * @param string $action
 * @return void
 */
function logPerformance($action) {
    $executionTime = getExecutionTime();
    $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024; // in MB
    
    error_log("Performance [{$action}]: {$executionTime}ms, Memory: {$memoryUsage}MB");
}

/**
 * Initialize default data (matching React initial data setup)
 * @return bool
 */
function initializeDefaultData() {
    try {
        // Initialize users with default admin and sample user
        $users = loadJsonData('users');
        if (empty($users)) {
            $defaultUsers = [
                [
                    'email' => 'user@email.com',
                    'password' => 'user@password.com',
                    'name' => 'John Doe',
                    'role' => 'user',
                    'address' => '123 Main Street, Barangay Centro',
                    'phone' => '+63 912 345 6789',
                    'createdAt' => date('c')
                ]
            ];
            saveJsonData('users', $defaultUsers);
        }
        
        // Initialize empty arrays for other data types
        $dataTypes = ['requests', 'concerns', 'notifications', 'announcements'];
        foreach ($dataTypes as $type) {
            $data = loadJsonData($type);
            if (empty($data)) {
                saveJsonData($type, []);
            }
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Error initializing default data: " . $e->getMessage());
        return false;
    }
}

/**
 * Backup data (maintenance function)
 * @return bool
 */
function backupData() {
    try {
        $backupDir = __DIR__ . '/../data/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d_H-i-s');
        $dataTypes = ['users', 'requests', 'concerns', 'notifications', 'announcements'];
        
        foreach ($dataTypes as $type) {
            $data = loadJsonData($type);
            $backupFile = $backupDir . "/{$type}_{$timestamp}.json";
            file_put_contents($backupFile, json_encode($data, JSON_PRETTY_PRINT));
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Error backing up data: " . $e->getMessage());
        return false;
    }
}

// Initialize default data on first load
if (!defined('DATA_INITIALIZED')) {
    initializeDefaultData();
    define('DATA_INITIALIZED', true);
}

/**
 * Storage helper functions (matching App.tsx localStorage)
 * @param string $key
 * @param mixed $defaultValue
 * @return mixed
 */
function getStorageItem($key, $defaultValue = null) {
    // In PHP, we use session instead of localStorage
    return $_SESSION[$key] ?? $defaultValue;
}

/**
 * Set storage item
 * @param string $key
 * @param mixed $value
 * @return bool
 */
function setStorageItem($key, $value) {
    try {
        $_SESSION[$key] = $value;
        return true;
    } catch (Exception $e) {
        error_log("Error setting session key \"$key\": " . $e->getMessage());
        return false;
    }
}

/**
 * Remove storage item
 * @param string $key
 * @return bool
 */
function removeStorageItem($key) {
    try {
        unset($_SESSION[$key]);
        return true;
    } catch (Exception $e) {
        error_log("Error removing session key \"$key\": " . $e->getMessage());
        return false;
    }
}
?>