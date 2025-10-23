<?php
/**
 * Migration script to split existing full names into separate fields
 * This script handles both JSON file storage and database storage
 */

require_once 'config.php';
require_once 'functions/utils.php';

function splitName($fullName) {
    $nameParts = explode(' ', trim($fullName));
    
    if (count($nameParts) == 1) {
        // Only one name provided, treat as first name
        return [
            'first_name' => $nameParts[0],
            'middle_name' => '',
            'last_name' => $nameParts[0]
        ];
    } elseif (count($nameParts) == 2) {
        // Two names: first and last
        return [
            'first_name' => $nameParts[0],
            'middle_name' => '',
            'last_name' => $nameParts[1]
        ];
    } else {
        // Three or more names: first, middle(s), last
        $first = $nameParts[0];
        $last = end($nameParts);
        $middle = implode(' ', array_slice($nameParts, 1, -1));
        
        return [
            'first_name' => $first,
            'middle_name' => $middle,
            'last_name' => $last
        ];
    }
}

function migrateJsonUsers() {
    echo "Migrating JSON users...\n";
    
    $users = loadJsonData('users');
    $updated = false;
    
    foreach ($users as &$user) {
        if (isset($user['name']) && !isset($user['first_name'])) {
            $nameParts = splitName($user['name']);
            $user['first_name'] = $nameParts['first_name'];
            $user['middle_name'] = $nameParts['middle_name'];
            $user['last_name'] = $nameParts['last_name'];
            $updated = true;
            echo "Updated user: {$user['email']}\n";
        }
    }
    
    if ($updated) {
        saveJsonData('users', $users);
        echo "JSON users migration completed.\n";
    } else {
        echo "No JSON users needed migration.\n";
    }
}

function migrateDatabaseUsers() {
    if (!USE_DATABASE) {
        echo "Database not enabled, skipping database migration.\n";
        return;
    }
    
    echo "Migrating database users...\n";
    
    require_once 'functions/db_utils.php';
    
    try {
        $db = getDB();
        
        // Check if the new columns exist
        $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'first_name'");
        if ($stmt->rowCount() == 0) {
            echo "Adding new name columns to users table...\n";
            
            // Add the new columns
            $db->query("ALTER TABLE users ADD COLUMN first_name VARCHAR(255) AFTER password");
            $db->query("ALTER TABLE users ADD COLUMN middle_name VARCHAR(255) AFTER first_name");
            $db->query("ALTER TABLE users ADD COLUMN last_name VARCHAR(255) AFTER middle_name");
            
            echo "New columns added successfully.\n";
        }
        
        // Get users with old name format
        $stmt = $db->query("SELECT id, full_name FROM users WHERE first_name IS NULL OR first_name = ''");
        $users = $stmt->fetchAll();
        
        foreach ($users as $user) {
            $nameParts = splitName($user['full_name']);
            
            $updateStmt = $db->query(
                "UPDATE users SET first_name = ?, middle_name = ?, last_name = ? WHERE id = ?",
                [
                    $nameParts['first_name'],
                    $nameParts['middle_name'],
                    $nameParts['last_name'],
                    $user['id']
                ]
            );
            
            if ($updateStmt->rowCount() > 0) {
                echo "Updated database user ID: {$user['id']}\n";
            }
        }
        
        echo "Database users migration completed.\n";
        
    } catch (Exception $e) {
        echo "Database migration error: " . $e->getMessage() . "\n";
    }
}

// Run migrations
echo "Starting name field migration...\n\n";

migrateJsonUsers();
echo "\n";
migrateDatabaseUsers();

echo "\nMigration completed!\n";
echo "All existing users now have separate first_name, middle_name, and last_name fields.\n";
echo "The 'name' field is kept for backward compatibility.\n";
?>

