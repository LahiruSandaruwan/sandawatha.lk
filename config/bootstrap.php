<?php
// Start output buffering immediately
ob_start();

// Apply session configuration if available
if (isset($sessionConfig) && is_array($sessionConfig)) {
    foreach ($sessionConfig as $key => $value) {
        ini_set("session.$key", $value);
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Bootstrap File
 * Initializes core functionality and error handling
 */

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define constants only if not already defined
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
if (!defined('APP_PATH')) define('APP_PATH', ROOT_PATH . '/app');
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT_PATH . '/config');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', ROOT_PATH . '/storage');

// Set default timezone
date_default_timezone_set('Asia/Colombo');

// Register error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // Error is not specified in error_reporting
        return false;
    }

    $errfile = str_replace(ROOT_PATH, '', $errfile);
    $error_message = sprintf("Error [%d]: %s\nFile: %s\nLine: %d", $errno, $errstr, $errfile, $errline);
    error_log($error_message);

    if (ini_get('display_errors')) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    return true;
});

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $errfile = str_replace(ROOT_PATH, '', $error['file']);
        $error_message = sprintf(
            "Fatal Error [%d]: %s\nFile: %s\nLine: %d",
            $error['type'],
            $error['message'],
            $errfile,
            $error['line']
        );
        error_log($error_message);

        if (ini_get('display_errors')) {
            http_response_code(500);
            if (!headers_sent()) {
                header('Content-Type: text/html; charset=utf-8');
            }
            echo '<h1>500 Internal Server Error</h1>';
            echo '<pre>' . htmlspecialchars($error_message) . '</pre>';
        }
    }
});

// Function to load view with proper error handling
function loadView($viewPath, $data = []) {
    if (!file_exists($viewPath)) {
        throw new Exception("View not found: $viewPath");
    }
    
    // Extract data to make it available in view
    if (is_array($data)) {
        extract($data);
    }
    
    // Start output buffering for the view
    ob_start();
    require $viewPath;
    return ob_get_clean();
}

// Function to handle API responses
function jsonResponse($data, $status = 200) {
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code($status);
    }
    echo json_encode($data);
    exit;
}

// Function to validate request method
function validateRequestMethod($method) {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
}

// Function to get POST data safely
function getPostData() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = $_POST;
    }
    return $data;
} 