<?php
/**
 * BarangayLink Configuration - PHP Version
 * Main configuration settings for the PHP application
 * SYNCHRONIZED with config.ts
 */

// Prevent direct access
if (!defined('BARANGAYLINK_ROOT')) {
    define('BARANGAYLINK_ROOT', dirname(__FILE__));
}

// Application Configuration
define('APP_NAME', 'BarangayLink');
define('APP_VERSION', '1.0.0');
define('APP_ENVIRONMENT', getenv('APP_ENVIRONMENT') ?: 'development');
define('DEFAULT_TIMEZONE', getenv('DEFAULT_TIMEZONE') ?: 'Asia/Manila');

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Database Configuration
define('DB_TYPE', 'sqlite');
define('DB_PATH', __DIR__ . '/database/barangaylink.db');
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'barangaylink');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Admin Credentials (hardcoded for demo - SAME as config.ts)
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'admin@barangaylink.gov.ph');
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'Admin@123456');

// Application Settings
define('ENABLE_REGISTRATION', true);
define('ENABLE_FILE_UPLOADS', true);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('AUTO_REFRESH_INTERVAL', 30000); // 30 seconds
define('NOTIFICATION_TIMEOUT', 5000); // 5 seconds

$ALLOWED_FILE_TYPES = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx'];

// Document Types Configuration - EXACT MATCH with config.ts
$DOCUMENT_TYPES = [
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
];

// Concern Categories Configuration - EXACT MATCH with config.ts
$CONCERN_CATEGORIES = [
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
];

// User Roles Configuration - EXACT MATCH with config.ts
$USER_ROLES = [
    'admin' => [
        'name' => 'Administrator',
        'permissions' => ['all'],
        'dashboard' => 'admin-dashboard'
    ],
    'user' => [
        'name' => 'Resident',
        'permissions' => ['view_own_data', 'submit_requests', 'submit_concerns'],
        'dashboard' => 'user-dashboard'
    ],
    'resident' => [
        'name' => 'Resident',
        'permissions' => ['view_own_data', 'submit_requests', 'submit_concerns'],
        'dashboard' => 'user-dashboard'
    ]
];

// Emergency Contact Numbers - EXACT MATCH with config.ts
$EMERGENCY_CONTACTS = [
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
];

// API Configuration - EXACT MATCH with config.ts
$API_CONFIG = [
    'weather' => [
        'baseUrl' => 'https://api.open-meteo.com/v1/forecast',
        'latitude' => 14.5995,
        'longitude' => 120.9842,
        'params' => [
            'current' => ['temperature_2m', 'relative_humidity_2m', 'wind_speed_10m', 'weather_code'],
            'timezone' => 'Asia/Singapore'
        ]
    ]
];

// Session Configuration - MUST BE CALLED BEFORE session_start()
// These will be set in index.php BEFORE session_start()

// Error Reporting (disable in production)
if (APP_ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Helper Functions
function getDocumentTypes() {
    global $DOCUMENT_TYPES;
    return $DOCUMENT_TYPES;
}

function getConcernCategories() {
    global $CONCERN_CATEGORIES;
    return $CONCERN_CATEGORIES;
}

function getUserRoles() {
    global $USER_ROLES;
    return $USER_ROLES;
}

function getEmergencyContacts() {
    global $EMERGENCY_CONTACTS;
    return $EMERGENCY_CONTACTS;
}

function getApiConfig() {
    global $API_CONFIG;
    return $API_CONFIG;
}