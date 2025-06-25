<?php
// Load bootstrap
require_once __DIR__ . '/../config/bootstrap.php';

try {
    // Get current route
    $route = getCurrentRoute();
    
    // Check if route exists
    if (!routeExists($route)) {
        throw new Exception('Route not found', 404);
    }
    
    // Check authentication
    if (requiresAuth($route) && !isAuthenticated()) {
        $_SESSION['redirect_after_login'] = $route;
        redirect('/login');
    }
    
    // Check admin access
    if (requiresAdmin($route) && !isAdmin()) {
        throw new Exception('Access denied', 403);
    }
    
    // Get route file
    $routeFile = getRouteFile($route);
    
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
    $content = loadView($routeFile);
    require_once APP_PATH . '/views/shared/layout.php';
    
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 500;
    if (!headers_sent()) {
        http_response_code($statusCode);
    }
    
    // Load error page
    $errorPage = getErrorPage($statusCode);
    if (file_exists($errorPage)) {
        require $errorPage;
    } else {
        echo '<h1>' . $statusCode . ' Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

// Flush output buffer
ob_end_flush(); 