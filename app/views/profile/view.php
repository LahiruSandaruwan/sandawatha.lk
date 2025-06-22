<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Get profile ID from URL
$profile_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$profile_id) {
    header('Location: /404.php');
    exit();
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Photo.php';
require_once __DIR__ . '/../../models/Horoscope.php';
require_once __DIR__ . '/../../models/Preference.php';
require_once __DIR__ . '/../../models/Interest.php';

// Initialize models
$userModel = new User($db);
$photoModel = new Photo($db);
$horoscopeModel = new Horoscope($db);
$preferenceModel = new Preference($db);
$interestModel = new Interest($db);

// Fetch user data
$profile = $userModel->getProfileById($profile_id);
if (!$profile) {
    header('Location: /404.php');
    exit();
}

// Check if current user has sent interest
$hasInterest = $interestModel->hasInterest($_SESSION['user_id'], $profile_id);

// Get profile photos
$photos = $photoModel->getPhotosByUserId($profile_id);

// Get horoscope details
$horoscope = $horoscopeModel->getHoroscopeByUserId($profile_id);

// Get preferences
$preferences = $preferenceModel->getPreferencesByUserId($profile_id);

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';

// Translations
$translations = [
    'en' => [
        'basic_info' => 'Basic Information',
        'photos' => 'Photos',
        'horoscope' => 'Horoscope',
        'preferences' => 'Preferences',
        'send_interest' => 'Send Interest',
        'interest_sent' => 'Interest Sent',
        'contact' => 'Contact Now',
        'age' => 'Age',
        'height' => 'Height',
        'religion' => 'Religion',
        'caste' => 'Caste',
        'education' => 'Education',
        'occupation' => 'Occupation',
        'location' => 'Location',
        'marital_status' => 'Marital Status',
        'about_me' => 'About Me',
        'family_info' => 'Family Information',
        'rashi' => 'Rashi',
        'nakshatra' => 'Nakshatra',
        'gan' => 'Gan',
        'nadi' => 'Nadi',
        'charan' => 'Charan',
        'preferred_age' => 'Preferred Age',
        'preferred_height' => 'Preferred Height',
        'preferred_education' => 'Preferred Education',
        'preferred_occupation' => 'Preferred Occupation',
        'preferred_location' => 'Preferred Location'
    ],
    // Add Sinhala and Tamil translations here
];

// Get translations for current language
$t = $translations[$currentLang];

// Include header
require_once __DIR__ . '/../shared/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="relative h-48 bg-gradient-to-r from-purple-500 to-indigo-600">
            <img src="<?= htmlspecialchars($profile['cover_photo'] ?? '/assets/images/default-cover.jpg') ?>" 
                 alt="Cover Photo" 
                 class="w-full h-full object-cover">
        </div>
        
        <div class="relative px-4 sm:px-6 lg:px-8 pb-8">
            <div class="relative -mt-16 flex items-end space-x-5">
                <div class="flex">
                    <img src="<?= htmlspecialchars($profile['profile_photo'] ?? '/assets/images/default-avatar.jpg') ?>" 
                         alt="<?= htmlspecialchars($profile['name']) ?>" 
                         class="h-32 w-32 rounded-full ring-4 ring-white bg-white object-cover">
                </div>
                <div class="flex-1 min-w-0 mb-2">
                    <h2 class="text-2xl font-bold text-gray-900 truncate">
                        <?= htmlspecialchars($profile['name']) ?>
                    </h2>
                    <p class="text-sm text-gray-500">
                        <?= htmlspecialchars($profile['age']) ?> Years • 
                        <?= htmlspecialchars($profile['location']) ?> • 
                        <?= htmlspecialchars($profile['occupation']) ?>
                    </p>
                </div>
                <div class="flex space-x-3 flex-shrink-0">
                    <?php if (!$hasInterest): ?>
                        <button id="sendInterest" 
                                data-profile-id="<?= $profile_id ?>" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <?= htmlspecialchars($t['send_interest']) ?>
                        </button>
                    <?php else: ?>
                        <button disabled class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 cursor-not-allowed">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <?= htmlspecialchars($t['interest_sent']) ?>
                        </button>
                    <?php endif; ?>
                    
                    <button id="contactNow" 
                            data-profile-id="<?= $profile_id ?>" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <?= htmlspecialchars($t['contact']) ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mt-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600" data-tab="info">
                    <?= htmlspecialchars($t['basic_info']) ?>
                </button>
                <button class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="photos">
                    <?= htmlspecialchars($t['photos']) ?>
                </button>
                <button class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="horoscope">
                    <?= htmlspecialchars($t['horoscope']) ?>
                </button>
                <button class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="preferences">
                    <?= htmlspecialchars($t['preferences']) ?>
                </button>
            </nav>
        </div>

        <!-- Tab Contents -->
        <div class="mt-8">
            <!-- Basic Info Tab -->
            <div id="info" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4"><?= htmlspecialchars($t['basic_info']) ?></h3>
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['age']) ?></dt>
                                <dd class="text-sm text-gray-900"><?= htmlspecialchars($profile['age']) ?> Years</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['height']) ?></dt>
                                <dd class="text-sm text-gray-900"><?= htmlspecialchars($profile['height']) ?> cm</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['religion']) ?></dt>
                                <dd class="text-sm text-gray-900"><?= htmlspecialchars($profile['religion']) ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['education']) ?></dt>
                                <dd class="text-sm text-gray-900"><?= htmlspecialchars($profile['education']) ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['occupation']) ?></dt>
                                <dd class="text-sm text-gray-900"><?= htmlspecialchars($profile['occupation']) ?></dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4"><?= htmlspecialchars($t['about_me']) ?></h3>
                        <p class="text-sm text-gray-600 whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($profile['about'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Photos Tab -->
            <div id="photos" class="tab-content hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($photos as $photo): ?>
                        <div class="relative aspect-w-1 aspect-h-1 rounded-lg overflow-hidden bg-gray-100">
                            <img src="<?= htmlspecialchars($photo['url']) ?>" 
                                 alt="Profile Photo" 
                                 class="object-cover cursor-pointer hover:opacity-75 transition-opacity"
                                 onclick="openPhotoModal(this.src)">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Horoscope Tab -->
            <div id="horoscope" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4"><?= htmlspecialchars($t['horoscope']) ?></h3>
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['rashi']) ?></dt>
                                    <dd class="text-sm text-gray-900"><?= htmlspecialchars($horoscope['rashi']) ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['nakshatra']) ?></dt>
                                    <dd class="text-sm text-gray-900"><?= htmlspecialchars($horoscope['nakshatra']) ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['gan']) ?></dt>
                                    <dd class="text-sm text-gray-900"><?= htmlspecialchars($horoscope['gan']) ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['nadi']) ?></dt>
                                    <dd class="text-sm text-gray-900"><?= htmlspecialchars($horoscope['nadi']) ?></dd>
                                </div>
                            </dl>
                        </div>
                        <div class="flex items-center justify-center">
                            <img src="<?= htmlspecialchars($horoscope['chart_image']) ?>" 
                                 alt="Horoscope Chart" 
                                 class="max-w-full h-auto rounded-lg shadow-lg">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences Tab -->
            <div id="preferences" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4"><?= htmlspecialchars($t['preferences']) ?></h3>
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['preferred_age']) ?></dt>
                            <dd class="text-sm text-gray-900"><?= htmlspecialchars($preferences['age_range']) ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['preferred_height']) ?></dt>
                            <dd class="text-sm text-gray-900"><?= htmlspecialchars($preferences['height_range']) ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['preferred_education']) ?></dt>
                            <dd class="text-sm text-gray-900"><?= htmlspecialchars($preferences['education']) ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['preferred_occupation']) ?></dt>
                            <dd class="text-sm text-gray-900"><?= htmlspecialchars($preferences['occupation']) ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($t['preferred_location']) ?></dt>
                            <dd class="text-sm text-gray-900"><?= htmlspecialchars($preferences['location']) ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div id="photoModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" onclick="closePhotoModal()" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <img id="modalImage" src="" alt="Full size photo" class="w-full h-auto rounded-lg">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Tab switching
    $('.tab-button').click(function() {
        const tabId = $(this).data('tab');
        
        // Update button states
        $('.tab-button').removeClass('border-indigo-500 text-indigo-600')
                       .addClass('border-transparent text-gray-500');
        $(this).removeClass('border-transparent text-gray-500')
               .addClass('border-indigo-500 text-indigo-600');
        
        // Show selected tab content
        $('.tab-content').addClass('hidden');
        $(`#${tabId}`).removeClass('hidden');
    });

    // Send Interest
    $('#sendInterest').click(function() {
        const profileId = $(this).data('profile-id');
        
        $.ajax({
            url: '/api/interests/send.php',
            method: 'POST',
            data: { profile_id: profileId },
            success: function(response) {
                if (response.success) {
                    // Update button state
                    $('#sendInterest').prop('disabled', true)
                                    .removeClass('bg-indigo-600 hover:bg-indigo-700')
                                    .addClass('bg-green-600')
                                    .html(`<svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                          </svg> Interest Sent`);
                } else {
                    alert(response.message || 'Failed to send interest. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again later.');
            }
        });
    });

    // Contact Now
    $('#contactNow').click(function() {
        const profileId = $(this).data('profile-id');
        window.location.href = `/chat.php?user_id=${profileId}`;
    });
});

// Photo modal functions
function openPhotoModal(src) {
    $('#modalImage').attr('src', src);
    $('#photoModal').removeClass('hidden');
}

function closePhotoModal() {
    $('#photoModal').addClass('hidden');
}

// Close modal on escape key
$(document).keydown(function(e) {
    if (e.keyCode === 27) { // escape key
        closePhotoModal();
    }
});

// Close modal on outside click
$('#photoModal').click(function(e) {
    if (e.target === this) {
        closePhotoModal();
    }
});
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?> 