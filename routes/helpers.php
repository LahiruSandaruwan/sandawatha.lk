<?php
/**
 * Helper Functions for Sandawatha.lk
 * 
 * This file contains common helper functions used throughout the application.
 */

/**
 * Get the base URL of the application
 * 
 * @return string The base URL
 */
function baseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return "$protocol://$host";
}

/**
 * Get the URL for an asset
 * 
 * @param string $path The path to the asset
 * @return string The asset URL with cache-busting
 */
function asset($path) {
    $path = ltrim($path, '/');
    $assetPath = ROOT_PATH . '/public/assets/' . $path;
    $timestamp = @filemtime($assetPath) ?: time();
    return baseUrl() . '/assets/' . $path . '?v=' . $timestamp;
}

/**
 * Get the URL for a storage item
 * 
 * @param string $path The path to the storage item
 * @return string The storage URL
 */
function storage($path) {
    return baseUrl() . '/storage/' . ltrim($path, '/');
}

/**
 * Escape a string for HTML output
 * 
 * @param string $string The string to escape
 * @return string The escaped string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Generate or get a CSRF token
 * 
 * @return string The CSRF token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a CSRF token
 * 
 * @param string $token The token to verify
 * @return bool True if valid, false otherwise
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get the current page name
 * 
 * @return string The current page name
 */
function getCurrentPage() {
    $route = getCurrentRoute();
    return trim($route, '/') ?: 'home';
}

/**
 * Handle JSON API responses
 * 
 * @param mixed $data The data to return
 * @param int $status The HTTP status code
 * @return void
 */
function jsonResponse($data, $status = 200) {
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code($status);
    }
    echo json_encode($data);
    exit;
}

/**
 * Validate the HTTP request method
 * 
 * @param string $method The expected method
 * @return void
 */
function validateRequestMethod($method) {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
}

/**
 * Get POST data safely (JSON or form data)
 * 
 * @return array The POST data
 */
function getPostData() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = $_POST;
    }
    return $data;
} 