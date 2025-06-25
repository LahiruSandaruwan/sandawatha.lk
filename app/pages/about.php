<?php
session_start();
require_once '../config/database.php';

// SEO Meta Data
$pageTitle = "About Sandawatha.lk - Sri Lanka's Premier Matrimony Platform";
$pageDescription = "Learn about Sandawatha.lk's mission to help Sri Lankan singles find their perfect match through our trusted matrimony platform.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'romantic': {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337'
                        }
                    },
                    fontFamily: {
                        'display': ['Playfair Display', 'serif'],
                        'body': ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-romantic-50/30">
    <!-- Include Navigation -->
    <?php require_once '../app/views/shared/navbar.php'; ?>

    <main class="pt-24 pb-16">
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:w-full lg:pb-28 xl:pb-32">
                    <div class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="text-center">
                            <h1 class="text-4xl tracking-tight font-display font-bold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block">Finding Love in</span>
                                <span class="block text-romantic-600">Sri Lankan Culture</span>
                            </h1>
                            <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                                Sandawatha.lk is Sri Lanka's premier matrimony platform, bringing together tradition, technology, and trust to help you find your perfect life partner.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission Section -->
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-3xl font-display font-bold text-gray-900 sm:text-4xl">
                        Our Mission
                    </h2>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                        To create meaningful connections that celebrate Sri Lankan values while embracing modern matchmaking technology.
                    </p>
                </div>

                <div class="mt-16">
                    <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                        <!-- Trust -->
                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-romantic-500 text-white">
                                <i class="fas fa-shield-alt text-xl"></i>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Trust & Safety</p>
                            <p class="mt-2 ml-16 text-base text-gray-500">
                                Verified profiles and strict privacy measures to ensure a safe matchmaking environment.
                            </p>
                        </div>

                        <!-- Culture -->
                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-romantic-500 text-white">
                                <i class="fas fa-heart text-xl"></i>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Cultural Values</p>
                            <p class="mt-2 ml-16 text-base text-gray-500">
                                Respecting Sri Lankan traditions while embracing modern relationships.
                            </p>
                        </div>

                        <!-- Technology -->
                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-romantic-500 text-white">
                                <i class="fas fa-mobile-alt text-xl"></i>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Modern Technology</p>
                            <p class="mt-2 ml-16 text-base text-gray-500">
                                Advanced matching algorithms and user-friendly features for the best experience.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How We Help Section -->
        <section class="py-12 bg-romantic-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-display font-bold text-center text-gray-900 sm:text-4xl mb-12">
                    How We Help Sri Lankan Singles
                </h2>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Left Column -->
                    <div class="space-y-8">
                        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                            <h3 class="text-xl font-semibold text-romantic-600 mb-3">
                                <i class="fas fa-star mr-2"></i>
                                Horoscope Matching
                            </h3>
                            <p class="text-gray-600">
                                Traditional Sri Lankan horoscope matching integrated with modern compatibility analysis.
                            </p>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                            <h3 class="text-xl font-semibold text-romantic-600 mb-3">
                                <i class="fas fa-users mr-2"></i>
                                Community Focus
                            </h3>
                            <p class="text-gray-600">
                                Connect with matches from your community, religion, or caste preferences.
                            </p>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                            <h3 class="text-xl font-semibold text-romantic-600 mb-3">
                                <i class="fas fa-lock mr-2"></i>
                                Privacy Control
                            </h3>
                            <p class="text-gray-600">
                                Advanced privacy settings to control who can view your profile and contact you.
                            </p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-8">
                        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                            <h3 class="text-xl font-semibold text-romantic-600 mb-3">
                                <i class="fas fa-brain mr-2"></i>
                                AI Matching
                            </h3>
                            <p class="text-gray-600">
                                Smart algorithms that learn your preferences to suggest better matches over time.
                            </p>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                            <h3 class="text-xl font-semibold text-romantic-600 mb-3">
                                <i class="fas fa-check-circle mr-2"></i>
                                Verified Profiles
                            </h3>
                            <p class="text-gray-600">
                                Manual verification process to ensure authentic and genuine profiles.
                            </p>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                            <h3 class="text-xl font-semibold text-romantic-600 mb-3">
                                <i class="fas fa-headset mr-2"></i>
                                24/7 Support
                            </h3>
                            <p class="text-gray-600">
                                Dedicated customer support team to assist you throughout your journey.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="bg-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-display font-bold text-gray-900 sm:text-4xl mb-8">
                    Ready to Find Your Perfect Match?
                </h2>
                <div class="inline-flex rounded-md shadow">
                    <a href="/register.php" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-romantic-600 hover:bg-romantic-700 transition-colors">
                        Get Started
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Include Footer -->
    <?php require_once '../app/views/shared/footer.php'; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html> 