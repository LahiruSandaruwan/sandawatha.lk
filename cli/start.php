<?php

/**
 * Start Script for Sandawatha.lk
 * 
 * This script:
 * 1. Checks if server is already running
 * 2. Creates necessary directories
 * 3. Starts the PHP development server
 * 4. Saves PID for later use
 */

// ANSI color codes
define('GREEN', "\033[0;32m");
define('YELLOW', "\033[1;33m");
define('RED', "\033[0;31m");
define('NC', "\033[0m"); // No Color

// Configuration
define('PORT', 8000);
define('PID_FILE', __DIR__ . '/../storage/server.pid');
define('LOG_FILE', __DIR__ . '/../storage/logs/server.log');
define('PUBLIC_DIR', __DIR__ . '/../public');

/**
 * Print colored message
 */
function println($message, $color = NC) {
    echo $color . $message . NC . PHP_EOL;
}

/**
 * Check if server is already running
 */
function isServerRunning() {
    if (file_exists(PID_FILE)) {
        $pid = file_get_contents(PID_FILE);
        if (posix_kill($pid, 0)) {
            return true;
        }
        // Clean up stale PID file
        unlink(PID_FILE);
    }
    return false;
}

/**
 * Create necessary directories
 */
function createDirectories() {
    $dirs = [
        dirname(LOG_FILE),
        dirname(PID_FILE),
        __DIR__ . '/../storage/profiles',
        __DIR__ . '/../storage/ids'
    ];

    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}

/**
 * Main execution
 */
try {
    // Check if server is already running
    if (isServerRunning()) {
        println("Server is already running on port " . PORT, YELLOW);
        exit(1);
    }

    // Create necessary directories
    createDirectories();

    // Clear old log file
    file_put_contents(LOG_FILE, '');

    // Start server
    println("\nSandawatha.lk - Local Development Server", GREEN);
    println("═══════════════════════════════════════\n");

    println("✅ Starting development server...");
    println("⚠️ Make sure MySQL service is running and .env file is correctly configured.\n");

    $command = sprintf(
        'php -S localhost:%d -t %s > %s 2>&1 & echo $! > %s',
        PORT,
        escapeshellarg(PUBLIC_DIR),
        escapeshellarg(LOG_FILE),
        escapeshellarg(PID_FILE)
    );

    exec($command);

    println("🚀 Server started successfully!");
    println("📝 View logs: tail -f " . LOG_FILE);
    println("🌐 Visit: http://localhost:" . PORT . "\n");

} catch (Exception $e) {
    println("Error: " . $e->getMessage(), RED);
    exit(1);
} 