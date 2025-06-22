<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';
$languages = [
    'en' => 'English',
    'si' => 'සිංහල',
    'ta' => 'தமிழ்'
];

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userPhoto = $_SESSION['user_photo'] ?? 'default-avatar.png';
$userName = $_SESSION['user_name'] ?? '';

// Get current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Main Navigation -->
<header class="bg-white shadow-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side - Logo -->
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="flex items-center">
                        <img class="h-8 w-auto" src="/assets/images/logo.png" alt="Sandawatha.lk">
                        <span class="ml-2 text-xl font-semibold text-gray-900">Sandawatha.lk</span>
                    </a>
                </div>
            </div>

            <!-- Middle - Navigation Links (Hidden on mobile) -->
            <div class="hidden sm:flex sm:space-x-8 sm:items-center">
                <a href="/" 
                   class="<?= $currentPage === 'index.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Home
                </a>
                <a href="/search.php" 
                   class="<?= $currentPage === 'search.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Search
                </a>
                <a href="/matches.php" 
                   class="<?= $currentPage === 'matches.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Matches
                </a>
                <a href="/about.php" 
                   class="<?= $currentPage === 'about.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    About
                </a>
            </div>

            <!-- Right side - User menu & Language -->
            <div class="flex items-center space-x-4">
                <!-- Language Switcher -->
                <div class="relative inline-block text-left">
                    <div>
                        <button type="button" 
                                id="language-menu-button"
                                class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                aria-expanded="false" 
                                aria-haspopup="true">
                            <?= $languages[$currentLang] ?>
                            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div id="language-menu" 
                         class="hidden origin-top-right absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                         role="menu" 
                         aria-orientation="vertical" 
                         aria-labelledby="language-menu-button">
                        <div class="py-1" role="none">
                            <?php foreach ($languages as $code => $name): ?>
                                <a href="/change-language.php?lang=<?= $code ?>" 
                                   class="<?= $currentLang === $code ? 'bg-gray-100 text-gray-900' : 'text-gray-700' ?> block px-4 py-2 text-sm hover:bg-gray-100" 
                                   role="menuitem">
                                    <?= $name ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <?php if ($isLoggedIn): ?>
                    <div class="relative inline-block text-left">
                        <div>
                            <button type="button"
                                    id="user-menu-button"
                                    class="flex items-center space-x-2 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    aria-expanded="false"
                                    aria-haspopup="true">
                                <img class="h-8 w-8 rounded-full object-cover" 
                                     src="<?= htmlspecialchars($userPhoto) ?>" 
                                     alt="<?= htmlspecialchars($userName) ?>">
                                <span class="hidden sm:block text-sm font-medium text-gray-700">
                                    <?= htmlspecialchars($userName) ?>
                                </span>
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div id="user-menu"
                             class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                             role="menu"
                             aria-orientation="vertical"
                             aria-labelledby="user-menu-button">
                            <div class="py-1" role="none">
                                <a href="/profile.php" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                   role="menuitem">
                                    My Profile
                                </a>
                                <a href="/matches.php" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                   role="menuitem">
                                    My Matches
                                </a>
                                <a href="/messages.php" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                   role="menuitem">
                                    Messages
                                </a>
                                <a href="/settings.php" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                   role="menuitem">
                                    Settings
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <a href="/logout.php" 
                                   class="block px-4 py-2 text-sm text-red-700 hover:bg-gray-100" 
                                   role="menuitem">
                                    Sign out
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex space-x-4">
                        <a href="/login.php" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50">
                            Sign in
                        </a>
                        <a href="/register.php" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Register
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button"
                            id="mobile-menu-button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                            aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <a href="/" 
                   class="<?= $currentPage === 'index.php' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Home
                </a>
                <a href="/search.php" 
                   class="<?= $currentPage === 'search.php' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Search
                </a>
                <a href="/matches.php" 
                   class="<?= $currentPage === 'matches.php' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Matches
                </a>
                <a href="/about.php" 
                   class="<?= $currentPage === 'about.php' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    About
                </a>
            </div>
        </div>
    </nav>
</header>

<!-- JavaScript for dropdowns and mobile menu -->
<script>
$(document).ready(function() {
    // Language menu toggle
    $('#language-menu-button').click(function() {
        $('#language-menu').toggleClass('hidden');
    });

    // User menu toggle
    $('#user-menu-button').click(function() {
        $('#user-menu').toggleClass('hidden');
    });

    // Mobile menu toggle
    $('#mobile-menu-button').click(function() {
        $('#mobile-menu').toggleClass('hidden');
    });

    // Close menus when clicking outside
    $(document).click(function(event) {
        if (!$(event.target).closest('#language-menu-button, #language-menu').length) {
            $('#language-menu').addClass('hidden');
        }
        if (!$(event.target).closest('#user-menu-button, #user-menu').length) {
            $('#user-menu').addClass('hidden');
        }
        if (!$(event.target).closest('#mobile-menu-button, #mobile-menu').length) {
            $('#mobile-menu').addClass('hidden');
        }
    });

    // Handle mobile menu on resize
    $(window).resize(function() {
        if ($(window).width() > 640) { // sm breakpoint
            $('#mobile-menu').addClass('hidden');
        }
    });
});
</script> 