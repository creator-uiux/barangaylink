<?php
/**
 * BarangayLink Configuration - PHP Version
 * EXACT conversion from config.ts preserving ALL configuration values
 */

// Application Configuration
define('APP_CONFIG', [
    'name' => 'BarangayLink',
    'version' => '1.0.0',
    'environment' => 'development',
    'defaultTimezone' => 'Asia/Manila'
]);

// Admin Credentials (hardcoded for demo - same as React version)
define('ADMIN_CREDENTIALS', [
    'email' => 'admin@email.com',
    'password' => 'admin@password.com'
]);

// Application Settings
define('APP_SETTINGS', [
    'enableRegistration' => true,
    'enableFileUploads' => true,
    'maxFileSize' => 5 * 1024 * 1024, // 5MB
    'allowedFileTypes' => ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx'],
    'autoRefreshInterval' => 30000, // 30 seconds
    'notificationTimeout' => 5000 // 5 seconds
]);

// Document Types Configuration - EXACT match from React version
define('DOCUMENT_TYPES', [
    'barangay_clearance' => [
        'name' => 'Barangay Clearance',
        'description' => 'Certificate of good moral standing in the community',
        'requirements' => ['Valid ID', 'Proof of Residency'],
        'fee' => 50,
        'processingTime' => '3-5 business days'
    ],
    'certificate_of_residency' => [
        'name' => 'Certificate of Residency',
        'description' => 'Certificate confirming residency in the barangay',
        'requirements' => ['Valid ID', 'Proof of Address'],
        'fee' => 30,
        'processingTime' => '1-2 business days'
    ],
    'certificate_of_indigency' => [
        'name' => 'Certificate of Indigency',
        'description' => 'Certificate for low-income families',
        'requirements' => ['Valid ID', 'Income Statement'],
        'fee' => 0,
        'processingTime' => '3-5 business days'
    ],
    'business_permit' => [
        'name' => 'Business Permit',
        'description' => 'Permit to operate a business in the barangay',
        'requirements' => ['Valid ID', 'Business Plan', 'Location Clearance'],
        'fee' => 500,
        'processingTime' => '7-10 business days'
    ],
    'health_certificate' => [
        'name' => 'Health Certificate',
        'description' => 'Certificate of health status for various purposes',
        'requirements' => ['Valid ID', 'Medical Records'],
        'fee' => 100,
        'processingTime' => '2-3 business days'
    ]
]);

// Concern Categories Configuration - EXACT match from React version
define('CONCERN_CATEGORIES', [
    'infrastructure' => [
        'name' => 'Infrastructure',
        'description' => 'Roads, bridges, street lights, and public facilities',
        'priorityLevels' => ['low', 'medium', 'high', 'urgent']
    ],
    'sanitation' => [
        'name' => 'Sanitation',
        'description' => 'Garbage collection, waste management, cleanliness',
        'priorityLevels' => ['low', 'medium', 'high', 'urgent']
    ],
    'public_safety' => [
        'name' => 'Public Safety',
        'description' => 'Security, crime prevention, emergency response',
        'priorityLevels' => ['medium', 'high', 'urgent']
    ],
    'public_order' => [
        'name' => 'Public Order',
        'description' => 'Noise complaints, disturbances, violations',
        'priorityLevels' => ['low', 'medium', 'high']
    ],
    'health_services' => [
        'name' => 'Health Services',
        'description' => 'Healthcare facilities, medical assistance',
        'priorityLevels' => ['medium', 'high', 'urgent']
    ],
    'utilities' => [
        'name' => 'Utilities',
        'description' => 'Water, electricity, telecommunications',
        'priorityLevels' => ['low', 'medium', 'high', 'urgent']
    ],
    'others' => [
        'name' => 'Others',
        'description' => 'General concerns and inquiries',
        'priorityLevels' => ['low', 'medium', 'high']
    ]
]);

// User Roles Configuration - EXACT match from React version
define('USER_ROLES', [
    'admin' => [
        'name' => 'Administrator',
        'permissions' => ['all'],
        'dashboard' => 'admin-dashboard'
    ],
    'user' => [
        'name' => 'Resident',
        'permissions' => ['view_own_data', 'submit_requests', 'submit_concerns'],
        'dashboard' => 'user-dashboard'
    ]
]);

// Emergency Contact Numbers - EXACT match from React version
define('EMERGENCY_CONTACTS', [
    'barangay_hall' => [
        'name' => 'Barangay Hall',
        'number' => '(02) 123-4567',
        'description' => 'Main office for general inquiries'
    ],
    'emergency_hotline' => [
        'name' => 'Emergency Hotline',
        'number' => '911',
        'description' => 'For immediate emergency assistance'
    ],
    'police' => [
        'name' => 'Police Station',
        'number' => '117',
        'description' => 'Local police for security concerns'
    ],
    'fire_department' => [
        'name' => 'Fire Department',
        'number' => '116',
        'description' => 'Fire emergency response'
    ],
    'health_center' => [
        'name' => 'Health Center',
        'number' => '(02) 234-5678',
        'description' => 'Medical assistance and health services'
    ]
]);

// Storage Keys (PHP equivalent of localStorage keys)
define('STORAGE_KEYS', [
    'auth' => 'barangaylink_auth',
    'users' => 'barangaylink_users',
    'documentRequests' => 'barangaylink_document_requests',
    'concerns' => 'barangaylink_concerns',
    'notifications' => 'barangaylink_notifications',
    'announcements' => 'barangaylink_announcements'
]);

// Helper function to get config values
function getConfig($key) {
    switch ($key) {
        case 'APP_CONFIG':
            return APP_CONFIG;
        case 'ADMIN_CREDENTIALS':
            return ADMIN_CREDENTIALS;
        case 'APP_SETTINGS':
            return APP_SETTINGS;
        case 'DOCUMENT_TYPES':
            return DOCUMENT_TYPES;
        case 'CONCERN_CATEGORIES':
            return CONCERN_CATEGORIES;
        case 'USER_ROLES':
            return USER_ROLES;
        case 'EMERGENCY_CONTACTS':
            return EMERGENCY_CONTACTS;
        case 'STORAGE_KEYS':
            return STORAGE_KEYS;
        default:
            return null;
    }
}

// Set timezone
date_default_timezone_set(APP_CONFIG['defaultTimezone']);

// Debug mode (set to false in production)
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true);
}

// Error handling in development
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>