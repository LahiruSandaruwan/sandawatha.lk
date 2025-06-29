<?php
/**
 * Main Router for Sandawatha.lk
 * 
 * This file handles all routing logic for the application.
 * It loads routes from routes.php, matches the requested URI,
 * and includes the appropriate file.
 */

// Define constants if not already defined
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
if (!defined('APP_PATH')) define('APP_PATH', ROOT_PATH . '/app');

// Include helpers
require_once ROOT_PATH . '/routes/helpers.php';

/**
 * Get the current route from the request URI
 * 
 * @return string The normalized route path
 */
function getCurrentRoute() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $route = '/' . trim($uri, '/');
    return $route === '//' ? '/' : $route;
}

/**
 * Check if the current user is authenticated
 * 
 * @return bool True if authenticated, false otherwise
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if the current user is an admin
 * 
 * @return bool True if admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Redirect to another URL
 * 
 * @param string $path The path to redirect to
 * @return void
 */
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

/**
 * Load and return the routes configuration
 * 
 * @return array The routes configuration
 */
function getRoutes() {
    return require ROOT_PATH . '/routes/routes.php';
}

/**
 * Check if a route requires authentication
 * 
 * @param string $route The route to check
 * @return bool True if authentication is required, false otherwise
 */
function requiresAuth($route) {
    // Routes that require authentication
    $authRoutes = [
        '/profile',
        '/profile/edit',
        '/chat',
        '/match',
        '/horoscope/match',
        '/settings',
        '/premium'
    ];
    
    // Check if the route or any parent route requires authentication
    foreach ($authRoutes as $authRoute) {
        if ($route === $authRoute || strpos($route, $authRoute . '/') === 0) {
            return true;
        }
    }
    
    return false;
}

/**
 * Check if a route requires admin access
 * 
 * @param string $route The route to check
 * @return bool True if admin access is required, false otherwise
 */
function requiresAdmin($route) {
    // Check if the route starts with /admin/
    return strpos($route, '/admin/') === 0;
}

/**
 * Get the file path for a route
 * 
 * @param string $route The route to get the file for
 * @param array $routes The routes configuration
 * @return string|null The file path or null if not found
 */
function getRouteFile($route, $routes) {
    if (isset($routes[$route])) {
        return ROOT_PATH . '/' . $routes[$route];
    }
    return null;
}

/**
 * Handle the current request
 * 
 * @return void
 */
function handleRequest() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Get current route and routes configuration
    $route = getCurrentRoute();
    $routes = getRoutes();
    
    // Get route file
    $routeFile = getRouteFile($route, $routes);
    
    // If route doesn't exist, show 404 page
    if ($routeFile === null || !file_exists($routeFile)) {
        http_response_code(404);
        require ROOT_PATH . '/app/pages/errors/404.php';
        return;
    }
    
    // Check authentication
    if (requiresAuth($route) && !isAuthenticated()) {
        $_SESSION['redirect_after_login'] = $route;
        redirect('/login');
        return;
    }
    
    // Check admin access
    if (requiresAdmin($route) && !isAdmin()) {
        http_response_code(403);
        require ROOT_PATH . '/app/pages/errors/403.php';
        return;
    }
    
    // Handle API routes
    if (strpos($route, '/api/') === 0) {
        // API security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        
        // CORS headers for API
        header('Access-Control-Allow-Origin: ' . baseUrl());
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
        
        require $routeFile;
        exit;
    }
    
    // For regular pages, load layout with content
    ob_start();
    require $routeFile;
    $content = ob_get_clean();
    require ROOT_PATH . '/app/views/shared/layout.php';
} 