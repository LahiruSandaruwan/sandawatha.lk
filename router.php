<?php
/**
 * Router for PHP's built-in development server
 * 
 * This file is used only when running the application with PHP's built-in server.
 * It serves static files directly and routes all other requests through the main router.
 */

if (php_sapi_name() === 'cli-server') {
    // Static file check
    if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|ico|svg|woff2?|ttf|eot)$/', $_SERVER["REQUEST_URI"])) {
        return false; // Serve the requested file as-is
    }
    
    // Rewrite all other requests to index.php
    require __DIR__ . '/public/index.php';
} 