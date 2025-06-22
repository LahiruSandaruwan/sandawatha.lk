<?php
/**
 * Migration and Seeder Files Check Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script checks and creates required migration and seeder files.
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

class MigrationSeederCheck
{
    private $projectRoot;
    private $migrationsPath;
    private $seedersPath;
    private $created = [];
    private $existing = [];
    private $errors = [];

    // Define required migrations with their base names
    private $requiredMigrations = [
        'create_users_table',
        'create_religions_table',
        'create_castes_table',
        'create_districts_table',
        'create_user_preferences_table',
        'create_user_horoscope_table',
        'create_user_photos_table',
        'create_messages_table',
        'create_subscriptions_table',
        'create_interests_table',
        'create_referrals_table',
        'create_gifts_table',
        'create_blog_posts_table',
        'create_admins_table',
        'create_notifications_table',
        'create_verifications_table',
        'create_reports_table',
        'create_blocks_table'
    ];

    // Define required seeders
    private $requiredSeeders = [
        'Religions',
        'Castes',
        'Districts',
        'Admins'
    ];

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__);
        $this->migrationsPath = $this->projectRoot . '/database/migrations';
        $this->seedersPath = $this->projectRoot . '/database/seeders';
        
        // Create directories if they don't exist
        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }
        if (!is_dir($this->seedersPath)) {
            mkdir($this->seedersPath, 0755, true);
        }
    }

    /**
     * Run the check process
     */
    public function run()
    {
        $this->printHeader();
        $this->checkMigrations();
        $this->checkSeeders();
        $this->printReport();
    }

    /**
     * Check required migrations
     */
    private function checkMigrations()
    {
        $existingFiles = glob($this->migrationsPath . '/*.php');
        $existingBasenames = array_map(function($file) {
            return $this->getMigrationBasename($file);
        }, $existingFiles);

        foreach ($this->requiredMigrations as $migration) {
            if (!in_array($migration, $existingBasenames)) {
                $this->createMigration($migration);
            } else {
                $this->existing[] = "migrations/{$migration}.php";
            }
        }
    }

    /**
     * Get migration basename without timestamp
     */
    private function getMigrationBasename($file)
    {
        $filename = basename($file);
        return preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', str_replace('.php', '', $filename));
    }

    /**
     * Check required seeders
     */
    private function checkSeeders()
    {
        foreach ($this->requiredSeeders as $seeder) {
            $filename = "{$seeder}Seeder.php";
            $path = "{$this->seedersPath}/{$filename}";
            
            if (!file_exists($path)) {
                $this->createSeeder($seeder);
            } else {
                $this->existing[] = "seeders/{$filename}";
            }
        }
    }

    /**
     * Create a migration file
     */
    private function createMigration($name)
    {
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$name}.php";
        $path = "{$this->migrationsPath}/{$filename}";

        $template = $this->getMigrationTemplate($name, $timestamp);

        if (file_put_contents($path, $template)) {
            $this->created[] = "migrations/{$filename}";
        } else {
            $this->errors[] = "Failed to create migration: {$filename}";
        }
    }

    /**
     * Create a seeder file
     */
    private function createSeeder($name)
    {
        $filename = "{$name}Seeder.php";
        $path = "{$this->seedersPath}/{$filename}";

        $template = $this->getSeederTemplate($name);

        if (file_put_contents($path, $template)) {
            $this->created[] = "seeders/{$filename}";
        } else {
            $this->errors[] = "Failed to create seeder: {$filename}";
        }
    }

    /**
     * Get migration file template
     */
    private function getMigrationTemplate($name, $timestamp)
    {
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        
        return <<<EOT
<?php
/**
 * Migration: {$name}
 * Created at: {$timestamp}
 */

class Migration_{$timestamp}_{$className}
{
    /**
     * Run the migration
     */
    public function up(PDO \$pdo)
    {
        \$sql = "";
        return \$pdo->exec(\$sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO \$pdo)
    {
        \$sql = "";
        return \$pdo->exec(\$sql);
    }
}
EOT;
    }

    /**
     * Get seeder file template
     */
    private function getSeederTemplate($name)
    {
        return <<<EOT
<?php
/**
 * Seeder: {$name}
 */

class {$name}Seeder
{
    /**
     * Run the seeder
     */
    public function run(PDO \$pdo)
    {
        \$sql = "";
        return \$pdo->exec(\$sql);
    }
}
EOT;
    }

    /**
     * Print script header
     */
    private function printHeader()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║        Sandawatha.lk Migration/Seeder Files Check         ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
    }

    /**
     * Print final report
     */
    private function printReport()
    {
        if (!empty($this->created)) {
            echo COLOR_GREEN . "\n✓ Created the following files:\n" . COLOR_RESET;
            foreach ($this->created as $file) {
                echo "  • {$file}\n";
            }
        }

        if (!empty($this->existing)) {
            echo COLOR_YELLOW . "\n✓ Already existing files:\n" . COLOR_RESET;
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

        if (empty($this->created) && empty($this->errors)) {
            echo COLOR_GREEN . "\n✓ All required migration and seeder files are present!\n" . COLOR_RESET;
        }

        echo "\nTotal Summary:\n";
        echo "  • Created: " . count($this->created) . " files\n";
        echo "  • Existing: " . count($this->existing) . " files\n";
        echo "  • Errors: " . count($this->errors) . " files\n\n";

        if (!empty($this->created)) {
            echo COLOR_YELLOW . "Note: Created files are empty templates.\n";
            echo "Please update them with proper SQL statements and data.\n" . COLOR_RESET . "\n";
        }
    }
}

// Run the check
$check = new MigrationSeederCheck();
$check->run(); 