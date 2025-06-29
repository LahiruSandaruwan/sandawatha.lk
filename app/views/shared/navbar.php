<?php
// Get current page for active link highlighting
$currentPage = getCurrentPage();

// Navigation items
$navItems = [
    'home' => ['label' => 'Home', 'href' => '/'],
    'register' => ['label' => 'Register', 'href' => '/register', 'class' => 'lg:hidden'],
    'login' => ['label' => 'Login', 'href' => '/login', 'class' => 'lg:hidden'],
    'about' => ['label' => 'About', 'href' => '/about'],
    'contact' => ['label' => 'Contact', 'href' => '/contact']
];

// Check if logo exists
$logoPath = PUBLIC_PATH . '/assets/images/logo.svg';
$hasLogo = file_exists($logoPath);

$isAuthenticated = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$isAdmin = isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
?>

<!-- Navigation -->
<nav class="fixed w-full top-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm transition-all duration-300" 
     id="mainNav" 
     aria-label="Main navigation">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2 group" aria-label="Sandawatha.lk - Home">
                <?php if ($hasLogo): ?>
                    <img src="<?php echo asset('images/logo.svg'); ?>" 
                         alt="Sandawatha.lk Logo" 
                         class="h-10 w-auto"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <!-- Fallback Icon (Hidden by default) -->
                    <div class="hidden h-10 w-10 bg-romantic-100 rounded-full items-center justify-center">
                        <svg class="w-6 h-6 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                <?php else: ?>
                    <!-- Fallback Icon -->
                    <div class="h-10 w-10 bg-romantic-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col">
                    <span class="text-xl font-semibold">
                        <span class="text-romantic-600 group-hover:text-romantic-700 transition-colors duration-200">Sandawatha</span>
                        <span class="text-gray-700">.lk</span>
                    </span>
                    <span class="text-xs text-gray-500 hidden sm:block">Find Your Perfect Match</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex lg:items-center lg:space-x-8">
                <?php foreach ($navItems as $page => $item): ?>
                    <?php if (empty($item['class']) || strpos($item['class'], 'lg:hidden') === false): ?>
                        <a href="<?php echo htmlspecialchars($item['href']); ?>" 
                           class="text-gray-700 hover:text-romantic-600 transition-colors duration-200 <?php echo $currentPage === $page ? 'text-romantic-600 font-semibold' : ''; ?>">
                            <?php echo htmlspecialchars($item['label']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Desktop Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <?php if ($isAuthenticated): ?>
                        <a href="/match" 
                           class="px-4 py-2 text-gray-700 hover:text-romantic-600 transition-colors duration-200">
                            Find Matches
                        </a>
                        <a href="/chat" 
                           class="px-4 py-2 text-gray-700 hover:text-romantic-600 transition-colors duration-200">
                            Messages
                            <?php
                            // Check for unread messages
                            if (isset($_SESSION['unread_messages']) && $_SESSION['unread_messages'] > 0): ?>
                                <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-romantic-100 text-romantic-800">
                                    <?php echo $_SESSION['unread_messages']; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="ml-3 relative">
                            <div>
                                <button type="button" 
                                        class="profile-menu-button bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500" 
                                        id="user-menu-button" 
                                        aria-expanded="false" 
                                        aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="h-8 w-8 rounded-full object-cover" 
                                         src="<?php echo isset($_SESSION['user_photo']) ? storage($_SESSION['user_photo']) : asset('images/placeholder.svg'); ?>" 
                                         alt="Profile photo">
                                </button>
                            </div>
                            <div class="profile-menu origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 hidden" 
                                 role="menu" 
                                 aria-orientation="vertical" 
                                 aria-labelledby="user-menu-button" 
                                 tabindex="-1">
                                <a href="/profile" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                   role="menuitem">Your Profile</a>
                                
                                <?php if ($isAdmin): ?>
                                    <a href="/admin/dashboard" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                       role="menuitem">Admin Dashboard</a>
                                <?php endif; ?>
                                
                                <a href="/settings" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                   role="menuitem">Settings</a>
                                
                                <form action="/logout" method="POST" class="block">
                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                    <button type="submit" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                            role="menuitem">Sign out</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login" 
                           class="px-4 py-2 text-gray-700 hover:text-romantic-600 transition-colors duration-200">
                            Login
                        </a>
                        <a href="/register" 
                           class="px-6 py-2 bg-romantic-600 text-white rounded-full hover:bg-romantic-700 transition-all duration-200 transform hover:scale-105">
                            Join Free
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button type="button" 
                    id="mobileMenuBtn"
                    class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-romantic-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-romantic-600"
                    aria-controls="mobileMenu"
                    aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <!-- Menu Icon -->
                <svg class="block h-6 w-6" 
                     id="menuIcon"
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor" 
                     aria-hidden="true">
                    <path stroke-linecap="round" 
                          stroke-linejoin="round" 
                          stroke-width="2" 
                          d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <!-- Close Icon -->
                <svg class="hidden h-6 w-6" 
                     id="closeIcon"
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor" 
                     aria-hidden="true">
                    <path stroke-linecap="round" 
                          stroke-linejoin="round" 
                          stroke-width="2" 
                          d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="lg:hidden hidden transition-all duration-300 ease-in-out" id="mobileMenu">
            <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-200">
                <?php foreach ($navItems as $page => $item): ?>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" 
                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-romantic-600 hover:bg-gray-50 transition-colors duration-200 <?php echo $currentPage === $page ? 'text-romantic-600 bg-gray-50' : ''; ?>">
                        <?php echo htmlspecialchars($item['label']); ?>
                    </a>
                <?php endforeach; ?>

                <?php if ($isAuthenticated): ?>
                    <a href="/match" 
                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-romantic-600 hover:bg-gray-50 transition-colors duration-200">
                        Find Matches
                    </a>
                    <a href="/chat" 
                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-romantic-600 hover:bg-gray-50 transition-colors duration-200">
                        Messages
                        <?php if (isset($_SESSION['unread_messages']) && $_SESSION['unread_messages'] > 0): ?>
                            <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-romantic-100 text-romantic-800">
                                <?php echo $_SESSION['unread_messages']; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="/profile" 
                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-romantic-600 hover:bg-gray-50 transition-colors duration-200">
                        Your Profile
                    </a>
                    <?php if ($isAdmin): ?>
                        <a href="/admin/dashboard" 
                           class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-romantic-600 hover:bg-gray-50 transition-colors duration-200">
                            Admin Dashboard
                        </a>
                    <?php endif; ?>
                    <a href="/settings" 
                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-romantic-600 hover:bg-gray-50 transition-colors duration-200">
                        Settings
                    </a>
                    <form action="/logout" method="POST" class="block">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <button type="submit" 
                                class="w-full text-left text-gray-700 hover:text-romantic-600 px-3 py-2 rounded-md text-base font-medium">
                            Sign out
                        </button>
                    </form>
                <?php else: ?>
                    <a href="/login" 
                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-romantic-600 hover:bg-gray-50 transition-colors duration-200">
                        Login
                    </a>
                    <a href="/register" 
                       class="bg-romantic-600 text-white px-3 py-2 rounded-md text-base font-medium">
                        Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Navigation Scripts -->
<script>
$(document).ready(function() {
    const $mobileMenuBtn = $('#mobileMenuBtn');
    const $mobileMenu = $('#mobileMenu');
    const $menuIcon = $('#menuIcon');
    const $closeIcon = $('#closeIcon');
    const $mainNav = $('#mainNav');
    let lastScroll = 0;

    // Mobile menu toggle
    $mobileMenuBtn.on('click', function() {
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        $(this).attr('aria-expanded', !isExpanded);
        $mobileMenu.toggleClass('hidden');
        $menuIcon.toggleClass('hidden');
        $closeIcon.toggleClass('hidden');
    });

    // Profile menu toggle
    $('.profile-menu-button').on('click', function() {
        $('.profile-menu').toggleClass('hidden');
    });

    // Hide profile menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.profile-menu-button').length && !$(e.target).closest('.profile-menu').length) {
            $('.profile-menu').addClass('hidden');
        }
    });

    // Scroll behavior for navbar
    $(window).scroll(function() {
        const currentScroll = $(this).scrollTop();
        
        if (currentScroll > 100) {
            $mainNav.addClass('shadow-md').removeClass('shadow-sm');
        } else {
            $mainNav.removeClass('shadow-md').addClass('shadow-sm');
        }
        
        if (currentScroll > lastScroll && currentScroll > 200) {
            // Scrolling down
            $mainNav.addClass('translate-y-[-100%]');
        } else {
            // Scrolling up
            $mainNav.removeClass('translate-y-[-100%]');
        }
        
        lastScroll = currentScroll;
    });
});
</script> 