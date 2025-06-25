<?php
// Define base routes
$routes = [
    // Public routes
    '/' => 'app/pages/home.php',
    '/about' => 'app/pages/about.php',
    '/contact' => 'app/pages/contact.php',
    '/login' => 'app/pages/login.php',
    '/register' => 'app/pages/register.php',
    '/privacy' => 'app/views/privacy.php',
    '/terms' => 'app/views/terms.php',
    
    // Auth required routes
    '/profile' => 'app/pages/profile/view.php',
    '/profile/edit' => 'app/pages/profile/edit.php',
    '/chat' => 'app/pages/chat/index.php',
    '/match' => 'app/pages/match/index.php',
    '/horoscope/match' => 'app/pages/horoscope/match.php',
    '/settings' => 'app/views/settings/index.php',
    '/premium' => 'app/views/premium/index.php',
    
    // Admin routes
    '/admin/dashboard' => 'app/pages/admin/dashboard.php',
    '/admin/users' => 'app/pages/admin/users.php',
    '/admin/reports' => 'app/pages/admin/reports.php',
    
    // API routes
    '/api/match-ai' => 'api/match-ai.php',
    '/api/verify' => 'api/verify.php',
    '/api/gifts' => 'api/gifts.php',
    '/api/referrals' => 'api/referrals.php',
];

// Error pages
$errorPages = [
    '404' => 'app/pages/errors/404.php',
    '403' => 'app/pages/errors/403.php',
    '500' => 'app/pages/errors/500.php',
];

// Route groups that require authentication
$authRequired = [
    '/profile',
    '/profile/edit',
    '/chat',
    '/match',
    '/horoscope/match'
];

// Route groups that require admin access
$adminRequired = [
    '/admin/dashboard',
    '/admin/users',
    '/admin/reports'
];

// Export route configuration
return [
    'routes' => $routes,
    'errorPages' => $errorPages,
    'authRequired' => $authRequired,
    'adminRequired' => $adminRequired,
    'api' => [
        '/api/match-ai' => 'api/match-ai.php',
        '/api/verify' => 'api/verify.php',
        '/api/gifts' => 'api/gifts.php',
        '/api/referrals' => 'api/referrals.php',
    ]
]; 