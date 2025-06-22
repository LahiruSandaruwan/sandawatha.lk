<?php
/**
 * Migration and Seeder Setup Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script provides a lightweight migration and seeder system.
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

class DatabaseManager
{
    private $pdo;
    private $projectRoot;
    private $migrationsPath;
    private $seedersPath;

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__);
        $this->migrationsPath = $this->projectRoot . '/database/migrations';
        $this->seedersPath = $this->projectRoot . '/database/seeders';
        
        // Create migrations and seeders directories if they don't exist
        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }
        if (!is_dir($this->seedersPath)) {
            mkdir($this->seedersPath, 0755, true);
        }

        // Load database configuration
        require_once $this->projectRoot . '/config/database.php';
        $this->pdo = $pdo;

        // Ensure migrations table exists
        $this->createMigrationsTable();
    }

    /**
     * Create migrations table if it doesn't exist
     */
    private function createMigrationsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->pdo->exec($sql);
    }

    /**
     * Create a new migration file
     */
    public function createMigration($name)
    {
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$name}.php";
        $path = "{$this->migrationsPath}/{$filename}";

        $template = <<<EOT
<?php
/**
 * Migration: {$name}
 * Created at: {$timestamp}
 */

class Migration_{$timestamp}_{$name}
{
    /**
     * Run the migration
     */
    public function up(\PDO \$pdo)
    {
        \$sql = "";
        return \$pdo->exec(\$sql);
    }

    /**
     * Reverse the migration
     */
    public function down(\PDO \$pdo)
    {
        \$sql = "";
        return \$pdo->exec(\$sql);
    }
}
EOT;

        if (file_put_contents($path, $template)) {
            echo COLOR_GREEN . "Created Migration: {$filename}\n" . COLOR_RESET;
        } else {
            echo COLOR_RED . "Failed to create migration: {$filename}\n" . COLOR_RESET;
        }
    }

    /**
     * Create a new seeder file
     */
    public function createSeeder($name)
    {
        $filename = "{$name}Seeder.php";
        $path = "{$this->seedersPath}/{$filename}";

        $template = <<<EOT
<?php
/**
 * Seeder: {$name}
 */

class {$name}Seeder
{
    /**
     * Run the seeder
     */
    public function run(\PDO \$pdo)
    {
        \$sql = "";
        return \$pdo->exec(\$sql);
    }
}
EOT;

        if (file_put_contents($path, $template)) {
            echo COLOR_GREEN . "Created Seeder: {$filename}\n" . COLOR_RESET;
        } else {
            echo COLOR_RED . "Failed to create seeder: {$filename}\n" . COLOR_RESET;
        }
    }

    /**
     * Run pending migrations
     */
    public function migrate()
    {
        // Get the last batch number
        $stmt = $this->pdo->query("SELECT MAX(batch) as batch FROM migrations");
        $lastBatch = $stmt->fetch(PDO::FETCH_ASSOC)['batch'] ?? 0;
        $currentBatch = $lastBatch + 1;

        // Get applied migrations
        $stmt = $this->pdo->query("SELECT migration FROM migrations");
        $applied = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Get all migration files
        $files = glob("{$this->migrationsPath}/*.php");
        sort($files);

        foreach ($files as $file) {
            $migration = basename($file);
            
            if (!in_array($migration, $applied)) {
                require_once $file;
                
                $className = 'Migration_' . basename($file, '.php');
                $instance = new $className();

                try {
                    $this->pdo->beginTransaction();
                    
                    $instance->up($this->pdo);
                    
                    $stmt = $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                    $stmt->execute([$migration, $currentBatch]);
                    
                    $this->pdo->commit();
                    echo COLOR_GREEN . "Migrated: {$migration}\n" . COLOR_RESET;
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    echo COLOR_RED . "Migration failed: {$migration}\n" . COLOR_RESET;
                    echo COLOR_RED . $e->getMessage() . "\n" . COLOR_RESET;
                }
            }
        }
    }

    /**
     * Rollback the last batch of migrations
     */
    public function rollback()
    {
        // Get the last batch
        $stmt = $this->pdo->query("SELECT MAX(batch) as batch FROM migrations");
        $lastBatch = $stmt->fetch(PDO::FETCH_ASSOC)['batch'];

        if (!$lastBatch) {
            echo COLOR_YELLOW . "Nothing to rollback.\n" . COLOR_RESET;
            return;
        }

        // Get migrations from the last batch
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC");
        $stmt->execute([$lastBatch]);
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($migrations as $migration) {
            $file = "{$this->migrationsPath}/{$migration}";
            
            if (file_exists($file)) {
                require_once $file;
                
                $className = 'Migration_' . basename($file, '.php');
                $instance = new $className();

                try {
                    $this->pdo->beginTransaction();
                    
                    $instance->down($this->pdo);
                    
                    $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE migration = ?");
                    $stmt->execute([$migration]);
                    
                    $this->pdo->commit();
                    echo COLOR_GREEN . "Rolled back: {$migration}\n" . COLOR_RESET;
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    echo COLOR_RED . "Rollback failed: {$migration}\n" . COLOR_RESET;
                    echo COLOR_RED . $e->getMessage() . "\n" . COLOR_RESET;
                }
            }
        }
    }

    /**
     * Run a specific seeder
     */
    public function seed($name = null)
    {
        if ($name) {
            $this->runSeeder($name);
        } else {
            // Run all seeders
            $files = glob("{$this->seedersPath}/*.php");
            sort($files);

            foreach ($files as $file) {
                $seeder = basename($file, '.php');
                $seeder = str_replace('Seeder', '', $seeder);
                $this->runSeeder($seeder);
            }
        }
    }

    /**
     * Run a single seeder
     */
    private function runSeeder($name)
    {
        $file = "{$this->seedersPath}/{$name}Seeder.php";
        
        if (file_exists($file)) {
            require_once $file;
            
            $className = "{$name}Seeder";
            $instance = new $className();

            try {
                $this->pdo->beginTransaction();
                $instance->run($this->pdo);
                $this->pdo->commit();
                echo COLOR_GREEN . "Seeded: {$name}\n" . COLOR_RESET;
            } catch (Exception $e) {
                $this->pdo->rollBack();
                echo COLOR_RED . "Seeding failed: {$name}\n" . COLOR_RESET;
                echo COLOR_RED . $e->getMessage() . "\n" . COLOR_RESET;
            }
        } else {
            echo COLOR_RED . "Seeder not found: {$name}\n" . COLOR_RESET;
        }
    }
}

