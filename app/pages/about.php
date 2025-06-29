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
                        },
                        'primary': '#e11d48',
                        'secondary': '#4f46e5'
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
    
    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
</head>
<body class="bg-romantic-50/30 font-body">
    <!-- Include Navigation -->
    <?php require_once '../app/views/shared/navbar.php'; ?>

    <main class="pt-24 pb-16">
        <!-- Hero Section with Background Pattern -->
        <div class="relative overflow-hidden bg-gradient-to-r from-romantic-50 to-white">
            <div class="absolute inset-0 opacity-10">
                <img src="/assets/images/patterns/hearts.svg" alt="" class="w-full h-full object-cover">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="py-16 md:py-24 lg:py-32">
                    <div class="text-center">
                        <h1 class="text-4xl tracking-tight font-display font-bold text-gray-900 sm:text-5xl md:text-6xl" data-aos="fade-up">
                            <span class="block">Our Story of Connecting</span>
                            <span class="block text-romantic-600">Sri Lankan Hearts</span>
                        </h1>
                        <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-500" data-aos="fade-up" data-aos-delay="100">
                            Sandawatha.lk brings together tradition, technology, and trust to help you find your perfect life partner in the beautiful context of Sri Lankan culture.
                        </p>
                        <div class="mt-10 flex justify-center space-x-6" data-aos="fade-up" data-aos-delay="200">
                            <a href="/register.php" class="px-8 py-3 rounded-full text-white bg-romantic-600 hover:bg-romantic-700 transition-all duration-300 transform hover:scale-105 shadow-md">
                                Start Your Journey
                            </a>
                            <a href="#our-mission" class="px-8 py-3 rounded-full text-romantic-600 bg-white border border-romantic-200 hover:bg-romantic-50 transition-all duration-300 shadow-sm">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Our Mission Section -->
        <section id="our-mission" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center mb-12">
                    <h2 class="text-base text-romantic-600 font-semibold tracking-wide uppercase" data-aos="fade-up">Our Mission</h2>
                    <p class="mt-2 text-3xl leading-8 font-display font-bold text-gray-900 sm:text-4xl" data-aos="fade-up" data-aos-delay="100">
                        Bringing Together Tradition and Technology
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto" data-aos="fade-up" data-aos-delay="200">
                        To create meaningful connections that celebrate Sri Lankan values while embracing modern matchmaking technology.
                    </p>
                </div>

                <div class="mt-16">
                    <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                        <!-- Trust -->
                        <div class="relative" data-aos="fade-up" data-aos-delay="100">
                            <div class="absolute flex items-center justify-center h-16 w-16 rounded-xl bg-romantic-500 text-white shadow-lg">
                                <i class="fas fa-shield-alt text-2xl"></i>
                            </div>
                            <div class="ml-24">
                                <h3 class="text-xl leading-6 font-bold text-gray-900">Trust & Safety</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    We prioritize your safety with verified profiles, strict privacy measures, and a secure environment for finding your perfect match.
                                </p>
                            </div>
                        </div>

                        <!-- Culture -->
                        <div class="relative" data-aos="fade-up" data-aos-delay="200">
                            <div class="absolute flex items-center justify-center h-16 w-16 rounded-xl bg-romantic-500 text-white shadow-lg">
                                <i class="fas fa-heart text-2xl"></i>
                            </div>
                            <div class="ml-24">
                                <h3 class="text-xl leading-6 font-bold text-gray-900">Cultural Values</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    We honor Sri Lankan traditions while embracing modern relationships, creating a platform where cultural values are respected.
                                </p>
                            </div>
                        </div>

                        <!-- Technology -->
                        <div class="relative" data-aos="fade-up" data-aos-delay="300">
                            <div class="absolute flex items-center justify-center h-16 w-16 rounded-xl bg-romantic-500 text-white shadow-lg">
                                <i class="fas fa-mobile-alt text-2xl"></i>
                            </div>
                            <div class="ml-24">
                                <h3 class="text-xl leading-6 font-bold text-gray-900">Modern Technology</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Our platform leverages advanced matching algorithms and user-friendly features to provide the best experience for finding your life partner.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- What Makes Us Different Section -->
        <section class="py-16 bg-romantic-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center mb-12">
                    <h2 class="text-base text-romantic-600 font-semibold tracking-wide uppercase" data-aos="fade-up">What Makes Us Different</h2>
                    <p class="mt-2 text-3xl leading-8 font-display font-bold text-gray-900 sm:text-4xl" data-aos="fade-up" data-aos-delay="100">
                        A Unique Approach to Finding Love
                    </p>
                </div>

                <div class="mt-12">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300" data-aos="fade-up" data-aos-delay="100">
                            <div class="text-romantic-500 mb-4">
                                <i class="fas fa-star text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Horoscope Matching</h3>
                            <p class="text-gray-600">
                                We integrate traditional Sri Lankan horoscope matching with modern compatibility analysis to ensure astrologically compatible matches.
                            </p>
                        </div>

                        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300" data-aos="fade-up" data-aos-delay="200">
                            <div class="text-romantic-500 mb-4">
                                <i class="fas fa-users text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Community Focus</h3>
                            <p class="text-gray-600">
                                Our platform allows you to connect with matches from your specific community, religion, or caste preferences while respecting diversity.
                            </p>
                        </div>

                        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300" data-aos="fade-up" data-aos-delay="300">
                            <div class="text-romantic-500 mb-4">
                                <i class="fas fa-brain text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">AI Matching</h3>
                            <p class="text-gray-600">
                                Our intelligent algorithms learn your preferences over time to suggest increasingly better matches tailored specifically to you.
                            </p>
                        </div>

                        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300" data-aos="fade-up" data-aos-delay="400">
                            <div class="text-romantic-500 mb-4">
                                <i class="fas fa-lock text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Privacy Control</h3>
                            <p class="text-gray-600">
                                Take control of your online presence with advanced privacy settings that let you decide who can view your profile and contact you.
                            </p>
                        </div>

                        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300" data-aos="fade-up" data-aos-delay="500">
                            <div class="text-romantic-500 mb-4">
                                <i class="fas fa-check-circle text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Verified Profiles</h3>
                            <p class="text-gray-600">
                                We implement a thorough manual verification process to ensure all profiles on our platform are authentic and genuine.
                            </p>
                        </div>

                        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300" data-aos="fade-up" data-aos-delay="600">
                            <div class="text-romantic-500 mb-4">
                                <i class="fas fa-headset text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">24/7 Support</h3>
                            <p class="text-gray-600">
                                Our dedicated customer support team is available around the clock to assist you throughout your journey to finding love.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Meet the Team Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center mb-12">
                    <h2 class="text-base text-romantic-600 font-semibold tracking-wide uppercase" data-aos="fade-up">Meet the Team</h2>
                    <p class="mt-2 text-3xl leading-8 font-display font-bold text-gray-900 sm:text-4xl" data-aos="fade-up" data-aos-delay="100">
                        The People Behind Sandawatha
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto" data-aos="fade-up" data-aos-delay="200">
                        Our diverse team of experts is dedicated to helping you find your perfect match.
                    </p>
                </div>

                <div class="mt-12 grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Team Member 1 -->
                    <div class="bg-romantic-50 rounded-xl overflow-hidden shadow-md" data-aos="fade-up" data-aos-delay="100">
                        <div class="aspect-w-1 aspect-h-1">
                            <img src="/assets/images/placeholder.svg" alt="Team Member" class="w-full h-64 object-cover">
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-900">Ashan Perera</h3>
                            <p class="text-romantic-600 font-medium">Founder & CEO</p>
                            <p class="mt-2 text-gray-600 text-sm">
                                Passionate about bringing Sri Lankan singles together through technology and tradition.
                            </p>
                        </div>
                    </div>

                    <!-- Team Member 2 -->
                    <div class="bg-romantic-50 rounded-xl overflow-hidden shadow-md" data-aos="fade-up" data-aos-delay="200">
                        <div class="aspect-w-1 aspect-h-1">
                            <img src="/assets/images/placeholder.svg" alt="Team Member" class="w-full h-64 object-cover">
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-900">Priya Mendis</h3>
                            <p class="text-romantic-600 font-medium">Head of Matchmaking</p>
                            <p class="mt-2 text-gray-600 text-sm">
                                Expert in relationship psychology and traditional Sri Lankan matchmaking practices.
                            </p>
                        </div>
                    </div>

                    <!-- Team Member 3 -->
                    <div class="bg-romantic-50 rounded-xl overflow-hidden shadow-md" data-aos="fade-up" data-aos-delay="300">
                        <div class="aspect-w-1 aspect-h-1">
                            <img src="/assets/images/placeholder.svg" alt="Team Member" class="w-full h-64 object-cover">
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-900">Dinesh Kumar</h3>
                            <p class="text-romantic-600 font-medium">Technology Director</p>
                            <p class="mt-2 text-gray-600 text-sm">
                                Leading our AI matching algorithms and platform development with over 15 years of experience.
                            </p>
                        </div>
                    </div>

                    <!-- Team Member 4 -->
                    <div class="bg-romantic-50 rounded-xl overflow-hidden shadow-md" data-aos="fade-up" data-aos-delay="400">
                        <div class="aspect-w-1 aspect-h-1">
                            <img src="/assets/images/placeholder.svg" alt="Team Member" class="w-full h-64 object-cover">
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-900">Kumari Silva</h3>
                            <p class="text-romantic-600 font-medium">Customer Success</p>
                            <p class="mt-2 text-gray-600 text-sm">
                                Dedicated to ensuring every member has a positive experience finding their perfect match.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-16 bg-gradient-to-b from-romantic-50 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center mb-12">
                    <h2 class="text-base text-romantic-600 font-semibold tracking-wide uppercase" data-aos="fade-up">Success Stories</h2>
                    <p class="mt-2 text-3xl leading-8 font-display font-bold text-gray-900 sm:text-4xl" data-aos="fade-up" data-aos-delay="100">
                        Happy Couples Who Found Love
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto" data-aos="fade-up" data-aos-delay="200">
                        Read about the real-life success stories from couples who met through Sandawatha.lk
                    </p>
                </div>

                <div class="mt-12 grid gap-8 md:grid-cols-3">
                    <!-- Testimonial 1 -->
                    <div class="bg-white p-6 rounded-xl shadow-md" data-aos="fade-up" data-aos-delay="100">
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-romantic-100 flex items-center justify-center">
                                <i class="fas fa-quote-left text-romantic-500"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-900">Priya & Ashan</h3>
                                <p class="text-romantic-600">Colombo</p>
                            </div>
                        </div>
                        <p class="text-gray-600 italic">
                            "We found each other on Sandawatha.lk and our horoscopes matched perfectly! The platform made it easy to connect, and now we're happily married for 2 years."
                        </p>
                        <div class="mt-4 flex text-romantic-500">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-white p-6 rounded-xl shadow-md" data-aos="fade-up" data-aos-delay="200">
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-romantic-100 flex items-center justify-center">
                                <i class="fas fa-quote-left text-romantic-500"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-900">Malini & Dinesh</h3>
                                <p class="text-romantic-600">Kandy</p>
                            </div>
                        </div>
                        <p class="text-gray-600 italic">
                            "The AI matching system introduced us, and it was like magic from our first meeting. We're getting married next month! Thank you Sandawatha for bringing us together."
                        </p>
                        <div class="mt-4 flex text-romantic-500">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-white p-6 rounded-xl shadow-md" data-aos="fade-up" data-aos-delay="300">
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-romantic-100 flex items-center justify-center">
                                <i class="fas fa-quote-left text-romantic-500"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-900">Kumari & Rajitha</h3>
                                <p class="text-romantic-600">Galle</p>
                            </div>
                        </div>
                        <p class="text-gray-600 italic">
                            "Thank you Sandawatha for helping us find true love. The verification process gave us peace of mind, and we felt safe throughout our journey to finding each other."
                        </p>
                        <div class="mt-4 flex text-romantic-500">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trust & Community Impact -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-romantic-600 rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-12 md:p-12 lg:px-16 lg:py-16">
                        <div class="md:flex md:items-center md:justify-between">
                            <div class="md:flex-1" data-aos="fade-right">
                                <h2 class="text-2xl font-display font-bold text-white sm:text-3xl">
                                    Trusted by Thousands of Sri Lankan Families
                                </h2>
                                <p class="mt-3 text-lg text-romantic-100">
                                    Sandawatha.lk has helped create countless happy marriages across Sri Lanka. Our commitment to authenticity, safety, and cultural values has made us the most trusted matrimonial platform in the country.
                                </p>
                                <div class="mt-8 flex space-x-6">
                                    <div class="flex items-center">
                                        <div class="text-4xl font-bold text-white">10,000+</div>
                                        <div class="ml-3 text-romantic-100">Active Members</div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="text-4xl font-bold text-white">5,000+</div>
                                        <div class="ml-3 text-romantic-100">Successful Matches</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-8 md:mt-0 md:ml-8 md:flex-shrink-0" data-aos="fade-left">
                                <a href="/register.php" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-romantic-600 bg-white hover:bg-romantic-50 md:py-4 md:text-lg md:px-10">
                                    Join Our Community
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-16 bg-romantic-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-display font-bold text-gray-900 sm:text-4xl mb-6" data-aos="fade-up">
                    Ready to Find Your Perfect Match?
                </h2>
                <p class="max-w-2xl mx-auto text-xl text-gray-500 mb-10" data-aos="fade-up" data-aos-delay="100">
                    Join thousands of Sri Lankan singles who have found love on Sandawatha.lk
                </p>
                <div class="inline-flex rounded-md shadow" data-aos="fade-up" data-aos-delay="200">
                    <a href="/register.php" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-lg font-medium rounded-full text-white bg-romantic-600 hover:bg-romantic-700 transition-all duration-300 transform hover:scale-105 shadow-md">
                        Start Your Journey
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
    
    <!-- Initialize AOS -->
    <script>
        $(document).ready(function() {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 100
            });
        });
    </script>
</body>
</html> 