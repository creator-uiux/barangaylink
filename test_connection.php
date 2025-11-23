<?php
/**
 * Test script to reproduce the database connection error
 */

require_once 'config.php';
require_once 'db.php';

try {
    echo "Testing database connection...\n";
    $db = getDB();
    echo "Connection successful!\n";

    // Try a simple query
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Users table has " . $result['count'] . " records.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
