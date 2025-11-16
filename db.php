<?php
/**
 * Database Connection Handler
 * Provides BOTH PDO and MySQLi connections for compatibility
 */

require_once 'config.php';

// MySQLi Connection (for legacy code)
function getDBConnection() {
    static $mysqli = null;
    
    if ($mysqli === null) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            error_log("MySQLi Connection Error: " . $mysqli->connect_error);
            // Check if we're in an API context (headers not yet sent for JSON)
            if (headers_sent() === false && strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'error' => 'Database connection failed. Please check your configuration.'
                ]);
                exit;
            }
            throw new Exception("Database connection failed: " . $mysqli->connect_error);
        }
        
        $mysqli->set_charset(DB_CHARSET);
    }
    
    return $mysqli;
}

// PDO Connection (for modern code)
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("PDO Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please check your configuration.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Helper function to get PDO database connection
function getDB() {
    return Database::getInstance()->getConnection();
}

// Helper function to execute query (PDO)
function executeQuery($sql, $params = []) {
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        throw new Exception("Database query failed");
    }
}

// Helper function to fetch all results (PDO)
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

// Helper function to fetch single row (PDO)
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

// Helper function to get last insert ID (PDO)
function getLastInsertId() {
    return getDB()->lastInsertId();
}

// Helper function to begin transaction (PDO)
function beginTransaction() {
    return getDB()->beginTransaction();
}

// Helper function to commit transaction (PDO)
function commitTransaction() {
    return getDB()->commit();
}

// Helper function to rollback transaction (PDO)
function rollbackTransaction() {
    return getDB()->rollBack();
}
?>
