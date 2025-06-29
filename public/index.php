<?php
/**
 * Sandawatha.lk - Main Entry Point
 * 
 * This file serves as the entry point for all requests to the application.
 * It loads the router and handles the current request.
 */

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load router
require_once ROOT_PATH . '/routes/router.php';

// Start output buffering
ob_start();

try {
    // Handle the current request
    handleRequest();
} catch (Exception $e) {
    // Log error
    error_log("Application error: " . $e->getMessage());
    
    // Set HTTP status code
    $statusCode = $e->getCode() ?: 500;
    if (!headers_sent()) {
        http_response_code($statusCode);
    }
    
    // Show error page
    switch ($statusCode) {
        case 404:
            require ROOT_PATH . '/app/pages/errors/404.php';
            break;
        case 403:
            require ROOT_PATH . '/app/pages/errors/403.php';
            break;
        default:
            require ROOT_PATH . '/app/pages/errors/500.php';
            break;
    }
}

// Flush output buffer
ob_end_flush(); 