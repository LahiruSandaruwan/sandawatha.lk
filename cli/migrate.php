#!/usr/bin/env php
<?php
/**
 * Database Migration Command
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script runs database migrations in a safe and tracked manner.
 * Usage: php cli/migrate.php
 */

// Ensure script is run from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

// Define color codes for CLI output
define('COLOR_GREEN', "\033[32m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_RED', "\033[31m");
define('COLOR_RESET', "\033[0m");

class MigrationCommand
{
    private $pdo;
    private $projectRoot;
    private $migrationsPath;
    private $configPath;
    private $appliedMigrations = [];
    private $errors = [];
    private $verbose = false;

    public function __construct()
    {
        // Set paths
        $this->projectRoot = dirname(__DIR__);
        $this->migrationsPath = $this->projectRoot . '/database/migrations';
        $this->configPath = $this->projectRoot . '/config';

        // Check verbose flag
        global $argv;
        $this->verbose = in_array('--verbose', $argv) || in_array('-v', $argv);

        // Setup environment
        $this->checkEnvironment();
    }

    /**
     * Check and setup environment
     */
    private function checkEnvironment()
    {
        try {
            // Check and create required directories
            $this->createDirectoryIfNotExists($this->configPath);
            $this->createDirectoryIfNotExists($this->migrationsPath);

            // Check and create database config if not exists
            $this->checkDatabaseConfig();

            // Initialize database connection
            $this->initializeDatabaseConnection();

            // Ensure migrations table exists
            $this->ensureMigrationsTable();

            // Load applied migrations
            $this->loadAppliedMigrations();

        } catch (Exception $e) {
            $this->error("Environment setup failed: " . $e->getMessage());
            exit(1);
        }
    }

    /**
     * Create directory if it doesn't exist
     */
    private function createDirectoryIfNotExists($path)
    {
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true)) {
                throw new Exception("Failed to create directory: {$path}");
            }
            $this->log("Created directory: {$path}");
        }
    }

    /**
     * Check and create database config if not exists
     */
    private function checkDatabaseConfig()
    {
        $configFile = $this->configPath . '/database.php';
        
        if (!file_exists($configFile)) {
            $template = <<<'EOT'
<?php
/**
 * Database Configuration
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=sandawatha;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    return $pdo;
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
EOT;
            if (!file_put_contents($configFile, $template)) {
                throw new Exception("Failed to create database config file");
            }
            $this->log("Created database config file: {$configFile}");
        }
    }

    /**
     * Initialize database connection
     */
    private function initializeDatabaseConnection()
    {
        try {
            $this->pdo = require $this->configPath . '/database.php';
            $this->log("Database connection established");
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Ensure migrations table exists
     */
    private function ensureMigrationsTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                execution_time FLOAT NULL,
                status ENUM('success', 'failed') DEFAULT 'success'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->pdo->exec($sql);
            $this->log("Migrations table checked/created");
        } catch (PDOException $e) {
            throw new Exception("Failed to create migrations table: " . $e->getMessage());
        }
    }

    /**
     * Load already applied migrations
     */
    private function loadAppliedMigrations()
    {
        try {
            $stmt = $this->pdo->query("SELECT migration FROM migrations WHERE status = 'success'");
            $this->appliedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $this->log("Loaded " . count($this->appliedMigrations) . " applied migrations");
        } catch (PDOException $e) {
            throw new Exception("Failed to load applied migrations: " . $e->getMessage());
        }
    }

    /**
     * Get next batch number
     */
    private function getNextBatch()
    {
        try {
            $stmt = $this->pdo->query("SELECT MAX(batch) FROM migrations");
            return (int)$stmt->fetchColumn() + 1;
        } catch (PDOException $e) {
            throw new Exception("Failed to get next batch number: " . $e->getMessage());
        }
    }

    /**
     * Run all pending migrations
     */
    public function run()
    {
        try {
            $this->printHeader();

            // Get all migration files
            $files = glob($this->migrationsPath . '/*.php');
            if (empty($files)) {
                $this->warning("No migration files found in: {$this->migrationsPath}");
                return;
            }

            // Organize files by dependencies
            $dependencies = [
                'religions' => [],
                'castes' => ['religions'],
                'districts' => [],
                'users' => ['religions', 'castes', 'districts'],
                'blocks' => ['users'],
                'blog_posts' => ['users'],
                'gifts' => ['users'],
                'interests' => ['users'],
                'messages' => ['users'],
                'notifications' => ['users'],
                'referrals' => ['users'],
                'reports' => ['users'],
                'subscriptions' => ['users'],
                'user_horoscope' => ['users'],
                'user_photos' => ['users'],
                'user_preferences' => ['users'],
                'verifications' => ['users'],
                'admins' => []
            ];

            // Helper function to get table name from filename
            $getTableName = function($filename) {
                if (preg_match('/_create_(.+)_table\.php$/', $filename, $matches)) {
                    return str_replace(['_', '-'], '', $matches[1]);
                }
                return null;
            };

            // Sort files based on dependencies
            usort($files, function($a, $b) use ($dependencies, $getTableName) {
                $aTable = $getTableName(basename($a));
                $bTable = $getTableName(basename($b));

                // If either file doesn't match our naming pattern, maintain original order
                if (!$aTable || !$bTable) {
                    return strcmp(basename($a), basename($b));
                }

                // If B depends on A, A should come first
                if (in_array($aTable, $dependencies[$bTable] ?? [])) {
                    return -1;
                }
                // If A depends on B, B should come first
                if (in_array($bTable, $dependencies[$aTable] ?? [])) {
                    return 1;
                }
                // Otherwise maintain timestamp order
                return strcmp(basename($a), basename($b));
            });

            $count = 0;
            $startTime = microtime(true);
            $batch = $this->getNextBatch();

            foreach ($files as $file) {
                $migrationName = basename($file);
                
                // Skip if already applied
                if (in_array($migrationName, $this->appliedMigrations)) {
                    continue;
                }

                $this->info("\nRunning migration: " . $migrationName);

                try {
                    // Include the migration file
                    require_once $file;
                    
                    // Get the migration class name from the file name
                    $baseName = basename($file, '.php');
                    $parts = explode('_', $baseName);
                    $timestamp = implode('_', array_slice($parts, 0, 4));
                    $tableName = implode('', array_map('ucfirst', array_slice($parts, 4)));
                    $className = 'Migration_' . $timestamp . '_' . $tableName;
                    
                    if (!class_exists($className)) {
                        throw new Exception("Migration class {$className} not found in {$migrationName}");
                    }

                    // Create migration instance
                    $migration = new $className();
                    
                    // Start transaction
                    if (!$this->pdo->inTransaction()) {
                        $this->pdo->beginTransaction();
                    }

                    // Run the migration
                    $migration->up($this->pdo);

                    // Record the migration
                    $stmt = $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                    $stmt->execute([$migrationName, $batch]);

                    // Commit transaction
                    if ($this->pdo->inTransaction()) {
                        $this->pdo->commit();
                    }

                    $this->success("✓ Migration successful");
                    $count++;

                } catch (Exception $e) {
                    // Rollback transaction if active
                    if ($this->pdo->inTransaction()) {
                        $this->pdo->rollBack();
                    }
                    $this->error("✗ Error in {$migrationName}: " . $e->getMessage());
                    $this->errors[] = $migrationName;
                }
            }

            $totalTime = microtime(true) - $startTime;
            $this->printSummary($count, $totalTime);

        } catch (Exception $e) {
            $this->error("Migration failed: " . $e->getMessage());
            exit(1);
        }
    }

    /**
     * Print script header
     */
    private function printHeader()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║            Sandawatha.lk Database Migrations              ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
    }

    /**
     * Print final summary
     */
    private function printSummary($count, $totalTime)
    {
        echo "\nMigration Summary:\n";
        echo "----------------\n";
        echo "Total migrations run: " . $count . "\n";
        echo "Successful: " . ($count - count($this->errors)) . "\n";
        echo "Failed: " . count($this->errors) . "\n";
        echo sprintf("Total time: %.4fs\n", $totalTime);

        if (!empty($this->errors)) {
            echo "\nFailed Migrations:\n";
            foreach ($this->errors as $migration) {
                echo "  • {$migration}\n";
            }
            exit(1);
        }
    }

    /**
     * Log message if verbose mode is on
     */
    private function log($message)
    {
        if ($this->verbose) {
            echo "LOG: {$message}\n";
        }
    }

    /**
     * Print success message
     */
    private function success($message)
    {
        echo COLOR_GREEN . $message . COLOR_RESET . "\n";
    }

    /**
     * Print warning message
     */
    private function warning($message)
    {
        echo COLOR_YELLOW . $message . COLOR_RESET . "\n";
    }

    /**
     * Print error message
     */
    private function error($message)
    {
        echo COLOR_RED . $message . COLOR_RESET . "\n";
    }

    /**
     * Print info message
     */
    private function info($message)
    {
        echo $message . "\n";
    }
}

// Run migrations
$command = new MigrationCommand();
$command->run();