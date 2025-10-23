<?php
/**
 * Database Configuration for BarangayLink
 * MySQL Database Connection Settings
 */

// Database connection settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'barangaylink_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Database connection options
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);

// Connection string
define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET);

/**
 * Get database connection
 * @return PDO
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed');
        }
    }
    
    return $pdo;
}

/**
 * Test database connection
 * @return bool
 */
function testDatabaseConnection() {
    try {
        $pdo = getDB();
        $stmt = $pdo->query('SELECT 1');
        return $stmt !== false;
    } catch (Exception $e) {
        error_log('Database test failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Initialize database tables
 * @return bool
 */
function initializeDatabase() {
    try {
        $pdo = getDB();
        
        // Read and execute schema file
        $schemaFile = __DIR__ . '/../database_schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
        }
        
        return true;
    } catch (Exception $e) {
        error_log('Database initialization failed: ' . $e->getMessage());
        return false;
    }
}
?>