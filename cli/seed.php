#!/usr/bin/env php
<?php
/**
 * Database Seeder Command
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * Usage:
 * php cli/seed.php [--force] [--verbose]
 * 
 * Options:
 * --force   Force run seeders even if tables are not empty
 * --verbose Show detailed progress information
 */

// Ensure script is run from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

class SeederCommand
{
    private $pdo;
    private $seedersPath;
    private $force = false;
    private $verbose = false;
    private $seederOrder = [
        'ReligionsSeeder',    // Must run first as religions are referenced by castes
        'CastesSeeder',       // Depends on religions
        'DistrictsSeeder',    // Independent
        'AdminsSeeder',       // Independent
        'GiftsSeeder'         // Optional
    ];
    private $results = [
        'success' => [],
        'skipped' => [],
        'failed' => []
    ];

    public function __construct()
    {
        try {
            // Load database configuration
            require_once dirname(__DIR__) . '/config/database.php';
            $this->pdo = Database::getInstance()->getConnection();
            
            // Set paths
            $this->seedersPath = dirname(__DIR__) . '/database/seeders';
            
            // Parse command line arguments
            global $argv;
            $this->force = in_array('--force', $argv);
            $this->verbose = in_array('--verbose', $argv);
            
            // Enable error reporting
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
        } catch (Exception $e) {
            $this->error("Failed to initialize seeder: " . $e->getMessage());
            exit(1);
        }
    }

    /**
     * Log a message if verbose mode is enabled
     */
    private function log($message)
    {
        if ($this->verbose) {
            echo $message . "\n";
        }
    }

    /**
     * Log an error message
     */
    private function error($message)
    {
        echo "\033[31m✗ Error: " . $message . "\033[0m\n";
    }

    /**
     * Log a success message
     */
    private function success($message)
    {
        echo "\033[32m✓ " . $message . "\033[0m\n";
    }

    /**
     * Log a warning/skipped message
     */
    private function warning($message)
    {
        echo "\033[33m⚠ " . $message . "\033[0m\n";
    }

    /**
     * Check if table exists
     */
    private function tableExists($table)
    {
        try {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '{$table}'");
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if table is empty
     */
    private function isTableEmpty($table)
    {
        try {
            $stmt = $this->pdo->query("SELECT 1 FROM {$table} LIMIT 1");
            return $stmt->fetch() === false;
        } catch (Exception $e) {
            $this->error("Failed to check if table {$table} is empty: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get table name from seeder class name
     */
    private function getTableName($seederName)
    {
        // Convert CamelCase to snake_case and remove 'Seeder'
        $table = preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Seeder', '', $seederName));
        return strtolower($table);
    }

    /**
     * Run all seeders
     */
    public function run()
    {
        echo "Starting database seeding...\n";
        echo "───────────────────────────\n\n";

        // Check if seeders directory exists
        if (!is_dir($this->seedersPath)) {
            $this->error("Seeders directory not found: {$this->seedersPath}");
            exit(1);
        }

        try {
            // Disable foreign key checks if forcing
            if ($this->force) {
                $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            }

            // Begin transaction
            $this->pdo->beginTransaction();

            // Run seeders in specified order
            foreach ($this->seederOrder as $seederName) {
                $tableName = $this->getTableName($seederName);
                $seederFile = $this->seedersPath . '/' . $seederName . '.php';

                // Check if seeder file exists
                if (!file_exists($seederFile)) {
                    if ($seederName !== 'GiftsSeeder') { // GiftsSeeder is optional
                        throw new Exception("Seeder file not found: {$seederName}.php");
                    } else {
                        $this->log("Optional seeder not found: {$seederName}.php");
                        $this->results['skipped'][] = $seederName;
                        continue;
                    }
                }

                // Check if table exists
                if (!$this->tableExists($tableName)) {
                    throw new Exception("Table '{$tableName}' does not exist. Run migrations first.");
                }

                // Skip if table is not empty and --force is not used
                if (!$this->force && !$this->isTableEmpty($tableName)) {
                    $this->warning("Skipping {$seederName}: Table {$tableName} is not empty. Use --force to override.");
                    $this->results['skipped'][] = $seederName;
                    continue;
                }

                $this->log("Running seeder: {$seederName}");

                // Clear table if --force is used
                if ($this->force) {
                    $this->log("Clearing table: {$tableName}");
                    $this->pdo->exec("TRUNCATE TABLE {$tableName}");
                }

                // Include and instantiate seeder class
                require_once $seederFile;
                $seeder = new $seederName();

                // Run seeder
                if (!$seeder->run($this->pdo)) {
                    throw new Exception("Seeder {$seederName} failed");
                }

                $this->success("Seeding completed: {$seederName}");
                $this->results['success'][] = $seederName;
            }

            // Commit transaction
            if ($this->pdo->inTransaction()) {
                $this->pdo->commit();
            }

            // Re-enable foreign key checks if forcing
            if ($this->force) {
                $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            }

            // Print results
            $this->printResults();
            exit(empty($this->results['failed']) ? 0 : 1);

        } catch (Exception $e) {
            // Rollback transaction
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            // Re-enable foreign key checks if forcing
            if ($this->force) {
                $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            }

            $this->error($e->getMessage());
            $this->results['failed'][] = $seederName ?? 'Unknown';
            $this->printResults();
            exit(1);
        }
    }

    /**
     * Print final results
     */
    private function printResults()
    {
        echo "\nSeeding Results:\n";
        echo "───────────────\n";
        
        if (!empty($this->results['success'])) {
            echo "\033[32m✓ Successfully seeded (" . count($this->results['success']) . "):\033[0m\n";
            foreach ($this->results['success'] as $seeder) {
                echo "  • {$seeder}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->results['skipped'])) {
            echo "\033[33m⚠ Skipped (" . count($this->results['skipped']) . "):\033[0m\n";
            foreach ($this->results['skipped'] as $seeder) {
                echo "  • {$seeder}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->results['failed'])) {
            echo "\033[31m✗ Failed (" . count($this->results['failed']) . "):\033[0m\n";
            foreach ($this->results['failed'] as $seeder) {
                echo "  • {$seeder}\n";
            }
            echo "\n";
            return false;
        }
        
        return empty($this->results['failed']);
    }
}

// Run seeders
$command = new SeederCommand();
$command->run();