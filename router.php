<?php
// Router for PHP's built-in development server
if (php_sapi_name() === 'cli-server') {
    // Static file check
    if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|ico|svg|woff2?|ttf|eot)$/', $_SERVER["REQUEST_URI"])) {
        return false; // Serve the requested file as-is
    }
    
    // Debug route handling
    error_log("Request URI: " . $_SERVER["REQUEST_URI"]);
    
    // Rewrite all other requests to index.php
    require __DIR__ . '/public/index.php';
} 