<?php
/**
 * Global Helper Functions
 */

if (!function_exists('asset')) {
    /**
     * Generate a URL for an asset file
     * 
     * @param string $path The path to the asset file
     * @return string The full URL to the asset
     */
    function asset($path) {
        $path = trim($path, '/');
        return "/assets/{$path}" . (defined('DEBUG_MODE') && DEBUG_MODE ? "?v=" . time() : '');
    }
}

if (!function_exists('storage')) {
    /**
     * Generate a URL for a storage file
     * 
     * @param string $path The path to the storage file
     * @return string The full URL to the storage file
     */
    function storage($path) {
        $path = trim($path, '/');
        return "/storage/{$path}";
    }
}

if (!function_exists('url')) {
    /**
     * Generate a full URL for a path
     * 
     * @param string $path The path
     * @return string The full URL
     */
    function url($path = '') {
        $path = trim($path, '/');
        return "/{$path}";
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to another page
     * 
     * @param string $path The path to redirect to
     * @param int $status The HTTP status code
     * @return void
     */
    function redirect($path, $status = 302) {
        http_response_code($status);
        header("Location: " . url($path));
        exit();
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate or retrieve CSRF token
     * 
     * @return string The CSRF token
     */
    function csrf_token() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validate_csrf_token')) {
    /**
     * Validate CSRF token
     * 
     * @param string $token The token to validate
     * @return bool Whether the token is valid
     */
    function validate_csrf_token($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('old')) {
    /**
     * Get old form input value
     * 
     * @param string $key The input key
     * @param mixed $default Default value if not found
     * @return mixed The old input value
     */
    function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * Set flash message
     * 
     * @param string $message The message to flash
     * @param string $type The message type (success, error, warning, info)
     * @return void
     */
    function flash($message, $type = 'info') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
}

if (!function_exists('has_error')) {
    /**
     * Check if field has validation error
     * 
     * @param string $field The field name
     * @return bool Whether the field has error
     */
    function has_error($field) {
        return isset($_SESSION['errors'][$field]);
    }
}

if (!function_exists('get_error')) {
    /**
     * Get validation error for field
     * 
     * @param string $field The field name
     * @return string|null The error message
     */
    function get_error($field) {
        return $_SESSION['errors'][$field] ?? null;
    }
}

if (!function_exists('isAuthenticated')) {
    /**
     * Check if user is authenticated
     * 
     * @return bool Whether user is authenticated
     */
    function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check if user is admin
     * 
     * @return bool Whether user is admin
     */
    function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}

if (!function_exists('getCurrentPage')) {
    /**
     * Get current page identifier
     * 
     * @return string The current page identifier
     */
    function getCurrentPage() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = trim($path, '/');
        return $path ?: 'home';
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format date
     * 
     * @param string $date The date string
     * @param string $format The format string
     * @return string The formatted date
     */
    function formatDate($date, $format = 'Y-m-d') {
        return date($format, strtotime($date));
    }
}

if (!function_exists('sanitize')) {
    /**
     * Sanitize input
     * 
     * @param mixed $input The input to sanitize
     * @return mixed The sanitized input
     */
    function sanitize($input) {
        if (is_array($input)) {
            return array_map('sanitize', $input);
        }
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die
     * 
     * @param mixed ...$vars Variables to dump
     * @return void
     */
    function dd(...$vars) {
        if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
            return;
        }
        
        echo '<pre style="background:#fff;color:#000;padding:10px;margin:10px;border-radius:5px;border:1px solid #ddd;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit(1);
    }
}

if (!function_exists('config')) {
    /**
     * Get config value
     * 
     * @param string $key The config key
     * @param mixed $default Default value if not found
     * @return mixed The config value
     */
    function config($key, $default = null) {
        global $config;
        
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

if (!function_exists('view')) {
    /**
     * Render view
     * 
     * @param string $view The view name
     * @param array $data Data to pass to view
     * @return string The rendered view
     */
    function view($view, $data = []) {
        extract($data);
        
        ob_start();
        require_once APP_PATH . "/views/{$view}.php";
        $content = ob_get_clean();
        
        require_once APP_PATH . '/views/shared/layout.php';
    }
}

if (!function_exists('json')) {
    /**
     * Send JSON response
     * 
     * @param mixed $data The data to send
     * @param int $status HTTP status code
     * @return void
     */
    function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

if (!function_exists('abort')) {
    /**
     * Abort request with error page
     * 
     * @param int $code HTTP status code
     * @param string $message Error message
     * @return void
     */
    function abort($code, $message = '') {
        http_response_code($code);
        
        if (file_exists(APP_PATH . "/pages/errors/{$code}.php")) {
            require_once APP_PATH . "/pages/errors/{$code}.php";
        } else {
            echo "Error {$code}: {$message}";
        }
        
        exit();
    }
}

if (!function_exists('generateToken')) {
    /**
     * Generate secure random token
     * 
     * @param int $length Token length
     * @return string The generated token
     */
    function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
}

if (!function_exists('isAjax')) {
    /**
     * Check if request is AJAX
     * 
     * @return bool Whether request is AJAX
     */
    function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable
     * 
     * @param string $key The environment variable name
     * @param mixed $default Default value if not found
     * @return mixed The environment variable value
     */
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
        }
        
        return $value;
    }
}

// The routing functions have been moved to routes/router.php
// and routes/helpers.php 