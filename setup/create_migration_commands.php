<?php
/**
 * Migration Commands Setup Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script creates CLI commands for managing database migrations and seeders.
 * Run this script from the project root directory.
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

class MigrationCommandsSetup
{
    private $projectRoot;
    private $cliPath;
    private $created = [];
    private $existing = [];
    private $errors = [];

    // Define required CLI commands
    private $requiredCommands = [
        'migrate.php' => 'migration',
        'rollback.php' => 'rollback',
        'seed.php' => 'seeder'
    ];

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__);
        $this->cliPath = $this->projectRoot . '/cli';
        
        // Create CLI directory if it doesn't exist
        if (!is_dir($this->cliPath)) {
            mkdir($this->cliPath, 0755, true);
        }
    }

    /**
     * Run the setup process
     */
    public function run()
    {
        $this->printHeader();
        $this->createCommands();
        $this->printReport();
    }

    /**
     * Create command files
     */
    private function createCommands()
    {
        foreach ($this->requiredCommands as $file => $type) {
            $path = "{$this->cliPath}/{$file}";
            
            if (!file_exists($path)) {
                $this->createCommandFile($file, $type);
            } else {
                $this->existing[] = "cli/{$file}";
            }
        }
    }

    /**
     * Create a command file
     */
    private function createCommandFile($filename, $type)
    {
        $path = "{$this->cliPath}/{$filename}";
        $template = $this->getCommandTemplate($type);

        if (file_put_contents($path, $template)) {
            chmod($path, 0755); // Make file executable
            $this->created[] = "cli/{$filename}";
        } else {
            $this->errors[] = "Failed to create command: {$filename}";
        }
    }

    /**
     * Get command file template
     */
    private function getCommandTemplate($type)
    {
        switch ($type) {
            case 'migration':
                return $this->getMigrationCommandTemplate();
            case 'rollback':
                return $this->getRollbackCommandTemplate();
            case 'seeder':
                return $this->getSeederCommandTemplate();
            default:
                return '';
        }
    }

    /**
     * Get migration command template
     */
    private function getMigrationCommandTemplate()
    {
        return <<<'EOT'
#!/usr/bin/env php
<?php
/**
 * Database Migration Command
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

// Ensure script is run from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

class MigrationCommand
{
    private $pdo;
    private $migrationsPath;
    private $appliedMigrations = [];

    public function __construct()
    {
        $this->pdo = require dirname(__DIR__) . '/config/database.php';
        $this->migrationsPath = dirname(__DIR__) . '/database/migrations';
        $this->ensureMigrationsTable();
        $this->loadAppliedMigrations();
    }

    /**
     * Ensure migrations table exists
     */
    private function ensureMigrationsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
    }

    /**
     * Load already applied migrations
     */
    private function loadAppliedMigrations()
    {
        $stmt = $this->pdo->query("SELECT migration FROM migrations");
        $this->appliedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get next batch number
     */
    private function getNextBatch()
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM migrations");
        return (int)$stmt->fetchColumn() + 1;
    }

    /**
     * Run migrations
     */
    public function run()
    {
        // Get all migration files
        $files = glob($this->migrationsPath . '/*.php');
        if (empty($files)) {
            echo "No migration files found.\n";
            return;
        }

        // Sort files by name (timestamp)
        sort($files);
        $batch = $this->getNextBatch();
        $count = 0;

        foreach ($files as $file) {
            $migrationName = basename($file);
            
            // Skip if already applied
            if (in_array($migrationName, $this->appliedMigrations)) {
                continue;
            }

            echo "Running migration: {$migrationName}\n";

            // Include and instantiate migration class
            require_once $file;
            $className = 'Migration_' . substr($migrationName, 0, -4); // Remove .php
            $migration = new $className();

            try {
                // Begin transaction
                $this->pdo->beginTransaction();

                // Run migration
                $migration->up($this->pdo);

                // Record migration
                $stmt = $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$migrationName, $batch]);

                // Commit transaction
                $this->pdo->commit();
                $count++;
                echo "✓ Migration completed: {$migrationName}\n";
            } catch (Exception $e) {
                // Rollback transaction
                $this->pdo->rollBack();
                echo "✗ Error in migration {$migrationName}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }

        if ($count > 0) {
            echo "\nSuccessfully ran {$count} " . ($count === 1 ? 'migration' : 'migrations') . ".\n";
        } else {
            echo "\nNo new migrations to run.\n";
        }
    }
}

