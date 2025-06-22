<?php
/**
 * Database Configuration
 * 
 * This file handles database connection using PDO and environment variables.
 * It includes error handling and connection management.
 */

class Database {
    private static $instance = null;
    private $connection = null;

    /**
     * Load environment variables from .env file
     */
    private function loadEnv() {
        $envFile = __DIR__ . '/../.env';
        
        if (!file_exists($envFile)) {
            throw new Exception('.env file not found');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse valid lines
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Remove quotes if present
                if (preg_match('/^(["\']).*\1$/', $value)) {
                    $value = substr($value, 1, -1);
                }
                
                putenv("$name=$value");
                $_ENV[$name] = $value;
            }
        }
    }

    /**
     * Get database configuration from environment
     */
    private function getConfig() {
        return [
            'driver' => getenv('DB_DRIVER') ?: 'mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'port' => getenv('DB_PORT') ?: '3306',
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
            'collation' => getenv('DB_COLLATION') ?: 'utf8mb4_unicode_ci',
            'prefix' => getenv('DB_PREFIX') ?: ''
        ];
    }

    /**
     * Constructor - Load environment and establish connection
     */
    private function __construct() {
        try {
            // Load environment variables
            $this->loadEnv();

            // Get configuration
            $config = $this->getConfig();

            // Validate required configuration
            if (empty($config['database']) || empty($config['username'])) {
                throw new Exception('Database configuration is incomplete');
            }

            // Build DSN
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
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
                $config['username'],
                $config['password'],
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

// Create and return database connection
try {
    $db = Database::getInstance()->getConnection();
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