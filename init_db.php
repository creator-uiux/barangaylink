<?php
/**
 * Database Initialization Script
 * Creates SQLite database and tables if they don't exist
 */

require_once 'config.php';

try {
    // Ensure database directory exists
    $dbDir = dirname(DB_PATH);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }

    // Check if database file exists
    $dbExists = file_exists(DB_PATH);

    if (!$dbExists) {
        echo "Creating SQLite database...\n";
    } else {
        echo "Database already exists.\n";
    }

    // Connect to SQLite database (creates file if it doesn't exist)
    $pdo = new PDO("sqlite:" . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Load and execute schema
    $schemaPath = __DIR__ . '/database/schema_sqlite.sql';

    if (!file_exists($schemaPath)) {
        echo "Error: Schema file not found at $schemaPath\n";
        exit(1);
    }

    echo "Importing database schema...\n";

    $schema = file_get_contents($schemaPath);

    // Execute schema
    $pdo->exec($schema);

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
