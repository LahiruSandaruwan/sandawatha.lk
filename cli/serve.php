#!/usr/bin/env php
<?php
/**
 * Local Development Server Script
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * This script runs the project using PHP's built-in development server.
 * Usage: php cli/serve.php [port]
 */

// Ensure script is run from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

// Basic configuration
$defaultPort = 8000;
$maxPortTries = 3;
$publicPath = dirname(__DIR__) . '/public';

/**
 * Print colored message
 */
function printMessage($message, $type = 'info') {
    $colors = [
        'success' => "\033[32m✅ ",
        'warning' => "\033[33m⚠️ ",
        'error'   => "\033[31m❌ ",
        'info'    => "\033[34mℹ️ ",
    ];
    $reset = "\033[0m";
    
    echo ($colors[$type] ?? '') . $message . $reset . PHP_EOL;
}

/**
 * Check if a port is available
 */
function isPortAvailable($port) {
    $sock = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
    if ($sock) {
        fclose($sock);
        return false;
    }
    return true;
}

/**
 * Find next available port
 */
function findAvailablePort($startPort, $maxTries) {
    $port = $startPort;
    for ($i = 0; $i < $maxTries; $i++) {
        if (isPortAvailable($port)) {
            return $port;
        }
        $port++;
    }
    return null;
}

/**
 * Show manual server command
 */
function showManualCommand($port, $publicPath) {
    printMessage("Unable to launch server automatically. Please run manually:", 'error');
    echo "cd " . dirname(__DIR__) . " && php -S localhost:$port -t " . basename($publicPath) . PHP_EOL;
}

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    printMessage("PHP 7.4.0 or higher is required. Your version: " . PHP_VERSION, 'error');
    exit(1);
}

// Check if public directory exists
if (!is_dir($publicPath)) {
    printMessage("Public directory not found: $publicPath", 'error');
    exit(1);
}

// Get port from command line argument or use default
$port = isset($argv[1]) ? (int)$argv[1] : $defaultPort;

// Check if port is available
if (!isPortAvailable($port)) {
    $newPort = findAvailablePort($port + 1, $maxPortTries - 1);
    if ($newPort === null) {
        printMessage("No available ports found between $port and " . ($port + $maxPortTries - 1), 'error');
        exit(1);
    }
    printMessage("Port $port is in use. Using port $newPort instead.", 'warning');
    $port = $newPort;
}

// Display startup banner
echo "\nSandawatha.lk - Local Development Server\n";
echo "═══════════════════════════════════════\n\n";

// Build server command
$command = sprintf(
    'php -S localhost:%d -t %s',
    $port,
    escapeshellarg($publicPath)
);

// Display startup information
printMessage("Starting development server...", 'success');
printMessage("Make sure XAMPP's MySQL service is running and .env file is correctly configured.", 'warning');
echo "\n";

// Try to start the server
try {
    // Start the server based on OS
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows - run in background
        $handle = popen("start /B " . $command, "r");
        if ($handle === false) {
            throw new Exception("Failed to start server");
        }
        pclose($handle);
    } else {
        // Unix - use exec
        exec($command . " 2>&1", $output, $returnCode);
        if ($returnCode !== 0) {
            throw new Exception(implode("\n", $output));
        }
    }

    // Display success message
    printMessage("Sandawatha.lk is now running at: http://localhost:$port", 'success');
    printMessage("Press Ctrl+C to stop the server", 'warning');
    echo "\n";

} catch (Exception $e) {
    // If server fails to start, show manual command
    printMessage("Failed to start server: " . $e->getMessage(), 'error');
    echo "\n";
    showManualCommand($port, $publicPath);
    exit(1);
} 