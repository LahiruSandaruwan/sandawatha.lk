<?php
session_start();
// Get database connection
$pdo = require_once '../config/database.php';

// Language handling
$languages = ['en' => 'English', 'si' => 'සිංහල', 'ta' => 'தமிழ்'];
$currentLang = isset($_GET['lang']) && array_key_exists($_GET['lang'], $languages) ? $_GET['lang'] : 'en';

// Fetch featured profiles
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            CONCAT(u.first_name, ' ', LEFT(u.last_name, 1), '.') as name,
            TIMESTAMPDIFF(YEAR, u.date_of_birth, CURDATE()) as age,
            d.name as district,
            u.profile_photo,
            r.name as religion,
            u.bio
        FROM users u
        LEFT JOIN districts d ON u.district_id = d.id
        LEFT JOIN religions r ON u.religion_id = r.id
        WHERE u.status = 'active'
        AND u.profile_photo IS NOT NULL
        ORDER BY RAND()
        LIMIT 6
    ");
    $stmt->execute();
    $featuredProfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featuredProfiles = [];
}

// Get total user counts
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'");
    $totalUsers = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active' AND email_verified_at IS NOT NULL");
    $verifiedUsers = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM messages");
    $totalConnections = $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalUsers = 0;
    $verifiedUsers = 0;
    $totalConnections = 0;
}

// Update testimonials with reliable image URLs and better placeholders
$testimonials = [
    [
        'name' => 'Priya & Ashan',
        'photo' => 'https://images.unsplash.com/photo-1621784563330-caee0b138a00?auto=format&fit=crop&w=400&q=80',
        'placeholder' => 'https://images.unsplash.com/photo-1621784563330-caee0b138a00?auto=format&fit=crop&w=50&q=20&blur=10',
        'message' => 'We found each other on Sandawatha.lk and our horoscopes matched perfectly! Now happily married for 2 years.',
        'rating' => 5,
        'location' => 'Colombo'
    ],
    [
        'name' => 'Malini & Dinesh',
        'photo' => 'https://images.unsplash.com/photo-1623069923731-45e5d19f6f0f?auto=format&fit=crop&w=400&q=80',
        'placeholder' => 'https://images.unsplash.com/photo-1623069923731-45e5d19f6f0f?auto=format&fit=crop&w=50&q=20&blur=10',
        'message' => 'The AI matching system introduced us, and it was like magic from our first meeting. Getting married next month!',
        'rating' => 5,
        'location' => 'Kandy'
    ],
    [
        'name' => 'Kumari & Rajitha',
        'photo' => 'https://images.unsplash.com/photo-1621784562877-01a79135c0c5?auto=format&fit=crop&w=400&q=80',
        'placeholder' => 'https://images.unsplash.com/photo-1621784562877-01a79135c0c5?auto=format&fit=crop&w=50&q=20&blur=10',
        'message' => 'Thank you Sandawatha for helping us find true love. The verification process gave us peace of mind.',
        'rating' => 5,
        'location' => 'Galle'
    ]
];

// Social media links
$socialLinks = [
    'facebook' => 'https://facebook.com/sandawatha',
    'instagram' => 'https://instagram.com/sandawatha',
    'twitter' => 'https://twitter.com/sandawatha',
    'youtube' => 'https://youtube.com/sandawatha'
];
?>

