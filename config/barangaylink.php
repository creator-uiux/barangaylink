<?php

return [
    /*
    |--------------------------------------------------------------------------
    | BarangayLink Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings specific to the BarangayLink
    | application, including admin credentials, document types, and other
    | barangay-specific settings.
    |
    */

    'admin_email' => env('ADMIN_EMAIL', 'admin@barangaylink.gov.ph'),
    'admin_password' => env('ADMIN_PASSWORD', 'Admin@123456'),

    'document_types' => [
        'barangay_clearance' => [
            'name' => 'Barangay Clearance',
            'description' => 'Certificate of good moral standing in the community',
            'requirements' => ['Valid ID', 'Proof of Residency'],
            'fee' => 50,
            'processing_time' => '3-5 business days'
        ],
        'certificate_of_residency' => [
            'name' => 'Certificate of Residency',
            'description' => 'Certificate confirming residency in the barangay',
            'requirements' => ['Valid ID', 'Proof of Address'],
            'fee' => 30,
            'processing_time' => '1-2 business days'
        ],
        'certificate_of_indigency' => [
            'name' => 'Certificate of Indigency',
            'description' => 'Certificate for low-income families',
            'requirements' => ['Valid ID', 'Income Statement'],
            'fee' => 0,
            'processing_time' => '3-5 business days'
        ],
        'business_permit' => [
            'name' => 'Business Permit',
            'description' => 'Permit for business operations within the barangay',
            'requirements' => ['Business Registration', 'Valid ID', 'Proof of Address'],
            'fee' => 200,
            'processing_time' => '5-7 business days'
        ],
    ],

    'concern_categories' => [
        'infrastructure' => 'Infrastructure & Facilities',
        'environment' => 'Environment & Sanitation',
        'security' => 'Security & Safety',
        'health' => 'Health & Medical',
        'education' => 'Education & Youth',
        'social' => 'Social Services',
        'other' => 'Other Concerns',
    ],

    'user_roles' => [
        'admin' => [
            'name' => 'Administrator',
            'permissions' => ['all'],
            'dashboard' => 'admin-dashboard'
        ],
        'official' => [
            'name' => 'Barangay Official',
            'permissions' => ['view_reports', 'manage_documents', 'manage_announcements'],
            'dashboard' => 'official-dashboard'
        ],
        'resident' => [
            'name' => 'Resident',
            'permissions' => ['view_own_data', 'submit_requests', 'submit_concerns'],
            'dashboard' => 'user-dashboard'
        ],
    ],

    'emergency_contacts' => [
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
        ],
    ],

    'api' => [
        'weather' => [
            'base_url' => 'https://api.open-meteo.com/v1/forecast',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'params' => [
                'current' => ['temperature_2m', 'relative_humidity_2m', 'wind_speed_10m', 'weather_code'],
                'timezone' => 'Asia/Singapore'
            ]
        ]
    ],

    'settings' => [
        'app_name' => env('APP_NAME', 'BarangayLink'),
        'app_version' => '1.0.0',
        'timezone' => env('DEFAULT_TIMEZONE', 'Asia/Manila'),
        'enable_registration' => env('ENABLE_REGISTRATION', true),
        'enable_file_uploads' => env('ENABLE_FILE_UPLOADS', true),
        'max_file_size' => env('MAX_FILE_SIZE', 5 * 1024 * 1024), // 5MB
        'auto_refresh_interval' => env('AUTO_REFRESH_INTERVAL', 30000), // 30 seconds
        'notification_timeout' => env('NOTIFICATION_TIMEOUT', 5000), // 5 seconds
    ],

    'allowed_file_types' => ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx'],
];
