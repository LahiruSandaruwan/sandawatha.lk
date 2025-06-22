<?php
// Get current page for active link highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Navigation items
$navItems = [
    'index' => ['label' => 'Home', 'href' => '/'],
    'register' => ['label' => 'Register', 'href' => '/register.php', 'class' => 'lg:hidden'],
    'login' => ['label' => 'Login', 'href' => '/login.php', 'class' => 'lg:hidden'],
    'about' => ['label' => 'About', 'href' => '/about.php'],
    'contact' => ['label' => 'Contact', 'href' => '/contact.php']
];

// Check if logo exists
$logoPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/logo.png';
$hasLogo = file_exists($logoPath);
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
                    <img src="/assets/images/logo.png" 
                         alt="Sandawatha.lk Logo" 
                         class="h-10 w-auto"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <!-- Fallback Icon (Hidden by default) -->
                    <div class="hidden h-10 w-10 bg-red-100 rounded-full items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                <?php else: ?>
                    <!-- Fallback Icon -->
                    <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col">
                    <span class="text-xl font-semibold">
                        <span class="text-primary group-hover:text-red-700 transition-colors duration-200">Sandawatha</span>
                        <span class="text-secondary">.lk</span>
                    </span>
                    <span class="text-xs text-gray-500 hidden sm:block">Find Your Perfect Match</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex lg:items-center lg:space-x-8">
                <?php foreach ($navItems as $page => $item): ?>
                    <?php if (empty($item['class']) || strpos($item['class'], 'lg:hidden') === false): ?>
                        <a href="<?php echo htmlspecialchars($item['href']); ?>" 
                           class="text-gray-700 hover:text-primary transition-colors duration-200 <?php echo $currentPage === $page ? 'text-primary font-semibold' : ''; ?>">
                            <?php echo htmlspecialchars($item['label']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Desktop Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="/login.php" 
                       class="px-4 py-2 text-gray-700 hover:text-primary transition-colors duration-200">
                        Login
                    </a>
                    <a href="/register.php" 
                       class="px-6 py-2 bg-primary text-white rounded-full hover:bg-red-700 transition-all duration-200 transform hover:scale-105">
                        Join Free
                    </a>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button type="button" 
                    id="mobileMenuBtn"
                    class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-primary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary"
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
                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors duration-200 <?php echo $currentPage === $page ? 'text-primary bg-gray-50' : ''; ?>">
                        <?php echo htmlspecialchars($item['label']); ?>
                    </a>
                <?php endforeach; ?>
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

    // Close mobile menu on window resize if screen becomes large
    $(window).on('resize', function() {
        if (window.innerWidth >= 1024) { // lg breakpoint
            $mobileMenu.addClass('hidden');
            $menuIcon.removeClass('hidden');
            $closeIcon.addClass('hidden');
            $mobileMenuBtn.attr('aria-expanded', 'false');
        }
    });

    // Handle scroll behavior
    $(window).on('scroll', function() {
        const currentScroll = $(this).scrollTop();
        
        // Add shadow and background opacity based on scroll position
        if (currentScroll > 0) {
            $mainNav.addClass('shadow-md');
        } else {
            $mainNav.removeClass('shadow-md');
        }

        // Optional: Hide/show navbar on scroll up/down
        if (currentScroll > lastScroll && currentScroll > 100) {
            // Scrolling down & past navbar
            $mainNav.css('transform', 'translateY(-100%)');
        } else {
            // Scrolling up or at top
            $mainNav.css('transform', 'translateY(0)');
        }
        
        lastScroll = currentScroll;
    });

    // Close mobile menu when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('#mobileMenu, #mobileMenuBtn').length) {
            $mobileMenu.addClass('hidden');
            $menuIcon.removeClass('hidden');
            $closeIcon.addClass('hidden');
            $mobileMenuBtn.attr('aria-expanded', 'false');
        }
    });
});
</script> 