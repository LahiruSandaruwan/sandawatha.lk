<?php
/**
 * Asset Files Check and Setup Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script checks and creates required CSS and JS files.
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

class AssetSetup
{
    private $projectRoot;
    private $created = [];
    private $existing = [];
    private $errors = [];

    // Define required asset files
    private $requiredAssets = [
        'css' => [
            'tailwind.css' => "/* Tailwind CSS - Will be populated by build process */\n",
            'main.css' => "/* Main Styles for Sandawatha.lk */\n\n:root {\n    --primary-color: #FF4B91;\n    --secondary-color: #FF7676;\n    --accent-color: #FFE5E5;\n}\n",
            'custom.css' => "/* Custom styles for Sandawatha.lk */\n"
        ],
        'js' => [
            'jquery.min.js' => "/* jQuery will be downloaded during build process */\n",
            'main.js' => "// Main JavaScript for Sandawatha.lk\n\ndocument.addEventListener('DOMContentLoaded', function() {\n    console.log('Sandawatha.lk - Main JS Loaded');\n});\n",
            'chat.js' => "// Chat functionality for Sandawatha.lk\n\nconst ChatModule = {\n    init: function() {\n        console.log('Chat Module Initialized');\n    }\n};\n",
            'match.js' => "// Match functionality for Sandawatha.lk\n\nconst MatchModule = {\n    init: function() {\n        console.log('Match Module Initialized');\n    }\n};\n"
        ]
    ];

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__);
    }

    /**
     * Run the asset check and setup
     */
    public function run()
    {
        $this->printHeader();
        
        foreach ($this->requiredAssets as $type => $files) {
            $this->checkAssetType($type, $files);
        }
        
        $this->printReport();
    }

    /**
     * Check and create assets for a specific type (css/js)
     */
    private function checkAssetType($type, $files)
    {
        $assetPath = $this->projectRoot . "/public/assets/{$type}";
        
        // Ensure the directory exists
        if (!file_exists($assetPath)) {
            if (!mkdir($assetPath, 0755, true)) {
                $this->errors[] = "Failed to create directory: assets/{$type}";
                return;
            }
        }

        // Check each required file
        foreach ($files as $filename => $content) {
            $filePath = "{$assetPath}/{$filename}";
            
            if (!file_exists($filePath)) {
                if (file_put_contents($filePath, $content) !== false) {
                    $this->created[] = "assets/{$type}/{$filename}";
                } else {
                    $this->errors[] = "Failed to create file: assets/{$type}/{$filename}";
                }
            } else {
                $this->existing[] = "assets/{$type}/{$filename}";
            }
        }
    }

    /**
     * Print script header
     */
    private function printHeader()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║             Sandawatha.lk Asset Files Check               ║\n";
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
                echo "  • $file\n";
            }
        }

        if (!empty($this->existing)) {
            echo COLOR_YELLOW . "\n✓ Already existing files:\n" . COLOR_RESET;
            foreach ($this->existing as $file) {
                echo "  • $file\n";
            }
        }

        if (!empty($this->errors)) {
            echo COLOR_RED . "\n✗ Errors occurred:\n" . COLOR_RESET;
            foreach ($this->errors as $error) {
                echo "  • $error\n";
            }
        }

        if (empty($this->created) && empty($this->errors)) {
            echo COLOR_GREEN . "\n✓ All required asset files are already present!\n" . COLOR_RESET;
        }

        echo "\nTotal Summary:\n";
        echo "  • Created: " . count($this->created) . " files\n";
        echo "  • Existing: " . count($this->existing) . " files\n";
        echo "  • Errors: " . count($this->errors) . " files\n\n";

        if (!empty($this->created)) {
            echo COLOR_YELLOW . "Note: Some created files are placeholders. Remember to:\n";
            echo "1. Run 'npm install' to get the latest Tailwind CSS\n";
            echo "2. Download jQuery from https://jquery.com/download/\n";
            echo "3. Update the placeholder files with your actual code\n" . COLOR_RESET . "\n";
        }
    }
}

// Run the asset setup
$setup = new AssetSetup();
$setup->run(); 