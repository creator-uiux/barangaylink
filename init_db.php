<?php
/**
 * Database Initialization Script
 * Creates SQLite database and tables if they don't exist
 */

require_once 'config.php';

try {
    if (defined('DB_TYPE') && DB_TYPE === 'sqlite') {
        // SQLite initialization
        $pdo = new PDO("sqlite:" . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if tables exist
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        $tableExists = $stmt->fetch();

        if (!$tableExists) {
            echo "Creating SQLite database...\n";

            // Read and execute schema
            $schemaPath = __DIR__ . '/database/schema_sqlite.sql';
            if (file_exists($schemaPath)) {
                $schema = file_get_contents($schemaPath);
                $pdo->exec($schema);
                echo "Database schema created successfully!\n";
            } else {
                echo "Error: SQLite schema file not found at $schemaPath\n";
                exit(1);
            }

            echo "Database initialized successfully!\n";
        } else {
            echo "Database already exists.\n";
        }

        // Test connection
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Database connection successful. Users table has {$result['count']} records.\n";
    } else {
        // MySQL initialization
        $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
        $dbExists = $stmt->fetch();

        if (!$dbExists) {
            echo "Creating MySQL database...\n";

            // Create database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "Database created successfully!\n";

            // Select the database
            $pdo->exec("USE `" . DB_NAME . "`");

            // Read and execute schema
            $schemaPath = __DIR__ . '/database/schema.sql';
            if (file_exists($schemaPath)) {
                $schema = file_get_contents($schemaPath);
                // Remove the database creation and use statements from schema
                $schema = preg_replace('/SET SQL_MODE.*;/', '', $schema);
                $schema = preg_replace('/START TRANSACTION;/', '', $schema);
                $schema = preg_replace('/COMMIT;/', '', $schema);
                $schema = preg_replace('/DROP DATABASE.*;/', '', $schema);
                $schema = preg_replace('/CREATE DATABASE.*;/', '', $schema);
                $schema = preg_replace('/USE.*;/', '', $schema);
                $pdo->exec($schema);
                echo "Database schema created successfully!\n";
            } else {
                echo "Error: Schema file not found at $schemaPath\n";
                exit(1);
            }

            echo "Database initialized successfully!\n";
        } else {
            echo "Database already exists.\n";
            // Select the database
            $pdo->exec("USE `" . DB_NAME . "`");
        }

        // Test connection
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Database connection successful. Users table has {$result['count']} records.\n";
    }

} catch (PDOException $e) {
    echo "Database initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
