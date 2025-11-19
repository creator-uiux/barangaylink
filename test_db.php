<?php
/**
 * Database Testing Script
 * Tests all functions in db.php thoroughly
 */

require_once 'config.php';
require_once 'db.php';

echo "Starting database tests...\n\n";

// Test 1: Singleton Pattern
echo "Test 1: Singleton Pattern\n";
try {
    $db1 = Database::getInstance();
    $db2 = Database::getInstance();
    if ($db1 === $db2) {
        echo "✓ Singleton works: Same instance returned\n";
    } else {
        echo "✗ Singleton failed: Different instances returned\n";
    }
} catch (Exception $e) {
    echo "✗ Singleton test failed: " . $e->getMessage() . "\n";
}

// Test 2: Database Connection
echo "\nTest 2: Database Connection\n";
try {
    $conn = getDB();
    if ($conn instanceof PDO) {
        echo "✓ Connection successful: PDO instance returned\n";
    } else {
        echo "✗ Connection failed: Not a PDO instance\n";
    }
} catch (Exception $e) {
    echo "✗ Connection test failed: " . $e->getMessage() . "\n";
}

// Test 3: Execute Query (INSERT)
echo "\nTest 3: Execute Query (INSERT)\n";
try {
    // Insert a test user (assuming users table exists from schema)
    $result = executeQuery("INSERT INTO users (email, password, role, first_name, last_name, created_at) VALUES (?, ?, ?, ?, ?, ?)", [
        'test@example.com',
        password_hash('password123', PASSWORD_DEFAULT),
        'user',
        'Test',
        'User',
        date('Y-m-d H:i:s')
    ]);
    if ($result) {
        echo "✓ INSERT query executed successfully\n";
        $lastId = getLastInsertId();
        echo "✓ Last insert ID: $lastId\n";
    } else {
        echo "✗ INSERT query failed\n";
    }
} catch (Exception $e) {
    echo "✗ INSERT test failed: " . $e->getMessage() . "\n";
}

// Test 4: Fetch One
echo "\nTest 4: Fetch One\n";
try {
    $user = fetchOne("SELECT * FROM users WHERE email = ?", ['test@example.com']);
    if ($user && $user['email'] === 'test@example.com') {
        echo "✓ fetchOne successful: Retrieved user\n";
    } else {
        echo "✗ fetchOne failed: User not found or incorrect data\n";
    }
} catch (Exception $e) {
    echo "✗ fetchOne test failed: " . $e->getMessage() . "\n";
}

// Test 5: Fetch All
echo "\nTest 5: Fetch All\n";
try {
    $users = fetchAll("SELECT * FROM users LIMIT 5");
    if (is_array($users) && count($users) > 0) {
        echo "✓ fetchAll successful: Retrieved " . count($users) . " users\n";
    } else {
        echo "✗ fetchAll failed: No users found or not an array\n";
    }
} catch (Exception $e) {
    echo "✗ fetchAll test failed: " . $e->getMessage() . "\n";
}

// Test 6: Transactions
echo "\nTest 6: Transactions\n";
try {
    beginTransaction();
    echo "✓ Transaction started\n";

    // Insert another test user
    executeQuery("INSERT INTO users (email, password, role, first_name, last_name, created_at) VALUES (?, ?, ?, ?, ?, ?)", [
        'test2@example.com',
        password_hash('password123', PASSWORD_DEFAULT),
        'user',
        'Test2',
        'User2',
        date('Y-m-d H:i:s')
    ]);

    commitTransaction();
    echo "✓ Transaction committed\n";

    // Verify the insert
    $user = fetchOne("SELECT * FROM users WHERE email = ?", ['test2@example.com']);
    if ($user) {
        echo "✓ Transaction insert verified\n";
    } else {
        echo "✗ Transaction insert not found\n";
    }
} catch (Exception $e) {
    echo "✗ Transaction test failed: " . $e->getMessage() . "\n";
    try {
        rollbackTransaction();
        echo "✓ Transaction rolled back on error\n";
    } catch (Exception $rollbackE) {
        echo "✗ Rollback failed: " . $rollbackE->getMessage() . "\n";
    }
}

// Test 7: Error Handling
echo "\nTest 7: Error Handling\n";
try {
    // Invalid query to test error handling
    executeQuery("SELECT * FROM nonexistent_table");
    echo "✗ Error handling failed: No exception thrown for invalid query\n";
} catch (Exception $e) {
    echo "✓ Error handling successful: " . $e->getMessage() . "\n";
}

// Test 8: Parameterized Queries
echo "\nTest 8: Parameterized Queries\n";
try {
    $result = executeQuery("SELECT * FROM users WHERE email = ? AND role = ?", ['test@example.com', 'user']);
    $user = $result->fetch();
    if ($user) {
        echo "✓ Parameterized query successful\n";
    } else {
        echo "✗ Parameterized query failed: No result\n";
    }
} catch (Exception $e) {
    echo "✗ Parameterized query test failed: " . $e->getMessage() . "\n";
}

// Test 9: Multiple Connections (Singleton)
echo "\nTest 9: Multiple Connections (Singleton)\n";
try {
    $conn1 = getDB();
    $conn2 = getDB();
    if ($conn1 === $conn2) {
        echo "✓ Multiple getDB() calls return same connection\n";
    } else {
        echo "✗ Multiple getDB() calls return different connections\n";
    }
} catch (Exception $e) {
    echo "✗ Multiple connections test failed: " . $e->getMessage() . "\n";
}

// Clean up test data
echo "\nCleaning up test data...\n";
try {
    executeQuery("DELETE FROM users WHERE email IN (?, ?)", ['test@example.com', 'test2@example.com']);
    echo "✓ Test data cleaned up\n";
} catch (Exception $e) {
    echo "✗ Cleanup failed: " . $e->getMessage() . "\n";
}

echo "\nAll tests completed!\n";
?>
