<?php
// Social media links (passed from parent)
$socialLinks = isset($socialLinks) ? $socialLinks : [
    'facebook' => 'https://facebook.com/sandawatha',
    'instagram' => 'https://instagram.com/sandawatha',
    'twitter' => 'https://twitter.com/sandawatha',
    'youtube' => 'https://youtube.com/sandawatha'
];

// Footer links
$footerLinks = [
    'Company' => [
        'About Us' => '/about',
        'Contact' => '/contact',
        'Blog' => '/blog',
        'Careers' => '/careers'
    ],
    'Legal' => [
        'Privacy Policy' => '/privacy',
        'Terms of Service' => '/terms',
        'Cookie Policy' => '/cookies',
        'Disclaimer' => '/disclaimer'
    ],
    'Support' => [
        'Help Center' => '/help',
        'Safety Tips' => '/safety',
        'Report Issues' => '/report',
        'FAQs' => '/faqs'
    ],
    'Features' => [
        'How It Works' => '/how-it-works',
        'Success Stories' => '/success-stories',
        'Premium Features' => '/premium',
        'Gift Store' => '/gifts'
    ]
];

$currentYear = date('Y');
?>

<footer class="bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Logo Section -->
        <div class="flex flex-col items-center mb-8">
            <a href="/index.php" class="mb-4">
                <!-- Inline SVG Logo -->
                <svg class="h-10 w-auto" viewBox="0 0 240 80" fill="none" xmlns="http://www.w3.org/2000/svg">
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
            <p class="text-gray-500 text-sm text-center">
                Sri Lanka's Premier Matrimony Platform
            </p>
        </div>

        <!-- Footer Links -->
        <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">
                    Company
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                    <li>
                        <a href="/about.php" class="text-base text-gray-500 hover:text-romantic-600">
                            About Us
                        </a>
                    </li>
                    <li>
                        <a href="/blog" class="text-base text-gray-500 hover:text-romantic-600">
                            Blog
                        </a>
                    </li>
                    <li>
                        <a href="/contact.php" class="text-base text-gray-500 hover:text-romantic-600">
                            Contact
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">
                    Features
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                    <li>
                        <a href="/horoscope/match" class="text-base text-gray-500 hover:text-romantic-600">
                            Horoscope Match
                        </a>
                    </li>
                    <li>
                        <a href="/search" class="text-base text-gray-500 hover:text-romantic-600">
                            Advanced Search
                        </a>
                    </li>
                    <li>
                        <a href="/premium" class="text-base text-gray-500 hover:text-romantic-600">
                            Premium Features
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">
                    Support
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                    <li>
                        <a href="/help" class="text-base text-gray-500 hover:text-romantic-600">
                            Help Center
                        </a>
                    </li>
                    <li>
                        <a href="/safety" class="text-base text-gray-500 hover:text-romantic-600">
                            Safety Tips
                        </a>
                    </li>
                    <li>
                        <a href="/privacy" class="text-base text-gray-500 hover:text-romantic-600">
                            Privacy Policy
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">
                    Legal
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                    <li>
                        <a href="/terms" class="text-base text-gray-500 hover:text-romantic-600">
                            Terms of Service
                        </a>
                    </li>
                    <li>
                        <a href="/cookies" class="text-base text-gray-500 hover:text-romantic-600">
                            Cookie Policy
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-span-2 md:col-span-4 lg:col-span-1">
                <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">
                    Connect With Us
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                    <li>
                        <a href="tel:+94111234567" class="text-base text-gray-500 hover:text-romantic-600 flex items-center">
                            <i class="fas fa-phone-alt w-5"></i>
                            <span class="ml-2">+94 11 123 4567</span>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:support@sandawatha.lk" class="text-base text-gray-500 hover:text-romantic-600 flex items-center">
                            <i class="fas fa-envelope w-5"></i>
                            <span class="ml-2">support@sandawatha.lk</span>
                        </a>
                    </li>
                    <li class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-romantic-600">
                            <span class="sr-only">Facebook</span>
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-romantic-600">
                            <span class="sr-only">Instagram</span>
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-romantic-600">
                            <span class="sr-only">Twitter</span>
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-12 border-t border-gray-200 pt-8">
            <p class="text-base text-gray-400 text-center">
                &copy; <?php echo $currentYear; ?> Sandawatha.lk. All rights reserved.
            </p>
        </div>
    </div>
</footer> 