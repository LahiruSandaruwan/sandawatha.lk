<?php
// Get current page for active link highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$isLoggedIn = isset($_SESSION['user_id']);

// Language handling (passed from parent)
$languages = isset($languages) ? $languages : ['en' => 'English', 'si' => 'සිංහල', 'ta' => 'தமிழ்'];
$currentLang = isset($currentLang) ? $currentLang : 'en';

// Navigation items with active state handling
$navItems = [
    'match' => [
        'label' => 'Find Matches',
        'href' => '/match',
        'icon' => 'fa-heart'
    ],
    'horoscope' => [
        'label' => 'Horoscope Match',
        'href' => '/horoscope/match',
        'icon' => 'fa-star'
    ],
    'blog' => [
        'label' => 'Blog',
        'href' => '/blog',
        'icon' => 'fa-pen'
    ],
    'about' => [
        'label' => 'About',
        'href' => '/about.php',
        'icon' => 'fa-info-circle'
    ],
    'contact' => [
        'label' => 'Contact',
        'href' => '/contact.php',
        'icon' => 'fa-envelope'
    ]
];

// Check if logo exists
$logoPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/logo.png';
$hasLogo = file_exists($logoPath);

// Helper function to determine if a nav item is active
function isActive($href) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    return $href === $currentUrl || ($href !== '/' && strpos($currentUrl, $href) === 0);
}
?>

<!-- Navigation -->
<nav class="fixed w-full z-50 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="/index.php" class="flex items-center">
                    <!-- Inline SVG Logo with increased size -->
                    <svg class="h-12 w-auto sm:h-14" viewBox="0 0 240 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Heart Icon -->
                        <g transform="translate(10,25) scale(0.8)">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" 
                                  fill="#E11D48" stroke="#E11D48" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <!-- Text "Sandawatha.lk" -->
                        <text x="50" y="50" font-family="'Playfair Display', serif" font-size="32" font-weight="600" fill="#1F2937">
                            <tspan fill="#E11D48">Sanda</tspan><tspan>watha.lk</tspan>
                        </text>
                    </svg>
                </a>
            </div>

            <!-- Navigation Links (Desktop) -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-8">
                <a href="/" class="<?php echo $currentPage === 'index' ? 'text-romantic-600 border-romantic-500' : 'text-gray-500 hover:text-gray-900 border-transparent'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Home
                </a>
                <a href="/about.php" class="<?php echo $currentPage === 'about' ? 'text-romantic-600 border-romantic-500' : 'text-gray-500 hover:text-gray-900 border-transparent'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    About
                </a>
                <a href="/horoscope/match" class="<?php echo $currentPage === 'match' ? 'text-romantic-600 border-romantic-500' : 'text-gray-500 hover:text-gray-900 border-transparent'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Horoscope Match
                </a>
                <a href="/blog" class="<?php echo $currentPage === 'blog' ? 'text-romantic-600 border-romantic-500' : 'text-gray-500 hover:text-gray-900 border-transparent'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Blog
                </a>
                <a href="/contact.php" class="<?php echo $currentPage === 'contact' ? 'text-romantic-600 border-romantic-500' : 'text-gray-500 hover:text-gray-900 border-transparent'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Contact
                </a>
            </div>

            <!-- Auth Buttons (Desktop) -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                <?php if ($isLoggedIn): ?>
                    <a href="/profile" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-romantic-600 bg-romantic-50 hover:bg-romantic-100">
                        My Profile
                    </a>
                    <a href="/logout.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-romantic-600 hover:bg-romantic-700">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="/login.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-romantic-600 bg-romantic-50 hover:bg-romantic-100">
                        Login
                    </a>
                    <a href="/register.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-romantic-600 hover:bg-romantic-700">
                        Register
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="mobile-menu sm:hidden hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="/" class="<?php echo $currentPage === 'index' ? 'bg-romantic-50 border-romantic-500 text-romantic-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Home
            </a>
            <a href="/about.php" class="<?php echo $currentPage === 'about' ? 'bg-romantic-50 border-romantic-500 text-romantic-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                About
            </a>
            <a href="/horoscope/match" class="<?php echo $currentPage === 'match' ? 'bg-romantic-50 border-romantic-500 text-romantic-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Horoscope Match
            </a>
            <a href="/blog" class="<?php echo $currentPage === 'blog' ? 'bg-romantic-50 border-romantic-500 text-romantic-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Blog
            </a>
            <a href="/contact.php" class="<?php echo $currentPage === 'contact' ? 'bg-romantic-50 border-romantic-500 text-romantic-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Contact
            </a>
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200">
            <?php if ($isLoggedIn): ?>
                <div class="space-y-1">
                    <a href="/profile" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">
                        My Profile
                    </a>
                    <a href="/logout.php" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">
                        Logout
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-1">
                    <a href="/login.php" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">
                        Login
                    </a>
                    <a href="/register.php" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">
                        Register
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Mobile Menu Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('.mobile-menu');

        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    });
</script> 