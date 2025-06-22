<?php
/**
 * Directory Structure Check and Setup Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script checks and creates the required directory structure for the project.
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

class DirectorySetup
{
    private $projectRoot;
    private $structure;
    private $created = [];
    private $existing = [];
    private $errors = [];

    public function __construct()
    {
        // Get project root directory (one level up from setup/)
        $this->projectRoot = dirname(__DIR__);
        
        // Define required directory structure
        $this->structure = [
            'public' => [
                'index.php' => '<?php require_once "../app/controllers/HomeController.php"; ?>',
                'login.php' => '<?php require_once "../app/controllers/AuthController.php"; ?>',
                'register.php' => '<?php require_once "../app/controllers/AuthController.php"; ?>',
                '.htaccess' => "RewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule ^(.*)$ index.php?url=$1 [QSA,L]",
                'assets' => [
                    'css' => [],
                    'js' => [],
                    'images' => []
                ]
            ],
            'app' => [
                'controllers' => [],
                'models' => [],
                'views' => [
                    'shared' => [],
                    'auth' => [],
                    'profile' => [],
                    'match' => [],
                    'horoscope' => [],
                    'chat' => [],
                    'admin' => []
                ]
            ],
            'api' => [
                'match-ai.php' => '<?php\nheader("Content-Type: application/json");\nrequire_once "../config/database.php";\n',
                'verify.php' => '<?php\nheader("Content-Type: application/json");\nrequire_once "../config/database.php";\n',
                'gifts.php' => '<?php\nheader("Content-Type: application/json");\nrequire_once "../config/database.php";\n',
                'referrals.php' => '<?php\nheader("Content-Type: application/json");\nrequire_once "../config/database.php";\n'
            ],
            'config' => [
                'database.php' => '<?php\nrequire_once __DIR__ . "/../.env.php";\n\ntry {\n    $pdo = new PDO(\n        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",\n        DB_USER,\n        DB_PASS,\n        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]\n    );\n} catch (PDOException $e) {\n    die("Connection failed: " . $e->getMessage());\n}'
            ],
            'database' => [
                'schema.sql' => ''  // Already exists and populated
            ],
            'storage' => [
                'profiles' => [],
                'ids' => [],
                'logs' => []
            ],
            '.env' => "DB_HOST=localhost\nDB_NAME=sandawatha\nDB_USER=root\nDB_PASS=\n\nAPP_NAME=Sandawatha.lk\nAPP_URL=http://localhost/sandawatha\nAPP_ENV=development\n\nMAIL_HOST=smtp.gmail.com\nMAIL_PORT=587\nMAIL_USERNAME=\nMAIL_PASSWORD=\n\nAI_API_KEY=\n",
            'README.md' => "# Sandawatha.lk\n\nSri Lankan Matrimonial Website\n\n## Setup Instructions\n\n1. Clone the repository\n2. Create a MySQL database named 'sandawatha'\n3. Import database/schema.sql\n4. Copy .env.example to .env and update the values\n5. Ensure storage directories are writable\n6. Point your web server to the public directory\n\n## Directory Structure\n\n- public/: Web root directory\n- app/: Application code\n- api/: API endpoints\n- config/: Configuration files\n- database/: Database schema and migrations\n- storage/: File uploads and logs\n\n## Requirements\n\n- PHP 7.4 or higher\n- MySQL 5.7 or higher\n- mod_rewrite enabled\n- GD Library for image processing\n- FileInfo extension for file uploads\n"
        ];
    }

    /**
     * Check and create directory structure
     */
    public function run()
    {
        $this->printHeader();
        $this->processStructure($this->structure, $this->projectRoot);
        $this->printReport();
    }

    /**
     * Process directory structure recursively
     */
    private function processStructure($structure, $basePath)
    {
        foreach ($structure as $name => $content) {
            $path = $basePath . DIRECTORY_SEPARATOR . $name;
            
            if (is_array($content)) {
                // It's a directory
                if (!file_exists($path)) {
                    if (mkdir($path, 0755, true)) {
                        $this->created[] = $this->getRelativePath($path);
                    } else {
                        $this->errors[] = "Failed to create directory: " . $this->getRelativePath($path);
                    }
                } else {
                    $this->existing[] = $this->getRelativePath($path);
                }
                $this->processStructure($content, $path);
            } else {
                // It's a file
                if (!file_exists($path)) {
                    if (file_put_contents($path, $content) !== false) {
                        $this->created[] = $this->getRelativePath($path);
                    } else {
                        $this->errors[] = "Failed to create file: " . $this->getRelativePath($path);
                    }
                } else {
                    $this->existing[] = $this->getRelativePath($path);
                }
            }
        }
    }

    /**
     * Get path relative to project root
     */
    private function getRelativePath($path)
    {
        return str_replace($this->projectRoot . DIRECTORY_SEPARATOR, '', $path);
    }

    /**
     * Print script header
     */
    private function printHeader()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║             Sandawatha.lk Directory Setup Check            ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
    }

    /**
     * Print final report
     */
    private function printReport()
    {
        if (!empty($this->created)) {
            echo COLOR_GREEN . "\n✓ Created the following items:\n" . COLOR_RESET;
            foreach ($this->created as $path) {
                echo "  • $path\n";
            }
        }

        if (!empty($this->existing)) {
            echo COLOR_YELLOW . "\n✓ Already existing items:\n" . COLOR_RESET;
            foreach ($this->existing as $path) {
                echo "  • $path\n";
            }
        }

        if (!empty($this->errors)) {
            echo COLOR_RED . "\n✗ Errors occurred:\n" . COLOR_RESET;
            foreach ($this->errors as $error) {
                echo "  • $error\n";
            }
        }

        if (empty($this->created) && empty($this->errors)) {
            echo COLOR_GREEN . "\n✓ Directory structure is already correct!\n" . COLOR_RESET;
        }

        echo "\nTotal Summary:\n";
        echo "  • Created: " . count($this->created) . " items\n";
        echo "  • Existing: " . count($this->existing) . " items\n";
        echo "  • Errors: " . count($this->errors) . " items\n\n";
    }
}

// Run the directory setup
$setup = new DirectorySetup();
$setup->run(); 