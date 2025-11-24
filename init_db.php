<?php
/**
 * Database Initialization Script
 * Creates PostgreSQL tables if they don't exist
 */

require_once 'config.php';

try {
    // Connect to PostgreSQL database
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";user=" . DB_USER . ";password=" . DB_PASS;

    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to PostgreSQL database successfully.\n";

    // Load PostgreSQL schema
    $schemaPath = __DIR__ . '/database/schema_pgsql.sql';

    if (!file_exists($schemaPath)) {
        echo "Error: Schema file not found at $schemaPath\n";
        exit(1);
    }

    echo "Importing database schema...\n";

    $schema = file_get_contents($schemaPath);

    // Execute PostgreSQL schema
    $statements = array_filter(array_map('trim', explode(';', $schema)));

    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip errors for statements that might already exist
                if (!preg_match('/already exists|does not exist/i', $e->getMessage())) {
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
