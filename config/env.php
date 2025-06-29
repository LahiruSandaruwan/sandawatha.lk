<?php
/**
 * Environment Configuration
 * Loads environment variables from .env file
 */

// Set session configuration
$sessionConfig = [
    'cookie_httponly' => 1,
    'use_only_cookies' => 1,
    'cookie_secure' => false, // Will be set to true in production
    'cookie_samesite' => 'Lax'
];

// Load environment variables from .env file
function loadEnv() {
    $envFile = __DIR__ . '/../.env';
    
    // Check if .env file exists
    if (!file_exists($envFile)) {
        // Create default .env file if it doesn't exist
        $defaultEnv = [
            'APP_NAME=Sandawatha.lk',
            'APP_ENV=local',
            'APP_DEBUG=true',
            'APP_URL=http://localhost',
            'APP_TIMEZONE=Asia/Colombo',
            '',
            'DB_HOST=localhost',
            'DB_PORT=3306',
            'DB_NAME=sandawatha',
            'DB_USER=root',
            'DB_PASS=',
            '',
            'MAIL_HOST=smtp.mailtrap.io',
            'MAIL_PORT=2525',
            'MAIL_USER=null',
            'MAIL_PASS=null',
            'MAIL_FROM=noreply@sandawatha.lk',
            'MAIL_NAME=Sandawatha.lk',
            '',
            'STRIPE_KEY=',
            'STRIPE_SECRET=',
            'STRIPE_WEBHOOK_SECRET=',
            '',
            'GOOGLE_CLIENT_ID=',
            'GOOGLE_CLIENT_SECRET=',
            '',
            'FACEBOOK_APP_ID=',
            'FACEBOOK_APP_SECRET=',
            '',
            'RECAPTCHA_SITE_KEY=',
            'RECAPTCHA_SECRET_KEY=',
            '',
            'AWS_ACCESS_KEY_ID=',
            'AWS_SECRET_ACCESS_KEY=',
            'AWS_DEFAULT_REGION=ap-south-1',
            'AWS_BUCKET=',
            '',
            'PUSHER_APP_ID=',
            'PUSHER_APP_KEY=',
            'PUSHER_APP_SECRET=',
            'PUSHER_APP_CLUSTER=mt1',
            '',
            'OPENAI_API_KEY='
        ];
        
        file_put_contents($envFile, implode("\n", $defaultEnv));
    }
    
    // Read .env file
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse valid lines
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove quotes if present
            if (preg_match('/^(["\']).*\1$/', $value)) {
                $value = substr($value, 1, -1);
            }
            
            // Set environment variable
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

// Load environment variables
loadEnv();

/**
 * Get environment variable
 * 
 * @param string $key Variable name
 * @param mixed $default Default value
 * @return mixed
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
        
        case 'empty':
        case '(empty)':
            return '';
    }
    
    return $value;
}

// Set error reporting based on environment
if (env('APP_DEBUG', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    define('DEBUG_MODE', true);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    define('DEBUG_MODE', false);
}

// Set timezone
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

// Update session cookie secure setting based on environment
if (env('APP_ENV') === 'production') {
    ini_set('session.cookie_secure', 1);
}

// Define common constants
if (!defined('APP_NAME')) define('APP_NAME', env('APP_NAME', 'Sandawatha.lk'));
if (!defined('APP_ENV')) define('APP_ENV', env('APP_ENV', 'local'));
if (!defined('APP_URL')) define('APP_URL', env('APP_URL', 'http://localhost'));

// Define path constants
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
if (!defined('APP_PATH')) define('APP_PATH', ROOT_PATH . '/app');
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT_PATH . '/config');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', ROOT_PATH . '/storage');
if (!defined('CACHE_PATH')) define('CACHE_PATH', STORAGE_PATH . '/cache');
if (!defined('LOGS_PATH')) define('LOGS_PATH', STORAGE_PATH . '/logs');
if (!defined('UPLOADS_PATH')) define('UPLOADS_PATH', STORAGE_PATH . '/uploads');

// Create storage directories if they don't exist
$directories = [
    STORAGE_PATH,
    CACHE_PATH,
    LOGS_PATH,
    UPLOADS_PATH,
    UPLOADS_PATH . '/photos',
    UPLOADS_PATH . '/documents',
    UPLOADS_PATH . '/temp'
];

foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
}

// Set error log file
ini_set('error_log', LOGS_PATH . '/php-error.log'); 