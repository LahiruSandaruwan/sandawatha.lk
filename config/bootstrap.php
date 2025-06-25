<?php
// Start output buffering immediately
ob_start();

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

// Load configuration
$config = require_once CONFIG_PATH . '/routes.php';

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

// Function to get current route
function getCurrentRoute() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $route = '/' . trim($uri, '/');
    $route = $route === '//' ? '/' : $route;
    error_log("Detected route: " . $route);
    error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
    return $route;
}

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

// Enhanced redirect function with security headers
function redirect($path) {
    if (!headers_sent()) {
        // Security headers
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        // Redirect
        header("Location: $path");
        exit;
    }
    echo '<script>window.location.href="' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '";</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '"></noscript>';
    exit;
}

// Function to get error page with proper path resolution
function getErrorPage($code) {
    global $config;
    $errorPage = isset($config['errorPages'][$code]) 
        ? ROOT_PATH . '/' . $config['errorPages'][$code]
        : ROOT_PATH . '/app/pages/errors/' . $code . '.php';
    
    if (!file_exists($errorPage)) {
        // Fallback to 500 if specific error page doesn't exist
        $errorPage = ROOT_PATH . '/app/pages/errors/500.php';
    }
    
    return $errorPage;
}

// Enhanced authentication check functions
function requiresAuth($route) {
    global $config;
    return isset($config['authRequired']) && in_array($route, $config['authRequired']);
}

function requiresAdmin($route) {
    global $config;
    return isset($config['adminRequired']) && in_array($route, $config['adminRequired']);
}

function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Enhanced URL helper functions
function baseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return "$protocol://$host";
}

function asset($path) {
    $path = ltrim($path, '/');
    $timestamp = @filemtime(PUBLIC_PATH . '/assets/' . $path) ?: time();
    return baseUrl() . '/assets/' . $path . '?v=' . $timestamp;
}

function storage($path) {
    return baseUrl() . '/storage/' . ltrim($path, '/');
}

// Enhanced security functions
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function getCurrentPage() {
    $route = getCurrentRoute();
    return trim($route, '/') ?: 'home';
}

function routeExists($route) {
    global $config;
    $route = $route === '' ? '/' : $route;
    return isset($config['routes'][$route]);
}

function getRouteFile($route) {
    global $config;
    if (!isset($config['routes'][$route])) {
        throw new Exception("Route not found: $route");
    }
    $file = ROOT_PATH . '/' . $config['routes'][$route];
    if (!file_exists($file)) {
        throw new Exception("Route file not found: $file");
    }
    return $file;
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