<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandawatha.lk - Find Your Perfect Match in Sri Lanka</title>
    
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
                        'champagne': {
                            50: '#fdfcfb',
                            100: '#fbf7ef',
                            200: '#f7e8d0',
                            300: '#f2d4aa',
                            400: '#e9b875',
                            500: '#dea04f',
                            600: '#c4873d',
                            700: '#a36a32',
                            800: '#85562d',
                            900: '#6d4728'
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'slide': 'slide 15s linear infinite',
                        'fade-in': 'fadeIn 1s ease-out',
                        'fade-up': 'fadeUp 1s ease-out',
                        'scale': 'scale 0.3s ease-in-out'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        fadeUp: {
                            '0%': { 
                                opacity: '0',
                                transform: 'translateY(20px)'
                            },
                            '100%': { 
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        },
                        scale: {
                            '0%': { transform: 'scale(1)' },
                            '50%': { transform: 'scale(1.05)' },
                            '100%': { transform: 'scale(1)' }
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Swiper.js for Testimonials -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap');
        
        :root {
            --swiper-theme-color: #e11d48;
            --swiper-navigation-size: 24px;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        h1, h2, h3, .font-display {
            font-family: 'Playfair Display', serif;
        }
        
        .hero-section {
            background-color: #1a1a1a; /* Fallback color while image loads */
            min-height: 100vh;
            position: relative;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
                            url('https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1920&q=80');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        .hero-section.loaded::before {
            opacity: 1;
        }
        
        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='52' height='26' viewBox='0 0 52 26' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M10 10c0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6h2c0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4v2c-3.314 0-6-2.686-6-6 0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6zm25.464-1.95l8.486 8.486-1.414 1.414-8.486-8.486 1.414-1.414z' /%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.05;
        }
        
        .feature-card {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.5);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(225, 29, 72, 0.15);
            border-color: rgba(225, 29, 72, 0.2);
        }
        
        .testimonial-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.5);
        }
        
        .nav-transparent {
            background: transparent;
            backdrop-filter: none;
        }
        
        .nav-solid {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }
        
        .romantic-gradient {
            background: linear-gradient(135deg, #fecdd3 0%, #e11d48 100%);
        }
        
        .romantic-text-gradient {
            background: linear-gradient(135deg, #e11d48, #be123c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .champagne-gradient {
            background: linear-gradient(135deg, #fbf7ef 0%, #dea04f 100%);
        }
        
        /* Accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Focus styles */
        a:focus, button:focus {
            outline: 2px solid #e11d48;
            outline-offset: 2px;
        }
        
        /* High contrast mode */
        @media (forced-colors: active) {
            .feature-card, .testimonial-card {
                border: 1px solid CanvasText;
            }
        }

        /* Loading state styles */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: #fff;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .loading-overlay.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #fecdd3;
            border-top-color: #e11d48;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Prevent FOUC */
        .no-fouc {
            visibility: hidden;
        }
        .fouc-ready {
            visibility: visible;
        }
    </style>
</head>
<body class="antialiased no-fouc">
    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Include Navigation -->
    <?php include_once '../app/views/shared/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section flex items-center justify-center pt-16 relative">
        <!-- Background Pattern -->
        <div class="hero-pattern"></div>
        
        <!-- Floating Hearts -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-0 transition-opacity duration-500" id="floating-hearts">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-romantic-300/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-romantic-500/20 rounded-full blur-3xl animate-float" style="animation-delay: -3s"></div>
        </div>
        
        <div class="container mx-auto px-4 text-center text-white relative">
            <!-- Main Content -->
            <div class="max-w-4xl mx-auto opacity-0 transition-opacity duration-500" id="hero-content">
                <h1 class="text-5xl md:text-7xl font-display font-bold mb-6 leading-tight">
                    Find Your Perfect Match
                    <span class="block mt-2 text-romantic-200">Your Love Story Begins Here</span>
                </h1>
                <p class="text-xl md:text-2xl mb-12 opacity-90 leading-relaxed">
                    Join Sri Lanka's most trusted matrimony platform where tradition meets technology
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mb-16">
                    <a href="/register.php" 
                       class="group relative px-8 py-4 bg-romantic-600 text-white rounded-full text-lg font-semibold transition-all duration-300 hover:bg-romantic-700 hover:shadow-lg hover:shadow-romantic-600/50 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-600">
                        <span class="relative z-10">Start Your Journey Today</span>
                        <div class="absolute inset-0 rounded-full bg-white/20 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                    </a>
                    <a href="/about.php" 
                       class="px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-full text-lg font-semibold hover:bg-white/20 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white/50">
                        Learn More
                        <svg class="inline-block w-5 h-5 ml-2 -mr-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8 opacity-0 transition-opacity duration-500" id="hero-stats">
                <div class="p-8 bg-white/10 backdrop-blur-sm rounded-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="text-5xl font-bold mb-3 text-romantic-200"><?php echo number_format($totalUsers); ?>+</div>
                    <div class="text-lg font-medium">Active Members</div>
                    <div class="mt-2 text-sm text-white/70">and growing every day</div>
                </div>
                <div class="p-8 bg-white/10 backdrop-blur-sm rounded-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="text-5xl font-bold mb-3 text-romantic-200"><?php echo number_format($verifiedUsers); ?>+</div>
                    <div class="text-lg font-medium">Verified Profiles</div>
                    <div class="mt-2 text-sm text-white/70">100% genuine members</div>
                </div>
                <div class="p-8 bg-white/10 backdrop-blur-sm rounded-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="text-5xl font-bold mb-3 text-romantic-200"><?php echo number_format($totalConnections); ?>+</div>
                    <div class="text-lg font-medium">Connections Made</div>
                    <div class="mt-2 text-sm text-white/70">successful matches</div>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce opacity-0 transition-opacity duration-500" id="scroll-indicator">
                <a href="#features" class="text-white/80 hover:text-white transition-colors duration-300">
                    <span class="sr-only">Scroll down</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gradient-to-b from-white to-romantic-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 text-gray-900">
                    Why Choose Sandawatha.lk?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Experience the perfect blend of tradition and technology in your journey to find love
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Verified Profiles -->
                <div class="feature-card p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-romantic-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-display font-semibold mb-4 text-gray-900">
                        Verified Profiles
                    </h3>
                    <p class="text-gray-600">
                        Every profile is manually verified to ensure you meet genuine people looking for meaningful relationships.
                    </p>
                </div>

                <!-- Horoscope Matching -->
                <div class="feature-card p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-romantic-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-display font-semibold mb-4 text-gray-900">
                        Horoscope Matching
                    </h3>
                    <p class="text-gray-600">
                        Traditional horoscope matching combined with modern compatibility algorithms for better matches.
                    </p>
                </div>

                <!-- AI Matching -->
                <div class="feature-card p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-romantic-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-display font-semibold mb-4 text-gray-900">
                        Smart Matching
                    </h3>
                    <p class="text-gray-600">
                        Our AI-powered system learns your preferences to suggest the most compatible matches for you.
                    </p>
                </div>

                <!-- Privacy & Security -->
                <div class="feature-card p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-16 h-16 bg-romantic-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-display font-semibold mb-4 text-gray-900">
                        Privacy & Security
                    </h3>
                    <p class="text-gray-600">
                        Your privacy is our priority. Advanced security measures protect your personal information.
                    </p>
                </div>
            </div>

            <!-- Additional Features -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 bg-romantic-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold mb-2 text-gray-900">Quick Matching</h4>
                    <p class="text-gray-600">Find compatible matches within minutes</p>
                </div>

                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-12 h-12 bg-romantic-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold mb-2 text-gray-900">Secure Chat</h4>
                    <p class="text-gray-600">Private messaging with matches</p>
                </div>

                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-12 h-12 bg-romantic-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold mb-2 text-gray-900">Instant Alerts</h4>
                    <p class="text-gray-600">Real-time match notifications</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Profiles Section -->
    <?php if (!empty($featuredProfiles)): ?>
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-16" data-aos="fade-up">
                Featured Profiles
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($featuredProfiles as $profile): ?>
                <div class="profile-card bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up">
                    <img src="<?php echo htmlspecialchars($profile['profile_photo'] ?? '/assets/images/default-profile.jpg'); ?>" 
                         alt="Profile" 
                         class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">
                            <?php echo htmlspecialchars($profile['name']); ?>
                        </h3>
                        <div class="text-gray-600 mb-4">
                            <p>
                                <?php echo htmlspecialchars($profile['age']); ?> years • 
                                <?php echo htmlspecialchars($profile['district']); ?> •
                                <?php echo htmlspecialchars($profile['religion']); ?>
                            </p>
                        </div>
                        <p class="text-gray-600 mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($profile['bio'] ?? 'No bio available'); ?>
                        </p>
                        <a href="/profile.php?id=<?php echo $profile['id']; ?>" 
                           class="inline-block w-full px-6 py-3 bg-primary text-white text-center rounded-full hover:bg-red-700 transition transform hover:scale-105">
                            View Profile
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="/search.php" 
                   class="inline-block px-8 py-4 bg-white text-primary border-2 border-primary rounded-full text-lg font-semibold hover:bg-primary hover:text-white transition">
                    View All Profiles
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Testimonials Section -->
    <section class="py-24 bg-gradient-to-b from-romantic-50 to-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-y-0 left-0 w-1/2 bg-gradient-to-r from-romantic-300 to-transparent"></div>
            <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-romantic-300 to-transparent"></div>
        </div>

        <!-- Floating Hearts -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-20 left-10 animate-float" style="animation-delay: -2s">
                <svg class="w-12 h-12 text-romantic-200" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="absolute top-40 right-20 animate-float" style="animation-delay: -4s">
                <svg class="w-8 h-8 text-romantic-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="container mx-auto px-4 relative">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 text-gray-900">
                    Love Stories Begin Here
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Hear from our happy couples who found their perfect match on Sandawatha.lk
                </p>
            </div>

            <!-- Testimonials Slider -->
            <div class="swiper-container testimonials-slider overflow-hidden" data-aos="fade-up">
                <div class="swiper-wrapper">
                    <?php foreach ($testimonials as $testimonial): ?>
                    <div class="swiper-slide p-4">
                        <div class="testimonial-card p-8 rounded-2xl shadow-lg">
                            <div class="flex items-center mb-6">
                                <div class="relative">
                                    <div class="w-20 h-20 rounded-full overflow-hidden bg-romantic-50">
                                        <!-- Blur placeholder while loading -->
                                        <img src="<?php echo htmlspecialchars($testimonial['placeholder']); ?>" 
                                             class="w-full h-full object-cover transition-opacity duration-300 blur"
                                             alt="Loading..." />
                                        <!-- Main image -->
                                        <img src="<?php echo htmlspecialchars($testimonial['photo']); ?>" 
                                             alt="<?php echo htmlspecialchars($testimonial['name']); ?>"
                                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                                             onload="this.previousElementSibling.classList.add('opacity-0')"
                                             onerror="handleImageError(this)"
                                             loading="lazy" />
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 bg-romantic-100 rounded-full p-1">
                                        <svg class="w-6 h-6 text-romantic-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-xl font-display font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($testimonial['name']); ?>
                                    </h3>
                                    <p class="text-romantic-600 font-medium">
                                        <?php echo htmlspecialchars($testimonial['location']); ?>
                                    </p>
                                    <div class="flex items-center mt-1">
                                        <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                        <svg class="w-5 h-5 text-romantic-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <blockquote class="text-gray-600 italic relative">
                                <svg class="absolute -top-2 -left-2 w-8 h-8 text-romantic-100 transform -translate-x-full -translate-y-full" fill="currentColor" viewBox="0 0 32 32">
                                    <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
                                </svg>
                                <?php echo htmlspecialchars($testimonial['message']); ?>
                            </blockquote>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Navigation -->
                <div class="swiper-button-next !text-romantic-600 after:!text-2xl"></div>
                <div class="swiper-button-prev !text-romantic-600 after:!text-2xl"></div>
                <!-- Pagination -->
                <div class="swiper-pagination !-bottom-12"></div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-24 bg-gradient-to-b from-white to-romantic-50 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'44\' height=\'44\' viewBox=\'0 0 44 44\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M22 21.172l-8.485-8.485a2 2 0 00-2.829 2.829L19.172 24l-8.486 8.485a2 2 0 102.829 2.829L22 26.828l8.485 8.486a2 2 0 102.829-2.829L24.828 24l8.486-8.485a2 2 0 10-2.829-2.829L22 21.172z\' fill=\'%23fda4af\' fill-opacity=\'0.1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E')] bg-repeat"></div>
        </div>

        <div class="container mx-auto px-4 relative">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 text-gray-900">
                    Your Journey to Love
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Four simple steps to find your perfect match on Sandawatha.lk
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative">
                <!-- Connecting Lines (Desktop) -->
                <div class="hidden lg:block absolute top-1/2 left-0 w-full h-0.5 bg-romantic-200"></div>
                <div class="hidden lg:block absolute top-1/2 left-1/4 w-0.5 h-32 bg-romantic-200 transform -translate-x-1/2 -translate-y-full"></div>
                <div class="hidden lg:block absolute top-1/2 left-1/2 w-0.5 h-32 bg-romantic-200 transform -translate-x-1/2 translate-y-0"></div>
                <div class="hidden lg:block absolute top-1/2 left-3/4 w-0.5 h-32 bg-romantic-200 transform -translate-x-1/2 -translate-y-full"></div>

                <!-- Step 1: Create Profile -->
                <div class="relative" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-white p-8 rounded-2xl shadow-lg relative z-10">
                        <div class="w-16 h-16 bg-romantic-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-romantic-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">1</div>
                        <h3 class="text-xl font-display font-semibold mb-4 text-center text-gray-900">Create Your Profile</h3>
                        <p class="text-gray-600 text-center">Sign up and create your detailed profile with photos and preferences</p>
                    </div>
                </div>

                <!-- Step 2: Browse Matches -->
                <div class="relative" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-white p-8 rounded-2xl shadow-lg relative z-10">
                        <div class="w-16 h-16 bg-romantic-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-romantic-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">2</div>
                        <h3 class="text-xl font-display font-semibold mb-4 text-center text-gray-900">Browse Matches</h3>
                        <p class="text-gray-600 text-center">Explore profiles and find potential matches based on your preferences</p>
                    </div>
                </div>

                <!-- Step 3: Connect & Chat -->
                <div class="relative" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white p-8 rounded-2xl shadow-lg relative z-10">
                        <div class="w-16 h-16 bg-romantic-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-romantic-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">3</div>
                        <h3 class="text-xl font-display font-semibold mb-4 text-center text-gray-900">Connect & Chat</h3>
                        <p class="text-gray-600 text-center">Start conversations and get to know your matches better</p>
                    </div>
                </div>

                <!-- Step 4: Begin Your Journey -->
                <div class="relative" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-white p-8 rounded-2xl shadow-lg relative z-10">
                        <div class="w-16 h-16 bg-romantic-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-romantic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-romantic-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">4</div>
                        <h3 class="text-xl font-display font-semibold mb-4 text-center text-gray-900">Begin Your Journey</h3>
                        <p class="text-gray-600 text-center">Take the next step towards your happily ever after</p>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center mt-16" data-aos="fade-up">
                <a href="/register.php" 
                   class="inline-flex items-center px-8 py-4 bg-romantic-600 text-white rounded-full text-lg font-semibold transition-all duration-300 hover:bg-romantic-700 hover:shadow-lg hover:shadow-romantic-600/50 transform hover:scale-105 group">
                    Start Your Journey
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-24 bg-romantic-50 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-romantic-600/5 to-champagne-200/5"></div>
            <div class="absolute inset-y-0 left-0 w-1/2 bg-gradient-to-r from-romantic-100/20 to-transparent"></div>
            <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-champagne-100/20 to-transparent"></div>
        </div>

        <!-- Floating Hearts -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-1/4 left-1/4 animate-float" style="animation-delay: -3s">
                <svg class="w-24 h-24 text-romantic-200/50" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="absolute bottom-1/4 right-1/4 animate-float" style="animation-delay: -5s">
                <svg class="w-32 h-32 text-romantic-300/30" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="container mx-auto px-4 relative">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl font-display font-bold mb-6 text-gray-900" data-aos="fade-up">
                    Ready to Find Your Perfect Match?
                </h2>
                <p class="text-xl text-gray-600 mb-12" data-aos="fade-up" data-aos-delay="100">
                    Join thousands of Sri Lankans who have found their soulmate on Sandawatha.lk
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6" data-aos="fade-up" data-aos-delay="200">
                    <a href="/register.php" 
                       class="w-full sm:w-auto px-8 py-4 bg-romantic-600 text-white rounded-full text-lg font-semibold transition-all duration-300 hover:bg-romantic-700 hover:shadow-lg hover:shadow-romantic-600/50 transform hover:scale-105 group">
                        Create Your Profile
                        <svg class="inline-block w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="/about.php" 
                       class="w-full sm:w-auto px-8 py-4 bg-white text-romantic-600 rounded-full text-lg font-semibold border-2 border-romantic-600 transition-all duration-300 hover:bg-romantic-50 transform hover:scale-105">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <!-- Company Info -->
                <div>
                    <h3 class="text-2xl font-semibold mb-6">Sandawatha.lk</h3>
                    <p class="text-gray-400 mb-6">
                        Sri Lanka's premier matrimonial platform combining tradition with technology.
                    </p>
                    <!-- Social Links -->
                    <div class="flex space-x-4">
                        <?php foreach ($socialLinks as $platform => $url): ?>
                        <a href="<?php echo htmlspecialchars($url); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="text-gray-400 hover:text-white transition-colors duration-300"
                           aria-label="Follow us on <?php echo ucfirst($platform); ?>">
                            <i class="fab fa-<?php echo $platform; ?> text-xl"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Quick Links</h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="/about.php" class="text-gray-400 hover:text-white transition-colors duration-300">
                                About Us
                            </a>
                        </li>
                        <li>
                            <a href="/blog.php" class="text-gray-400 hover:text-white transition-colors duration-300">
                                Blog
                            </a>
                        </li>
                        <li>
                            <a href="/contact.php" class="text-gray-400 hover:text-white transition-colors duration-300">
                                Contact Us
                            </a>
                        </li>
                        <li>
                            <a href="/faq.php" class="text-gray-400 hover:text-white transition-colors duration-300">
                                FAQ
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Legal</h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="/privacy.php" class="text-gray-400 hover:text-white transition-colors duration-300">
                                Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="/terms.php" class="text-gray-400 hover:text-white transition-colors duration-300">
                                Terms of Service
                            </a>
                        </li>
                        <li>
                            <a href="/refund.php" class="text-gray-400 hover:text-white transition-colors duration-300">
                                Refund Policy
                            </a>
                        </li>
                        <li>
                            <a href="/safety.php" class="text-gray-400 hover:text-white transition-colors duration-300">
                                Safety Tips
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Stay Updated</h3>
                    <p class="text-gray-400 mb-4">
                        Subscribe to our newsletter for updates and success stories.
                    </p>
                    <form action="/subscribe.php" method="POST" class="space-y-4">
                        <div class="relative">
                            <input type="email" 
                                   name="email" 
                                   placeholder="Enter your email"
                                   required
                                   class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <button type="submit" 
                                class="w-full px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Sandawatha.lk. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Initialize JavaScript -->
    <script>
        // Preload hero background image
        const heroImage = new Image();
        heroImage.src = 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1920&q=80';
        
        // Show content when everything is ready
        window.addEventListener('load', function() {
            // Remove loading overlay
            document.querySelector('.loading-overlay').classList.add('hidden');
            
            // Show main content
            document.body.classList.add('fouc-ready');
            
            // Fade in hero section elements
            document.querySelector('.hero-section').classList.add('loaded');
            
            // Fade in floating hearts
            setTimeout(() => {
                document.getElementById('floating-hearts').style.opacity = '1';
                document.getElementById('hero-content').style.opacity = '1';
                document.getElementById('hero-stats').style.opacity = '1';
                document.getElementById('scroll-indicator').style.opacity = '1';
            }, 300);
        });

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS with a slight delay to prevent blocking
            setTimeout(() => {
                AOS.init({
                    duration: 800,
                    easing: 'ease-out',
                    once: true,
                    disable: 'mobile' // Disable on mobile for better performance
                });
            }, 100);

            // Initialize Swiper only after images are loaded
            const testimonialSlider = new Swiper('.testimonials-slider', {
                init: false, // Prevent auto-initialization
                slidesPerView: 1,
                spaceBetween: 32,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                }
            });

            // Initialize Swiper after all images are loaded
            Promise.all(Array.from(document.querySelectorAll('.testimonials-slider img'))
                .filter(img => !img.complete)
                .map(img => new Promise(resolve => {
                    img.onload = img.onerror = resolve;
                })))
                .then(() => {
                    testimonialSlider.init();
                })
                .catch(err => {
                    console.error('Error loading testimonial images:', err);
                    testimonialSlider.init(); // Initialize anyway to prevent blocking
                });

            // Navbar Scroll Effect - Use requestAnimationFrame for smooth performance
            const navbar = document.querySelector('.navbar');
            let ticking = false;

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        if (window.scrollY > 50) {
                            navbar.classList.remove('nav-transparent');
                            navbar.classList.add('nav-solid');
                        } else {
                            navbar.classList.add('nav-transparent');
                            navbar.classList.remove('nav-solid');
                        }
                        ticking = false;
                    });
                    ticking = true;
                }
            });

            // Mobile Menu Toggle - Optimize for performance
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuButtons = document.querySelectorAll('[aria-label="Toggle menu"], [aria-label="Close menu"]');

            mobileMenuButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const isOpen = mobileMenu.classList.contains('translate-x-0');
                    requestAnimationFrame(() => {
                        mobileMenu.classList.toggle('translate-x-full', isOpen);
                        mobileMenu.classList.toggle('translate-x-0', !isOpen);
                    });
                });
            });

            // Smooth Scroll - Optimize for performance
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Close mobile menu if open
                        requestAnimationFrame(() => {
                            mobileMenu.classList.add('translate-x-full');
                            mobileMenu.classList.remove('translate-x-0');
                        });
                    }
                });
            });
        });

        // Add error handling for images
        function handleImageError(img) {
            img.onerror = null; // Prevent infinite loop
            img.src = 'https://picsum.photos/400/400'; // Fallback to placeholder
        }
    </script>
</body>
</html> 