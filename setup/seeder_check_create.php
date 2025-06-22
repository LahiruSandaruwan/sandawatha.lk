<?php
/**
 * Seeder Check and Creation Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script checks for required seeders and creates missing ones.
 */

class SeederCheckCreate
{
    private $projectRoot;
    private $seedersPath;
    private $requiredSeeders;
    private $existingSeeders = [];
    private $createdSeeders = [];
    private $pdo;

    public function __construct()
    {
        // Set paths
        $this->projectRoot = dirname(__DIR__);
        $this->seedersPath = $this->projectRoot . '/database/seeders';

        // Define required seeders with their templates
        $this->requiredSeeders = [
            'ReligionsSeeder.php' => [
                'table' => 'religions',
                'data' => [
                    ['name' => 'Buddhism', 'name_si' => 'බෞද්ධ', 'name_ta' => 'பௌத்தம்'],
                    ['name' => 'Hinduism', 'name_si' => 'හින්දු', 'name_ta' => 'இந்து'],
                    ['name' => 'Islam', 'name_si' => 'ඉස්ලාම්', 'name_ta' => 'இஸ்லாம்'],
                    ['name' => 'Christianity', 'name_si' => 'ක්‍රිස්තියානි', 'name_ta' => 'கிறிஸ்தவம்'],
                    ['name' => 'Roman Catholic', 'name_si' => 'රෝමානු කතෝලික', 'name_ta' => 'ரோமன் கத்தோலிக்க']
                ]
            ],
            'CastesSeeder.php' => [
                'table' => 'castes',
                'data' => [
                    ['name' => 'Govigama', 'name_si' => 'ගොවිගම', 'name_ta' => 'கோவிகம', 'religion_id' => 1],
                    ['name' => 'Karava', 'name_si' => 'කරාව', 'name_ta' => 'கராவ', 'religion_id' => 1],
                    ['name' => 'Salagama', 'name_si' => 'සලගම', 'name_ta' => 'சலகம', 'religion_id' => 1],
                    ['name' => 'Brahmin', 'name_si' => 'බ්‍රාහ්මණ', 'name_ta' => 'பிராமணர்', 'religion_id' => 2],
                    ['name' => 'Vellalar', 'name_si' => 'වෙල්ලාල', 'name_ta' => 'வேளாளர்', 'religion_id' => 2]
                ]
            ],
            'DistrictsSeeder.php' => [
                'table' => 'districts',
                'data' => [
                    ['name' => 'Colombo', 'name_si' => 'කොළඹ', 'name_ta' => 'கொழும்பு'],
                    ['name' => 'Gampaha', 'name_si' => 'ගම්පහ', 'name_ta' => 'கம்பஹா'],
                    ['name' => 'Kalutara', 'name_si' => 'කළුතර', 'name_ta' => 'களுத்துறை'],
                    ['name' => 'Kandy', 'name_si' => 'මහනුවර', 'name_ta' => 'கண்டி'],
                    ['name' => 'Matale', 'name_si' => 'මාතලේ', 'name_ta' => 'மாத்தளை']
                ]
            ],
            'AdminsSeeder.php' => [
                'table' => 'admins',
                'data' => [
                    [
                        'username' => 'admin',
                        'email' => 'admin@sandawatha.lk',
                        'password' => password_hash('admin123', PASSWORD_DEFAULT),
                        'first_name' => 'Super',
                        'last_name' => 'Admin',
                        'role' => 'super_admin',
                        'status' => 'active'
                    ]
                ]
            ],
            'GiftsSeeder.php' => [
                'table' => 'gifts',
                'data' => [
                    [
                        'name' => 'Red Rose',
                        'description' => 'A beautiful virtual red rose to express your love',
                        'icon' => 'assets/images/gifts/rose.png',
                        'price' => 100.00,
                        'status' => 'active'
                    ],
                    [
                        'name' => 'Heart',
                        'description' => 'Send a heart to show your interest',
                        'icon' => 'assets/images/gifts/heart.png',
                        'price' => 50.00,
                        'status' => 'active'
                    ],
                    [
                        'name' => 'Ring',
                        'description' => 'A virtual ring to show your commitment',
                        'icon' => 'assets/images/gifts/ring.png',
                        'price' => 200.00,
                        'status' => 'active'
                    ]
                ]
            ]
        ];
    }