// Create example migration and seeder files
function createExampleFiles(DatabaseManager $db)
{
    // Create migrations
    $db->createMigration('create_religions_table');
    $db->createMigration('create_castes_table');
    $db->createMigration('create_districts_table');

    // Create seeders
    $db->createSeeder('Religions');
    $db->createSeeder('Castes');
    $db->createSeeder('Districts');
}

// Handle command line arguments
if ($argc < 2) {
    echo "Usage: php migration_seeder_setup.php [command] [name]\n";
    echo "Commands:\n";
    echo "  migrate              Run pending migrations\n";
    echo "  rollback            Rollback last batch of migrations\n";
    echo "  seed [name]         Run all or specific seeder\n";
    echo "  create:migration    Create a new migration\n";
    echo "  create:seeder       Create a new seeder\n";
    echo "  examples            Create example files\n";
    exit(1);
}

$db = new DatabaseManager();
$command = $argv[1];

switch ($command) {
    case 'migrate':
        $db->migrate();
        break;
    case 'rollback':
        $db->rollback();
        break;
    case 'seed':
        $name = $argv[2] ?? null;
        $db->seed($name);
        break;
    case 'create:migration':
        if (!isset($argv[2])) {
            echo COLOR_RED . "Migration name required\n" . COLOR_RESET;
            exit(1);
        }
        $db->createMigration($argv[2]);
        break;
    case 'create:seeder':
        if (!isset($argv[2])) {
            echo COLOR_RED . "Seeder name required\n" . COLOR_RESET;
            exit(1);
        }
        $db->createSeeder($argv[2]);
        break;
    case 'examples':
        createExampleFiles($db);
        break;
    default:
        echo COLOR_RED . "Unknown command: {$command}\n" . COLOR_RESET;
        exit(1);
} 