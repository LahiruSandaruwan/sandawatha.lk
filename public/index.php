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

// Social media links
$socialLinks = [
    'facebook' => 'https://facebook.com/sandawatha',
    'instagram' => 'https://instagram.com/sandawatha',
    'twitter' => 'https://twitter.com/sandawatha',
    'youtube' => 'https://youtube.com/sandawatha'
];

// Testimonials data
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
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="antialiased">
    <!-- Navigation -->
    <?php require_once '../app/views/shared/navbar.php'; ?>

    <!-- Hero Section -->
    <?php require_once '../app/views/shared/hero.php'; ?>

    <!-- Features Section -->
    <?php require_once '../app/views/shared/features.php'; ?>

    <!-- How It Works Section -->
    <?php require_once '../app/views/shared/how-it-works.php'; ?>

    <!-- Testimonials Section -->
    <?php require_once '../app/views/shared/testimonials.php'; ?>

    <!-- CTA Section -->
    <?php require_once '../app/views/shared/cta.php'; ?>

    <!-- Footer -->
    <?php require_once '../app/views/shared/footer.php'; ?>

    <!-- Initialize AOS -->
    <script>
        $(document).ready(function() {
            // Initialize AOS
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                mirror: false
            });
        });
    </script>
</body>
</html> 