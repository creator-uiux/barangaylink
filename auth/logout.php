<?php
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    
    // Log activity
    logActivity($userId, 'logout', 'User logged out');
    
    // Destroy session
    session_destroy();
}

// Redirect to landing page
header('Location: /index.php');
exit;
?>