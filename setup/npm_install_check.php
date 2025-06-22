<?php
/**
 * NPM Dependencies Check and Installation Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script checks if node_modules exists and runs npm install if needed.
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

class NpmSetup
{
    private $projectRoot;
    private $packageJsonContent = '{
  "name": "sandawatha",
  "version": "1.0.0",
  "description": "Sri Lankan Matrimonial Website",
  "scripts": {
    "build:css": "tailwindcss -i ./public/assets/css/tailwind.css -o ./public/assets/css/tailwind.min.css --minify",
    "watch:css": "tailwindcss -i ./public/assets/css/tailwind.css -o ./public/assets/css/tailwind.min.css --watch"
  },
  "dependencies": {
    "jquery": "^3.7.1"
  },
  "devDependencies": {
    "tailwindcss": "^3.4.1",
    "@tailwindcss/forms": "^0.5.7",
    "autoprefixer": "^10.4.17",
    "postcss": "^8.4.35"
  }
}';

    private $tailwindConfigContent = '/** @type {import("tailwindcss").Config} */
module.exports = {
  content: [
    "./public/**/*.{php,html,js}",
    "./app/views/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: "#FF4B91",
        secondary: "#FF7676",
        accent: "#FFE5E5"
      },
      fontFamily: {
        sans: ["Inter", "system-ui", "sans-serif"]
      }
    }
  },
  plugins: [
    require("@tailwindcss/forms")
  ]
}';

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__);
    }

    /**
     * Run the NPM setup process
     */
    public function run()
    {
        $this->printHeader();

        // First check if npm is installed
        if (!$this->isNpmInstalled()) {
            $this->printError("npm is not installed. Please install Node.js and npm first.");
            return;
        }

        // Check and create package.json if needed
        $this->checkPackageJson();

        // Check and create tailwind.config.js if needed
        $this->checkTailwindConfig();

        // Check node_modules
        if ($this->isNodeModulesExists()) {
            $this->printSuccess("Dependencies are already installed.");
            
            // Verify if all required packages are installed
            if ($this->verifyDependencies()) {
                $this->printSuccess("All required packages are properly installed.");
            } else {
                $this->printWarning("Some packages might be missing. Running npm install...");
                $this->runNpmInstall();
            }
        } else {
            $this->printInfo("Installing dependencies...");
            $this->runNpmInstall();
        }
    }

    /**
     * Check if npm is installed
     */
    private function isNpmInstalled()
    {
        exec('npm --version 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Check if node_modules exists
     */
    private function isNodeModulesExists()
    {
        return is_dir($this->projectRoot . '/node_modules');
    }

    /**
     * Check and create package.json if needed
     */
    private function checkPackageJson()
    {
        $packageJsonPath = $this->projectRoot . '/package.json';
        
        if (!file_exists($packageJsonPath)) {
            $this->printInfo("Creating package.json...");
            if (file_put_contents($packageJsonPath, $this->packageJsonContent)) {
                $this->printSuccess("Created package.json");
            } else {
                $this->printError("Failed to create package.json");
            }
        }
    }

    /**
     * Check and create tailwind.config.js if needed
     */
    private function checkTailwindConfig()
    {
        $tailwindConfigPath = $this->projectRoot . '/tailwind.config.js';
        
        if (!file_exists($tailwindConfigPath)) {
            $this->printInfo("Creating tailwind.config.js...");
            if (file_put_contents($tailwindConfigPath, $this->tailwindConfigContent)) {
                $this->printSuccess("Created tailwind.config.js");
            } else {
                $this->printError("Failed to create tailwind.config.js");
            }
        }
    }

    /**
     * Verify if all required packages are installed
     */
    private function verifyDependencies()
    {
        $requiredPackages = ['tailwindcss', 'jquery'];
        
        foreach ($requiredPackages as $package) {
            if (!is_dir($this->projectRoot . "/node_modules/{$package}")) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Run npm install
     */
    private function runNpmInstall()
    {
        $command = 'npm install 2>&1';
        $output = [];
        $returnCode = 0;

        // Change to project root directory
        chdir($this->projectRoot);

        // Execute npm install
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->printSuccess("Dependencies installed successfully!");
            
            // Copy jQuery to assets
            $this->copyJquery();
            
            // Run initial Tailwind build
            $this->buildTailwind();
        } else {
            $this->printError("Failed to install dependencies:");
            foreach ($output as $line) {
                echo "  {$line}\n";
            }
        }
    }

    /**
     * Copy jQuery to assets directory
     */
    private function copyJquery()
    {
        $source = $this->projectRoot . '/node_modules/jquery/dist/jquery.min.js';
        $destination = $this->projectRoot . '/public/assets/js/jquery.min.js';

        if (file_exists($source)) {
            if (copy($source, $destination)) {
                $this->printSuccess("Copied jQuery to assets directory");
            } else {
                $this->printError("Failed to copy jQuery to assets directory");
            }
        }
    }

    /**
     * Run initial Tailwind build
     */
    private function buildTailwind()
    {
        $this->printInfo("Building Tailwind CSS...");
        
        exec('npm run build:css 2>&1', $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->printSuccess("Tailwind CSS built successfully!");
        } else {
            $this->printError("Failed to build Tailwind CSS");
        }
    }

    /**
     * Print script header
     */
    private function printHeader()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║           Sandawatha.lk NPM Dependencies Setup            ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
    }

    /**
     * Print success message
     */
    private function printSuccess($message)
    {
        echo COLOR_GREEN . "✓ {$message}\n" . COLOR_RESET;
    }

    /**
     * Print error message
     */
    private function printError($message)
    {
        echo COLOR_RED . "✗ {$message}\n" . COLOR_RESET;
    }

    /**
     * Print warning message
     */
    private function printWarning($message)
    {
        echo COLOR_YELLOW . "! {$message}\n" . COLOR_RESET;
    }

    /**
     * Print info message
     */
    private function printInfo($message)
    {
        echo "→ {$message}\n";
    }
}

// Run the NPM setup
$setup = new NpmSetup();
$setup->run(); 