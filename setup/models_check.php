<?php
/**
 * Model Files Check Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script checks and creates required model files.
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

class ModelsCheck
{
    private $projectRoot;
    private $modelsPath;
    private $created = [];
    private $existing = [];
    private $errors = [];

    // Define required models and their properties
    private $requiredModels = [
        'User' => [
            'id', 'email', 'password', 'first_name', 'last_name', 'gender', 
            'date_of_birth', 'religion_id', 'caste_id', 'district_id', 
            'profile_photo', 'bio', 'occupation', 'education', 'height',
            'marital_status', 'drinking', 'smoking', 'is_verified',
            'is_premium', 'last_active', 'created_at', 'updated_at'
        ],
        'UserPreference' => [
            'id', 'user_id', 'min_age', 'max_age', 'religion_id', 
            'caste_id', 'district_id', 'marital_status', 'min_height',
            'max_height', 'education', 'occupation', 'drinking', 'smoking'
        ],
        'Horoscope' => [
            'id', 'user_id', 'birth_time', 'birth_place', 'nakatha',
            'gana', 'zodiac', 'rashi', 'nekatha', 'horoscope_image'
        ],
        'Message' => [
            'id', 'sender_id', 'receiver_id', 'content', 'is_read',
            'created_at', 'updated_at'
        ],
        'Subscription' => [
            'id', 'user_id', 'plan', 'amount', 'start_date',
            'end_date', 'status', 'payment_method', 'created_at'
        ],
        'Interest' => [
            'id', 'sender_id', 'receiver_id', 'status',
            'created_at', 'updated_at'
        ],
        'Referral' => [
            'id', 'referrer_id', 'referred_email', 'status',
            'joined_user_id', 'created_at'
        ],
        'Gift' => [
            'id', 'sender_id', 'receiver_id', 'gift_type',
            'message', 'created_at'
        ],
        'Admin' => [
            'id', 'username', 'email', 'password', 'role',
            'last_login', 'created_at', 'updated_at'
        ],
        'Report' => [
            'id', 'reporter_id', 'reported_user_id', 'reason',
            'description', 'status', 'created_at'
        ],
        'Block' => [
            'id', 'blocker_id', 'blocked_user_id', 'reason',
            'created_at'
        ],
        'Notification' => [
            'id', 'user_id', 'type', 'content', 'is_read',
            'created_at'
        ]
    ];

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__);
        $this->modelsPath = $this->projectRoot . '/app/models';
        
        // Create models directory if it doesn't exist
        if (!is_dir($this->modelsPath)) {
            mkdir($this->modelsPath, 0755, true);
        }
    }

    /**
     * Run the check process
     */
    public function run()
    {
        $this->printHeader();
        $this->checkModels();
        $this->printReport();
    }

    /**
     * Check required models
     */
    private function checkModels()
    {
        foreach ($this->requiredModels as $model => $properties) {
            $filename = "{$model}.php";
            $path = "{$this->modelsPath}/{$filename}";
            
            if (!file_exists($path)) {
                $this->createModel($model, $properties);
            } else {
                $this->existing[] = "models/{$filename}";
            }
        }
    }

    /**
     * Create a model file
     */
    private function createModel($name, $properties)
    {
        $filename = "{$name}.php";
        $path = "{$this->modelsPath}/{$filename}";

        $template = $this->getModelTemplate($name, $properties);

        if (file_put_contents($path, $template)) {
            $this->created[] = "models/{$filename}";
        } else {
            $this->errors[] = "Failed to create model: {$filename}";
        }
    }

    /**
     * Get model file template
     */
    private function getModelTemplate($name, $properties)
    {
        // Create property declarations
        $propertyDeclarations = array_map(function($prop) {
            return "    private \${$prop};";
        }, $properties);
        
        // Create getter methods
        $getterMethods = array_map(function($prop) {
            $methodName = str_replace('_', '', ucwords($prop, '_'));
            return <<<EOT
    
    /**
     * Get {$prop}
     */
    public function get{$methodName}()
    {
        return \$this->{$prop};
    }
EOT;
        }, $properties);
        
        // Create setter methods
        $setterMethods = array_map(function($prop) {
            $methodName = str_replace('_', '', ucwords($prop, '_'));
            return <<<EOT
    
    /**
     * Set {$prop}
     */
    public function set{$methodName}(\$value)
    {
        \$this->{$prop} = \$value;
        return \$this;
    }
EOT;
        }, $properties);

        // Build the complete template
        return <<<EOT
<?php
/**
 * {$name} Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class {$name}
{
    // Database connection
    private \$pdo;
    
    // Table name
    private \$table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', '{$name}')).''.'s';

    // Properties
{$this->indent(implode("\n", $propertyDeclarations), 1)}

    /**
     * Constructor
     */
    public function __construct(PDO \$pdo = null)
    {
        \$this->pdo = \$pdo ?: require dirname(__DIR__, 2) . '/config/database.php';
    }

    /**
     * Find by ID
     */
    public function findById(\$id)
    {
        \$stmt = \$this->pdo->prepare("SELECT * FROM {\$this->table} WHERE id = :id");
        \$stmt->execute(['id' => \$id]);
        return \$stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new record
     */
    public function create(array \$data)
    {
        // Implementation
    }

    /**
     * Update record
     */
    public function update(\$id, array \$data)
    {
        // Implementation
    }

    /**
     * Delete record
     */
    public function delete(\$id)
    {
        \$stmt = \$this->pdo->prepare("DELETE FROM {\$this->table} WHERE id = :id");
        return \$stmt->execute(['id' => \$id]);
    }

    /**
     * Get all records
     */
    public function getAll()
    {
        \$stmt = \$this->pdo->query("SELECT * FROM {\$this->table}");
        return \$stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Getters{$this->indent(implode("", $getterMethods), 1)}

    // Setters{$this->indent(implode("", $setterMethods), 1)}
}
EOT;
    }

    /**
     * Indent text by specified number of levels
     */
    private function indent($text, $levels = 1)
    {
        $lines = explode("\n", $text);
        $indentation = str_repeat("    ", $levels);
        $indentedLines = array_map(function($line) use ($indentation) {
            return empty($line) ? $line : $indentation . $line;
        }, $lines);
        return implode("\n", $indentedLines);
    }

    /**
     * Print script header
     */
    private function printHeader()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║           Sandawatha.lk Model Files Check                 ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
    }

    /**
     * Print final report
     */
    private function printReport()
    {
        if (!empty($this->created)) {
            echo COLOR_GREEN . "\n✓ Created the following model files:\n" . COLOR_RESET;
            foreach ($this->created as $file) {
                echo "  • {$file}\n";
            }
        }

        if (!empty($this->existing)) {
            echo COLOR_YELLOW . "\n✓ Already existing model files:\n" . COLOR_RESET;
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
            echo COLOR_GREEN . "\n✓ All required model files are present!\n" . COLOR_RESET;
        }

        echo "\nTotal Summary:\n";
        echo "  • Created: " . count($this->created) . " files\n";
        echo "  • Existing: " . count($this->existing) . " files\n";
        echo "  • Errors: " . count($this->errors) . " files\n\n";

        if (!empty($this->created)) {
            echo COLOR_YELLOW . "Note: Created files are skeleton templates.\n";
            echo "Please implement the create() and update() methods for each model.\n" . COLOR_RESET . "\n";
        }
    }
}

// Run the check
$check = new ModelsCheck();
$check->run(); 