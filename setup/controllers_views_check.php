<?php
/**
 * Controllers and Views Check Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script checks and creates required controller and view files.
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

class ControllersViewsCheck
{
    private $projectRoot;
    private $controllersPath;
    private $viewsPath;
    private $created = [];
    private $existing = [];
    private $errors = [];

    // Define required controllers and their methods
    private $requiredControllers = [
        'Auth' => [
            'login', 'register', 'logout', 'forgotPassword', 'resetPassword',
            'verifyEmail', 'resendVerification'
        ],
        'Profile' => [
            'view', 'edit', 'update', 'uploadPhoto', 'deletePhoto',
            'updatePreferences', 'updateHoroscope'
        ],
        'Match' => [
            'index', 'search', 'filter', 'sendInterest', 'acceptInterest',
            'rejectInterest', 'block'
        ],
        'Horoscope' => [
            'match', 'calculate', 'upload', 'delete', 'compare'
        ],
        'Chat' => [
            'index', 'send', 'receive', 'history', 'markRead',
            'deleteMessage'
        ],
        'Admin' => [
            'dashboard', 'users', 'reports', 'verifications', 'statistics',
            'settings', 'blockUser', 'unblockUser'
        ]
    ];

    // Define required views and their content types
    private $requiredViews = [
        'shared/header.php' => 'layout',
        'shared/footer.php' => 'layout',
        'profile/view.php' => 'profile',
        'profile/edit.php' => 'profile',
        'match/index.php' => 'match',
        'horoscope/match.php' => 'horoscope',
        'chat/index.php' => 'chat',
        'admin/dashboard.php' => 'admin',
        'admin/users.php' => 'admin',
        'admin/reports.php' => 'admin'
    ];

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__);
        $this->controllersPath = $this->projectRoot . '/app/controllers';
        $this->viewsPath = $this->projectRoot . '/app/views';
        
        // Create necessary directories
        foreach (array_merge([$this->controllersPath], $this->getRequiredViewDirectories()) as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Run the check process
     */
    public function run()
    {
        $this->printHeader();
        $this->checkControllers();
        $this->checkViews();
        $this->printReport();
    }

    /**
     * Get required view directories
     */
    private function getRequiredViewDirectories()
    {
        $dirs = [];
        foreach (array_keys($this->requiredViews) as $view) {
            $dirs[] = $this->viewsPath . '/' . dirname($view);
        }
        return array_unique($dirs);
    }

    /**
     * Check required controllers
     */
    private function checkControllers()
    {
        foreach ($this->requiredControllers as $controller => $methods) {
            $filename = "{$controller}Controller.php";
            $path = "{$this->controllersPath}/{$filename}";
            
            if (!file_exists($path)) {
                $this->createController($controller, $methods);
            } else {
                $this->existing[] = "controllers/{$filename}";
            }
        }
    }

    /**
     * Check required views
     */
    private function checkViews()
    {
        foreach ($this->requiredViews as $view => $type) {
            $path = "{$this->viewsPath}/{$view}";
            
            if (!file_exists($path)) {
                $this->createView($view, $type);
            } else {
                $this->existing[] = "views/{$view}";
            }
        }
    }

    /**
     * Create a controller file
     */
    private function createController($name, $methods)
    {
        $filename = "{$name}Controller.php";
        $path = "{$this->controllersPath}/{$filename}";

        $template = $this->getControllerTemplate($name, $methods);

        if (file_put_contents($path, $template)) {
            $this->created[] = "controllers/{$filename}";
        } else {
            $this->errors[] = "Failed to create controller: {$filename}";
        }
    }

    /**
     * Create a view file
     */
    private function createView($view, $type)
    {
        $path = "{$this->viewsPath}/{$view}";
        
        $template = $this->getViewTemplate($view, $type);

        if (file_put_contents($path, $template)) {
            $this->created[] = "views/{$view}";
        } else {
            $this->errors[] = "Failed to create view: {$view}";
        }
    }

    /**
     * Get controller file template
     */
    private function getControllerTemplate($name, $methods)
    {
        $methodsCode = array_map(function($method) {
            return <<<EOT
    
    /**
     * {$method} action
     */
    public function {$method}()
    {
        // Implementation
    }
EOT;
        }, $methods);

        return <<<EOT
<?php
/**
 * {$name} Controller
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class {$name}Controller
{
    private \$pdo;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        \$this->pdo = require dirname(__DIR__, 2) . '/config/database.php';
    }
{$this->indent(implode("", $methodsCode), 0)}
}
EOT;
    }

    /**
     * Get view file template
     */
    private function getViewTemplate($view, $type)
    {
        switch ($type) {
            case 'layout':
                return $this->getLayoutTemplate($view);
            case 'profile':
                return $this->getProfileTemplate($view);
            case 'match':
                return $this->getMatchTemplate();
            case 'horoscope':
                return $this->getHoroscopeTemplate();
            case 'chat':
                return $this->getChatTemplate();
            case 'admin':
                return $this->getAdminTemplate($view);
            default:
                return $this->getDefaultTemplate($view);
        }
    }

    /**
     * Get layout template (header/footer)
     */
    private function getLayoutTemplate($view)
    {
        if (strpos($view, 'header') !== false) {
            return <<<'EOT'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandawatha.lk - Sri Lankan Matrimonial Site</title>
    <link href="/assets/css/tailwind.min.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/main.js"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <a href="/" class="flex items-center">
                        <span class="text-2xl font-bold text-purple-600">Sandawatha.lk</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <!-- Navigation items will go here -->
                </div>
            </div>
        </div>
    </nav>
    <main class="container mx-auto px-4 py-8">
EOT;
        } else {
            return <<<'EOT'
    </main>
    <footer class="bg-white shadow-lg mt-8">
        <div class="max-w-7xl mx-auto py-6 px-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600">&copy; <?php echo date('Y'); ?> Sandawatha.lk - All rights reserved</p>
                </div>
                <div>
                    <!-- Footer links will go here -->
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
EOT;
        }
    }

    /**
     * Get profile template
     */
    private function getProfileTemplate($view)
    {
        if (strpos($view, 'view') !== false) {
            return <<<'EOT'
<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="bg-white shadow rounded-lg p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <!-- Profile Photo Section -->
            <div class="text-center">
                <div class="relative">
                    <img src="/assets/images/default-profile.jpg" alt="Profile Photo" 
                         class="w-48 h-48 rounded-full mx-auto object-cover">
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            <!-- Profile Details Section -->
            <div class="space-y-4">
                <h1 class="text-2xl font-bold text-gray-900">
                    <?php echo htmlspecialchars($user->getFirstName() . ' ' . $user->getLastName()); ?>
                </h1>
                <!-- Other profile details will go here -->
            </div>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/shared/footer.php'; ?>
EOT;
        } else {
            return <<<'EOT'
<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Profile</h1>
    
    <form action="/profile/update" method="POST" enctype="multipart/form-data" class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" name="first_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" name="last_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <!-- Other form fields will go here -->
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                Save Changes
            </button>
        </div>
    </form>
</div>

<?php require dirname(__DIR__) . '/shared/footer.php'; ?>
EOT;
        }
    }

    /**
     * Get match template
     */
    private function getMatchTemplate()
    {
        return <<<'EOT'
<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="space-y-6">
    <!-- Search Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Search Filters</h2>
        <form action="/match/search" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Filter fields will go here -->
        </form>
    </div>

    <!-- Match Results -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Match cards will go here -->
    </div>
</div>

<script src="/assets/js/match.js"></script>
<?php require dirname(__DIR__) . '/shared/footer.php'; ?>
EOT;
    }

    /**
     * Get horoscope template
     */
    private function getHoroscopeTemplate()
    {
        return <<<'EOT'
<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Horoscope Matching</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Partner 1 Horoscope -->
        <div class="border rounded-lg p-4">
            <h2 class="text-xl font-semibold mb-4">Your Horoscope</h2>
            <!-- Horoscope details will go here -->
        </div>

        <!-- Partner 2 Horoscope -->
        <div class="border rounded-lg p-4">
            <h2 class="text-xl font-semibold mb-4">Partner's Horoscope</h2>
            <!-- Partner horoscope details will go here -->
        </div>
    </div>

    <!-- Matching Results -->
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-4">Matching Results</h2>
        <!-- Results will go here -->
    </div>
</div>

<?php require dirname(__DIR__) . '/shared/footer.php'; ?>
EOT;
    }

    /**
     * Get chat template
     */
    private function getChatTemplate()
    {
        return <<<'EOT'
<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Chat List -->
    <div class="md:col-span-1 bg-white shadow rounded-lg">
        <div class="p-4">
            <h2 class="text-xl font-semibold mb-4">Messages</h2>
            <div class="space-y-2">
                <!-- Chat list items will go here -->
            </div>
        </div>
    </div>

    <!-- Chat Window -->
    <div class="md:col-span-3 bg-white shadow rounded-lg">
        <div class="flex flex-col h-[600px]">
            <!-- Chat Header -->
            <div class="p-4 border-b">
                <h3 class="text-lg font-semibold">Chat with <span id="chat-partner-name"></span></h3>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 p-4 overflow-y-auto" id="messages-container">
                <!-- Messages will go here -->
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t">
                <form id="message-form" class="flex gap-2">
                    <input type="text" id="message-input" 
                           class="flex-1 rounded-md border-gray-300 shadow-sm"
                           placeholder="Type your message...">
                    <button type="submit" 
                            class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/chat.js"></script>
<?php require dirname(__DIR__) . '/shared/footer.php'; ?>
EOT;
    }

    /**
     * Get admin template
     */
    private function getAdminTemplate($view)
    {
        if (strpos($view, 'dashboard') !== false) {
            return <<<'EOT'
<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900">Total Users</h3>
            <p class="text-3xl font-bold text-purple-600">0</p>
        </div>
        <!-- Other stat cards will go here -->
    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Activity</h2>
        <!-- Activity list will go here -->
    </div>
</div>

<?php require dirname(__DIR__) . '/shared/footer.php'; ?>
EOT;
        } elseif (strpos($view, 'users') !== false) {
            return <<<'EOT'
<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
        <div class="flex gap-2">
            <!-- Action buttons will go here -->
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <!-- Other table headers will go here -->
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- User rows will go here -->
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/shared/footer.php'; ?>
EOT;
        } else {
            return <<<'EOT'
<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
    </div>

    <!-- Reports Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Report ID
                    </th>
                    <!-- Other table headers will go here -->
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Report rows will go here -->
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/shared/footer.php'; ?>
EOT;
        }
    }

    /**
     * Get default template
     */
    private function getDefaultTemplate($view)
    {
        return <<<EOT
<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{$view}</h1>
    <!-- Content will go here -->
</div>

<?php require dirname(__DIR__) . '/shared/footer.php'; ?>
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
        echo "║      Sandawatha.lk Controllers & Views Files Check        ║\n";
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
            echo COLOR_GREEN . "\n✓ All required controller and view files are present!\n" . COLOR_RESET;
        }

        echo "\nTotal Summary:\n";
        echo "  • Created: " . count($this->created) . " files\n";
        echo "  • Existing: " . count($this->existing) . " files\n";
        echo "  • Errors: " . count($this->errors) . " files\n\n";

        if (!empty($this->created)) {
            echo COLOR_YELLOW . "Note: Created files are templates.\n";
            echo "Please implement the required functionality in controllers and customize the views.\n" . COLOR_RESET . "\n";
        }
    }
}

// Run the check
$check = new ControllersViewsCheck();
$check->run(); 