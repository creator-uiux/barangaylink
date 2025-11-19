<?php
/**
 * API Index - Main API Entry Point
 * Provides information about available API endpoints
 */

require_once '../init.php';

header('Content-Type: application/json');

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $apiInfo = [
        'name' => 'BarangayLink API',
        'version' => '1.0.0',
        'description' => 'REST API for BarangayLink digital governance platform',
        'endpoints' => [
            'GET /api/announcements' => 'Get all announcements',
            'POST /api/announcements' => 'Create new announcement (admin only)',
            'GET /api/events' => 'Get upcoming events',
            'POST /api/events' => 'Create new event (admin only)',
            'GET /api/emergency-alerts' => 'Get emergency alerts',
            'POST /api/emergency-alerts' => 'Create emergency alert (admin only)',
            'GET /api/concerns?action=list' => 'List concerns',
            'POST /api/concerns' => 'Create/submit concern',
            'GET /api/documents?action=list' => 'List documents',
            'POST /api/documents' => 'Create document request',
            'GET /api/notifications?action=list' => 'Get notifications',
            'POST /api/notifications' => 'Create notification',
            'GET /api/users?action=list' => 'List users (admin only)',
            'POST /api/users' => 'Create/update user'
        ],
        'authentication' => [
            'type' => 'Session-based',
            'login_endpoint' => 'POST /api/auth.php?action=login',
            'signup_endpoint' => 'POST /api/auth.php?action=signup'
        ],
        'status' => 'operational'
    ];

    jsonResponse($apiInfo);
} else {
    jsonResponse(['error' => 'Method not allowed'], 405);
}
