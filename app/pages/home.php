<?php
// Page title for header
$pageTitle = 'Find Your Perfect Match in Sri Lanka';

// Language handling
$languages = ['en' => 'English', 'si' => 'සිංහල', 'ta' => 'தமிழ்'];
$currentLang = isset($_GET['lang']) && array_key_exists($_GET['lang'], $languages) ? $_GET['lang'] : 'en';

// Initialize variables
$featuredProfiles = [];
$totalUsers = 0;
$verifiedUsers = 0;
$totalConnections = 0;

// Helper function to format numbers or return string as is
function formatNumber($value) {
    if (is_numeric($value)) {
        return number_format($value);
    }
    return $value;
}

try {
    // Get database connection
    $pdo = require_once ROOT_PATH . '/config/database.php';
    
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }

    // Fetch featured profiles
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

    // Get total user counts
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'");
    $totalUsers = (int)$stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active' AND email_verified_at IS NOT NULL");
    $verifiedUsers = (int)$stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM messages");
    $totalConnections = (int)$stmt->fetchColumn();

} catch (Exception $e) {
    // Log error but don't expose details
    error_log("Database error in home.php: " . $e->getMessage());
    
    // Set default values for a graceful fallback
    $featuredProfiles = [];
    $totalUsers = '10,000+';
    $verifiedUsers = '8,000+';
    $totalConnections = '5,000+';
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
        'photo' => '/assets/images/testimonials/couple1.jpg',
        'placeholder' => '/assets/images/testimonials/couple1-placeholder.jpg',
        'message' => 'We found each other on Sandawatha.lk and our horoscopes matched perfectly! Now happily married for 2 years.',
        'rating' => 5,
        'location' => 'Colombo'
    ],
    [
        'name' => 'Malini & Dinesh',
        'photo' => '/assets/images/testimonials/couple2.jpg',
        'placeholder' => '/assets/images/testimonials/couple2-placeholder.jpg',
        'message' => 'The AI matching system introduced us, and it was like magic from our first meeting. Getting married next month!',
        'rating' => 5,
        'location' => 'Kandy'
    ],
    [
        'name' => 'Kumari & Rajitha',
        'photo' => '/assets/images/testimonials/couple3.jpg',
        'placeholder' => '/assets/images/testimonials/couple3-placeholder.jpg',
        'message' => 'Thank you Sandawatha for helping us find true love. The verification process gave us peace of mind.',
        'rating' => 5,
        'location' => 'Galle'
    ]
];

// Main content starts here - header is already included by public/index.php
?>

<main id="main-content">
    <!-- Hero Section -->
    <?php require_once ROOT_PATH . '/app/views/shared/hero.php'; ?>

    <!-- Features Section -->
    <?php require_once ROOT_PATH . '/app/views/shared/features.php'; ?>

    <!-- How It Works Section -->
    <?php require_once ROOT_PATH . '/app/views/shared/how-it-works.php'; ?>

    <!-- Featured Profiles Section -->
    <?php if (!empty($featuredProfiles)): ?>
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Featured Profiles</h2>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                    Find your perfect match from our verified members
                </p>
            </div>
            
            <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($featuredProfiles as $profile): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="aspect-w-3 aspect-h-4">
                            <img class="object-cover w-full h-full" 
                                 src="<?php echo htmlspecialchars($profile['profile_photo']); ?>" 
                                 alt="<?php echo htmlspecialchars($profile['name']); ?>'s photo"
                                 loading="lazy">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($profile['name']); ?></h3>
                            <p class="mt-2 text-gray-600">
                                <?php echo htmlspecialchars($profile['age']); ?> years • 
                                <?php echo htmlspecialchars($profile['district']); ?> • 
                                <?php echo htmlspecialchars($profile['religion']); ?>
                            </p>
                            <p class="mt-4 text-gray-500 line-clamp-3"><?php echo htmlspecialchars($profile['bio']); ?></p>
                            <a href="/profile/<?php echo $profile['id']; ?>" 
                               class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-romantic-600 hover:bg-romantic-700">
                                View Profile
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Statistics Section -->
    <section class="py-12 bg-romantic-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                <div class="text-center">
                    <div class="text-4xl font-bold text-white"><?php echo formatNumber($totalUsers); ?>+</div>
                    <div class="mt-2 text-romantic-100">Active Members</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-white"><?php echo formatNumber($verifiedUsers); ?>+</div>
                    <div class="mt-2 text-romantic-100">Verified Profiles</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-white"><?php echo formatNumber($totalConnections); ?>+</div>
                    <div class="mt-2 text-romantic-100">Successful Matches</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <?php require_once ROOT_PATH . '/app/views/shared/testimonials.php'; ?>

    <!-- CTA Section -->
    <?php require_once ROOT_PATH . '/app/views/shared/cta.php'; ?>
</main>

<!-- Initialize AOS -->
<script>
    $(document).ready(function() {
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    });
</script> 