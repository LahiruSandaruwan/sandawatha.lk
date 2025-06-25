<?php
/**
 * Database Configuration
 * 
 * This file handles database connection using PDO and environment variables.
 * It includes error handling and connection management.
 */

// Load environment variables
require_once __DIR__ . '/env.php';

class Database {
    private static $instance = null;
    private $connection = null;

    /**
     * Get database configuration from environment
     */
    private function getConfig() {
        return [
            'host' => env('DB_HOST', 'localhost'),
            'name' => env('DB_NAME', 'sandawatha'),
            'user' => env('DB_USER', 'root'),
            'pass' => env('DB_PASS', ''),
            'port' => env('DB_PORT', 3306),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci'
        ];
    }

    /**
     * Constructor - Load environment and establish connection
     */
    private function __construct() {
        try {
            // Get configuration
            $config = $this->getConfig();

            // Build DSN
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;port=%d;charset=%s",
                $config['host'],
                $config['name'],
                $config['port'],
                $config['charset']
            );

            // Set PDO options
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']} COLLATE {$config['collation']}"
            ];

            // Create PDO instance
            $this->connection = new PDO(
                $dsn,
                $config['user'],
                $config['pass'],
                $options
            );

        } catch (PDOException $e) {
            // Log error details to a secure location
            error_log(sprintf(
                "Database connection failed: %s\nFile: %s\nLine: %d",
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));

            // Throw a generic error message for security
            throw new Exception('Database connection failed. Please try again later.');
        }
    }

    /**
     * Get database instance (Singleton pattern)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get database connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollBack() {
        return $this->connection->rollBack();
    }

    /**
     * Prevent cloning of the instance (Singleton pattern)
     */
    private function __clone() {}

    /**
     * Prevent unserialize of the instance (Singleton pattern)
     */
    public function __wakeup() {}
}

/**
 * Get database connection
 * 
 * @return PDO
 * @throws PDOException
 */
function getConnection() {
    $db = Database::getInstance()->getConnection();
    return $db;
}

/**
 * Execute a query and return all results
 * 
 * @param string $query The SQL query
 * @param array $params Query parameters
 * @return array The query results
 */
function query($query, $params = []) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        handleDatabaseError($e);
    }
}

/**
 * Execute a query and return first result
 * 
 * @param string $query The SQL query
 * @param array $params Query parameters
 * @return array|null The first result or null
 */
function queryOne($query, $params = []) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        handleDatabaseError($e);
    }
}

/**
 * Execute a query and return the last insert ID
 * 
 * @param string $query The SQL query
 * @param array $params Query parameters
 * @return int The last insert ID
 */
function insert($query, $params = []) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        handleDatabaseError($e);
    }
}

/**
 * Execute a query and return number of affected rows
 * 
 * @param string $query The SQL query
 * @param array $params Query parameters
 * @return int Number of affected rows
 */
function execute($query, $params = []) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        handleDatabaseError($e);
    }
}

/**
 * Begin a transaction
 * 
 * @return bool
 */
function beginTransaction() {
    try {
        $pdo = getConnection();
        return $pdo->beginTransaction();
    } catch (PDOException $e) {
        handleDatabaseError($e);
    }
}

/**
 * Commit a transaction
 * 
 * @return bool
 */
function commitTransaction() {
    try {
        $pdo = getConnection();
        return $pdo->commit();
    } catch (PDOException $e) {
        handleDatabaseError($e);
    }
}

/**
 * Rollback a transaction
 * 
 * @return bool
 */
function rollbackTransaction() {
    try {
        $pdo = getConnection();
        return $pdo->rollBack();
    } catch (PDOException $e) {
        handleDatabaseError($e);
    }
}

/**
 * Handle database errors
 * 
 * @param PDOException $e The exception
 * @throws PDOException
 */
function handleDatabaseError($e) {
    // Log error
    error_log("Database error: " . $e->getMessage());
    
    // Throw exception in debug mode
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        throw $e;
    }
    
    // Show generic error in production
    throw new PDOException('Database error occurred. Please try again later.');
}

/**
 * Escape string for use in LIKE clause
 * 
 * @param string $string The string to escape
 * @return string The escaped string
 */
function escapeLike($string) {
    return str_replace(['%', '_'], ['\%', '\_'], $string);
}

/**
 * Build WHERE clause from conditions
 * 
 * @param array $conditions The conditions
 * @return array [clause, params]
 */
function buildWhereClause($conditions) {
    $where = [];
    $params = [];
    
    foreach ($conditions as $column => $value) {
        if (is_null($value)) {
            $where[] = "`$column` IS NULL";
        } else {
            $where[] = "`$column` = ?";
            $params[] = $value;
        }
    }
    
    return [
        implode(' AND ', $where),
        $params
    ];
}

/**
 * Build SET clause from data
 * 
 * @param array $data The data
 * @return array [clause, params]
 */
function buildSetClause($data) {
    $set = [];
    $params = [];
    
    foreach ($data as $column => $value) {
        if (is_null($value)) {
            $set[] = "`$column` = NULL";
        } else {
            $set[] = "`$column` = ?";
            $params[] = $value;
        }
    }
    
    return [
        implode(', ', $set),
        $params
    ];
}

// Create and return database connection
try {
    $db = getConnection();
} catch (Exception $e) {
    // Handle connection error
    if (php_sapi_name() === 'cli') {
        // CLI output
        die("Error: " . $e->getMessage() . PHP_EOL);
    } else {
        // Web output
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 300'); // 5 minutes
        
        if (stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            // JSON response
            header('Content-Type: application/json');
            die(json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]));
        } else {
            // HTML response
            die("<!DOCTYPE html>
                <html>
                <head>
                    <title>Database Error</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; }
                    </style>
                </head>
                <body>
                    <div class='error'>" . htmlspecialchars($e->getMessage()) . "</div>
                </body>
                </html>");
        }
    }
}

// Example .env file structure:
/*
# Database Configuration
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sandawatha
DB_USERNAME=your_username
DB_PASSWORD=your_password
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
DB_PREFIX=
*/

return $db;
?> 