    /**
     * Initialize database connection
     */
    private function initDatabase()
    {
        try {
            require_once $this->projectRoot . '/config/database.php';
            $this->pdo = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage() . "\n");
        }
    }

    /**
     * Check existing seeders
     */
    private function checkExistingSeeders()
    {
        foreach ($this->requiredSeeders as $seeder => $config) {
            $seederPath = $this->seedersPath . '/' . $seeder;
            if (file_exists($seederPath)) {
                $this->existingSeeders[] = $seeder;
            }
        }
    }

    /**
     * Create missing seeders
     */
    private function createMissingSeeders()
    {
        foreach ($this->requiredSeeders as $seeder => $config) {
            if (!in_array($seeder, $this->existingSeeders)) {
                $this->createSeeder($seeder, $config);
                $this->createdSeeders[] = $seeder;
            }
        }
    }

    /**
     * Create a seeder file
     */
    private function createSeeder($seeder, $config)
    {
        $className = basename($seeder, '.php');
        $table = $config['table'];
        $data = var_export($config['data'], true);

        $template = <<<PHP
<?php
/**
 * {$className}
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * Seeds the {$table} table with initial data
 */

class {$className}
{
    /**
     * Run the seeder
     */
    public function run(PDO \$pdo)
    {
        \$data = {$data};

        try {
            // Begin transaction
            \$pdo->beginTransaction();

            // Prepare insert statement
            \$sql = "INSERT INTO {$table} (" . implode(', ', array_keys(\$data[0])) . ") 
                    VALUES (" . implode(', ', array_fill(0, count(\$data[0]), '?')) . ")";
            \$stmt = \$pdo->prepare(\$sql);

            // Insert each record
            foreach (\$data as \$record) {
                \$stmt->execute(array_values(\$record));
            }

            // Commit transaction
            \$pdo->commit();
            return true;

        } catch (Exception \$e) {
            // Rollback on error
            if (\$pdo->inTransaction()) {
                \$pdo->rollBack();
            }
            throw \$e;
        }
    }
}
PHP;

        $seederPath = $this->seedersPath . '/' . $seeder;
        if (!file_put_contents($seederPath, $template)) {
            throw new Exception("Failed to create seeder: {$seeder}");
        }
    }

    /**
     * Print execution report
     */
    private function printReport()
    {
        echo "\n╔════════════════════════════════════════════════════════════╗\n";
        echo "║            Sandawatha.lk Seeder Check Report              ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";

        if (!empty($this->existingSeeders)) {
            echo "✓ Existing Seeders:\n";
            foreach ($this->existingSeeders as $seeder) {
                echo "  • {$seeder}\n";
            }
            echo "\n";
        }

        if (!empty($this->createdSeeders)) {
            echo "✓ Newly Created Seeders:\n";
            foreach ($this->createdSeeders as $seeder) {
                echo "  • {$seeder}\n";
            }
            echo "\n";
        }

        echo "Total Seeders: " . count($this->requiredSeeders) . "\n";
        echo "• Found: " . count($this->existingSeeders) . "\n";
        echo "• Created: " . count($this->createdSeeders) . "\n\n";
    }

    /**
     * Run the seeder check and creation process
     */
    public function run()
    {
        try {
            // Initialize database connection
            $this->initDatabase();

            // Create seeders directory if not exists
            if (!is_dir($this->seedersPath)) {
                if (!mkdir($this->seedersPath, 0755, true)) {
                    throw new Exception("Failed to create seeders directory");
                }
            }

            // Check and create seeders
            $this->checkExistingSeeders();
            $this->createMissingSeeders();
            $this->printReport();

        } catch (Exception $e) {
            die("Error: " . $e->getMessage() . "\n");
        }
    }
}

// Run the script
$checker = new SeederCheckCreate();
$checker->run(); 