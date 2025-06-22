<?php

/**
 * Stop Script for Sandawatha.lk
 * 
 * This script:
 * 1. Checks if server is running
 * 2. Stops the server process
 * 3. Cleans up PID file
 */

// ANSI color codes
define('GREEN', "\033[0;32m");
define('YELLOW', "\033[1;33m");
define('RED', "\033[0;31m");
define('NC', "\033[0m"); // No Color

// Configuration
define('PID_FILE', __DIR__ . '/../storage/server.pid');

/**
 * Print colored message
 */
function println($message, $color = NC) {
    echo $color . $message . NC . PHP_EOL;
}

/**
 * Main execution
 */
try {
    if (!file_exists(PID_FILE)) {
        println("Server is not running.", YELLOW);
        exit(0);
    }

    $pid = file_get_contents(PID_FILE);
    
    if (!$pid || !is_numeric($pid)) {
        println("Invalid PID file. Cleaning up...", YELLOW);
        unlink(PID_FILE);
        exit(1);
    }

    // Try to kill the process
    if (posix_kill($pid, SIGTERM)) {
        unlink(PID_FILE);
        println("✅ Server stopped successfully!", GREEN);
    } else {
        println("⚠️ Server process not found. Cleaning up...", YELLOW);
        unlink(PID_FILE);
    }

} catch (Exception $e) {
    println("Error: " . $e->getMessage(), RED);
    exit(1);
} 