// Run migrations
$command = new MigrationCommand();
$command->run();
EOT;
    }

    /**
     * Get rollback command template
     */
    private function getRollbackCommandTemplate()
    {
        return <<<'EOT'
#!/usr/bin/env php
<?php
/**
 * Database Rollback Command
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

// Ensure script is run from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

class RollbackCommand
{
    private $pdo;
    private $migrationsPath;

    public function __construct()
    {
        $this->pdo = require dirname(__DIR__) . '/config/database.php';
        $this->migrationsPath = dirname(__DIR__) . '/database/migrations';
    }

    /**
     * Get last batch migrations
     */
    private function getLastBatchMigrations()
    {
        $stmt = $this->pdo->query("
            SELECT migration 
            FROM migrations 
            WHERE batch = (SELECT MAX(batch) FROM migrations)
            ORDER BY id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Run rollback
     */
    public function run()
    {
        // Get migrations from last batch
        $migrations = $this->getLastBatchMigrations();
        if (empty($migrations)) {
            echo "Nothing to rollback.\n";
            return;
        }

        $count = 0;
        foreach ($migrations as $migrationName) {
            echo "Rolling back: {$migrationName}\n";

            // Include and instantiate migration class
            $file = $this->migrationsPath . '/' . $migrationName;
            if (!file_exists($file)) {
                echo "✗ Migration file not found: {$migrationName}\n";
                continue;
            }

            require_once $file;
            $className = 'Migration_' . substr($migrationName, 0, -4); // Remove .php
            $migration = new $className();

            try {
                // Begin transaction
                $this->pdo->beginTransaction();

                // Run rollback
                $migration->down($this->pdo);

                // Remove migration record
                $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE migration = ?");
                $stmt->execute([$migrationName]);

                // Commit transaction
                $this->pdo->commit();
                $count++;
                echo "✓ Rollback completed: {$migrationName}\n";
            } catch (Exception $e) {
                // Rollback transaction
                $this->pdo->rollBack();
                echo "✗ Error in rollback {$migrationName}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }

        if ($count > 0) {
            echo "\nSuccessfully rolled back {$count} " . ($count === 1 ? 'migration' : 'migrations') . ".\n";
        }
    }
}

// Run rollback
$command = new RollbackCommand();
$command->run();
EOT;
    }

    /**
     * Get seeder command template
     */
    private function getSeederCommandTemplate()
    {
        return <<<'EOT'
#!/usr/bin/env php
<?php
/**
 * Database Seeder Command
 * Sandawatha.lk - Sri Lankan Matrimonial Site
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

    public function __construct()
    {
        $this->pdo = require dirname(__DIR__) . '/config/database.php';
        $this->seedersPath = dirname(__DIR__) . '/database/seeders';
        
        // Check for --force flag
        global $argv;
        $this->force = in_array('--force', $argv);
    }

    /**
     * Check if table is empty
     */
    private function isTableEmpty($table)
    {
        $stmt = $this->pdo->query("SELECT 1 FROM {$table} LIMIT 1");
        return $stmt->fetch() === false;
    }

    /**
     * Get table name from seeder class name
     */
    private function getTableName($seederName)
    {
        // Convert CamelCase to snake_case and remove 'Seeder'
        $table = preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Seeder', '', $seederName));
        return strtolower($table) . 's';
    }

    /**
     * Run seeders
     */
    public function run()
    {
        // Get all seeder files
        $files = glob($this->seedersPath . '/*.php');
        if (empty($files)) {
            echo "No seeder files found.\n";
            return;
        }

        $count = 0;
        foreach ($files as $file) {
            $seederName = basename($file, '.php');
            $tableName = $this->getTableName($seederName);
            
            // Skip if table is not empty and --force is not used
            if (!$this->force && !$this->isTableEmpty($tableName)) {
                echo "Skipping {$seederName}: Table {$tableName} is not empty. Use --force to override.\n";
                continue;
            }

            echo "Running seeder: {$seederName}\n";

            // Include and instantiate seeder class
            require_once $file;
            $seeder = new $seederName();

            try {
                // Begin transaction
                $this->pdo->beginTransaction();

                // Clear table if --force is used
                if ($this->force) {
                    $this->pdo->exec("TRUNCATE TABLE {$tableName}");
                }

                // Run seeder
                $seeder->run($this->pdo);

                // Commit transaction
                $this->pdo->commit();
                $count++;
                echo "✓ Seeding completed: {$seederName}\n";
            } catch (Exception $e) {
                // Rollback transaction
                $this->pdo->rollBack();
                echo "✗ Error in seeder {$seederName}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }

        if ($count > 0) {
            echo "\nSuccessfully ran {$count} " . ($count === 1 ? 'seeder' : 'seeders') . ".\n";
        }
    }
}

// Run seeders
$command = new SeederCommand();
$command->run();
EOT;
    }

    /**
     * Print script header
     */
    private function printHeader()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║        Sandawatha.lk Migration Commands Setup             ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
    }

    /**
     * Print final report
     */
    private function printReport()
    {
        if (!empty($this->created)) {
            echo COLOR_GREEN . "\n✓ Created the following command files:\n" . COLOR_RESET;
            foreach ($this->created as $file) {
                echo "  • {$file}\n";
            }
        }

        if (!empty($this->existing)) {
            echo COLOR_YELLOW . "\n✓ Already existing command files:\n" . COLOR_RESET;
            foreach ($this->existing as $file) {
                echo "  • {$file}\n";
            }
        }

        if (!empty($this->errors)) {
            echo COLOR_RED . "\n✗ Errors occurred:\n" . COLOR_RESET;
            foreach ($this->errors as $error) {
                echo "  • {$error}\n";
            }
        }

        echo "\nTotal Summary:\n";
        echo "  • Created: " . count($this->created) . " files\n";
        echo "  • Existing: " . count($this->existing) . " files\n";
        echo "  • Errors: " . count($this->errors) . " files\n\n";

        echo COLOR_YELLOW . "How to use the commands:\n\n";
        echo "1. Run migrations:\n";
        echo "   php cli/migrate.php\n\n";
        echo "2. Rollback last batch:\n";
        echo "   php cli/rollback.php\n\n";
        echo "3. Run seeders:\n";
        echo "   php cli/seed.php\n";
        echo "   # Or force re-seed:\n";
        echo "   php cli/seed.php --force\n" . COLOR_RESET . "\n";
    }
}

// Run the setup
$setup = new MigrationCommandsSetup();
$setup->run(); 