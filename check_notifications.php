<?php
require_once 'db.php';

try {
    $db = getDB();
    $stmt = $db->query('SELECT * FROM notifications');
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Notifications in database:\n";
    print_r($results);

    // Check users table
    $stmt = $db->query('SELECT id, email, role FROM users');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nUsers in database:\n";
    print_r($users);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
