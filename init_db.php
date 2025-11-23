<?php
/**
 * Database Initialization Script
 * Creates MySQL database and tables if they don't exist
 */

require_once 'config.php';

try {
    // Connect to MySQL server (without specifying database)
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Connect to the specific database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL database successfully.\n";

    // Load and execute schema
    $schemaPath = __DIR__ . '/database/schema.sql';

    if (!file_exists($schemaPath)) {
        echo "Error: Schema file not found at $schemaPath\n";
        exit(1);
    }

    echo "Importing database schema...\n";

    $schema = file_get_contents($schemaPath);

    // Split schema into individual statements and execute them
    $statements = array_filter(array_map('trim', explode(';', $schema)));

    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip errors for statements that might already exist (like CREATE DATABASE, USE, etc.)
                if (!preg_match('/already exists|database exists/i', $e->getMessage())) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    echo "Database schema imported successfully!\n";

    // Test database connection
    $stmt = $pdo->query("SELECT COUNT(*) AS count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Database connection successful. Users table has {$result['count']} records.\n";

} catch (PDOException $e) {
    echo "Database initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
