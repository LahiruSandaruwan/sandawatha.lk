<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug mode (set in config or environment)
define('DEBUG_MODE', true);

// Error handling for development
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Include necessary files
try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/User.php';
    require_once __DIR__ . '/../../models/MatchMaking.php';
    require_once __DIR__ . '/../../models/District.php';
    require_once __DIR__ . '/../../models/Religion.php';
    require_once __DIR__ . '/../../models/Caste.php';
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die("Error loading required files: " . htmlspecialchars($e->getMessage()));
    } else {
        die("An error occurred. Please try again later.");
    }
}

// Initialize models and fetch data with error handling
try {
    $db = Database::getInstance()->getConnection();
    $userModel = new User($db);
    $matchModel = new MatchMaking($db);
    $districtModel = new District($db);
    $religionModel = new Religion($db);
    $casteModel = new Caste($db);

    // Get filter options
    $districts = $districtModel->getAllDistricts() ?? [];
    $religions = $religionModel->getAllReligions() ?? [];
    $castes = $casteModel->getAllCastes() ?? [];

    // Get user preferences for default filter values
    $userPreferences = $userModel->getUserPreferences($_SESSION['user_id']) ?? [];
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    } else {
        die("An error occurred while fetching data. Please try again later.");
    }
}

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';

// Translations
$translations = [
    'en' => [
        'matches' => 'Your Matches',
        'filters' => 'Search Filters',
        'age_range' => 'Age Range',
        'district' => 'District',
        'religion' => 'Religion',
        'caste' => 'Caste',
        'gender' => 'Gender',
        'marital_status' => 'Marital Status',
        'apply_filters' => 'Apply Filters',
        'reset_filters' => 'Reset',
        'match' => 'Match',
        'view_profile' => 'View Profile',
        'no_matches' => 'No matches found',
        'loading' => 'Loading matches...',
        'years' => 'years',
        'select_district' => 'Select District',
        'select_religion' => 'Select Religion',
        'select_caste' => 'Select Caste',
        'select_gender' => 'Select Gender',
        'select_marital_status' => 'Select Marital Status',
        'male' => 'Male',
        'female' => 'Female'
    ],
    'si' => [
        // Add Sinhala translations here
    ],
    'ta' => [
        // Add Tamil translations here
    ]
];

// Get translations for current language with fallback to English
$t = array_merge($translations['en'], $translations[$currentLang] ?? []);

// Include header
require_once __DIR__ . '/../shared/header.php';
?>

<!-- Debug information in development mode -->
<?php if (DEBUG_MODE): ?>
    <script>
        console.log('Debug Mode: Active');
        console.log('User ID:', <?= json_encode($_SESSION['user_id']) ?>);
        console.log('Language:', <?= json_encode($currentLang) ?>);
        console.log('User Preferences:', <?= json_encode($userPreferences) ?>);
    </script>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="md:flex md:space-x-8">
        <!-- Filters Sidebar -->
        <div class="md:w-1/4">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-6"><?= htmlspecialchars($t['filters']) ?></h2>
                
                <form id="matchFilters" class="space-y-6">
                    <!-- Gender Filter -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            <?= htmlspecialchars($t['gender']) ?>
                        </label>
                        <select id="gender" 
                                name="gender" 
                                class="form-select w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                            <option value=""><?= htmlspecialchars($t['select_gender']) ?></option>
                            <option value="male"><?= htmlspecialchars($t['male']) ?></option>
                            <option value="female"><?= htmlspecialchars($t['female']) ?></option>
                        </select>
                    </div>

                    <!-- Age Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?= htmlspecialchars($t['age_range']) ?>
                        </label>
                        <div class="flex space-x-4">
                            <input type="number" 
                                   name="age_min" 
                                   min="18" 
                                   max="80" 
                                   value="<?= htmlspecialchars($userPreferences['age_min'] ?? '') ?>"
                                   class="form-input w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300" 
                                   placeholder="Min">
                            <input type="number" 
                                   name="age_max" 
                                   min="18" 
                                   max="80" 
                                   value="<?= htmlspecialchars($userPreferences['age_max'] ?? '') ?>"
                                   class="form-input w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300" 
                                   placeholder="Max">
                        </div>
                    </div>

                    <!-- District Filter -->
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-2">
                            <?= htmlspecialchars($t['district']) ?>
                        </label>
                        <select id="district" 
                                name="district" 
                                class="form-select w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                            <option value=""><?= htmlspecialchars($t['select_district']) ?></option>
                            <?php foreach ($districts as $district): ?>
                                <option value="<?= htmlspecialchars($district['id']) ?>"
                                        <?= ($userPreferences['district_id'] ?? '') == $district['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($district["name_$currentLang"] ?? $district['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Religion Filter -->
                    <div>
                        <label for="religion" class="block text-sm font-medium text-gray-700 mb-2">
                            <?= htmlspecialchars($t['religion']) ?>
                        </label>
                        <select id="religion" 
                                name="religion" 
                                class="form-select w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                            <option value=""><?= htmlspecialchars($t['select_religion']) ?></option>
                            <?php foreach ($religions as $religion): ?>
                                <option value="<?= htmlspecialchars($religion['id']) ?>"
                                        <?= ($userPreferences['religion_id'] ?? '') == $religion['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($religion["name_$currentLang"] ?? $religion['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Caste Filter -->
                    <div>
                        <label for="caste" class="block text-sm font-medium text-gray-700 mb-2">
                            <?= htmlspecialchars($t['caste']) ?>
                        </label>
                        <select id="caste" 
                                name="caste" 
                                class="form-select w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                            <option value=""><?= htmlspecialchars($t['select_caste']) ?></option>
                            <?php foreach ($castes as $caste): ?>
                                <option value="<?= htmlspecialchars($caste['id']) ?>"
                                        data-religion="<?= htmlspecialchars($caste['religion_id']) ?>"
                                        <?= ($userPreferences['caste_id'] ?? '') == $caste['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($caste["name_$currentLang"] ?? $caste['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Marital Status Filter -->
                    <div>
                        <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-2">
                            <?= htmlspecialchars($t['marital_status']) ?>
                        </label>
                        <select id="marital_status" 
                                name="marital_status" 
                                class="form-select w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                            <option value=""><?= htmlspecialchars($t['select_marital_status']) ?></option>
                            <option value="never_married" <?= ($userPreferences['marital_status'] ?? '') == 'never_married' ? 'selected' : '' ?>>Never Married</option>
                            <option value="divorced" <?= ($userPreferences['marital_status'] ?? '') == 'divorced' ? 'selected' : '' ?>>Divorced</option>
                            <option value="widowed" <?= ($userPreferences['marital_status'] ?? '') == 'widowed' ? 'selected' : '' ?>>Widowed</option>
                            <option value="separated" <?= ($userPreferences['marital_status'] ?? '') == 'separated' ? 'selected' : '' ?>>Separated</option>
                        </select>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex space-x-3">
                        <button type="submit" 
                                class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <?= htmlspecialchars($t['apply_filters']) ?>
                        </button>
                        <button type="reset" 
                                class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <?= htmlspecialchars($t['reset_filters']) ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Grid -->
        <div class="md:w-3/4 mt-8 md:mt-0">
            <div id="matchResults" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Results will be loaded here via AJAX -->
                <div class="col-span-full text-center py-12 text-gray-500">
                    <?= htmlspecialchars($t['loading']) ?>
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="mt-8 flex justify-center space-x-2">
                <!-- Pagination will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Match Card Template -->
<template id="matchCardTemplate">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <div class="relative pb-48">
            <img class="match-photo absolute h-full w-full object-cover" src="" alt="">
            <div class="absolute top-0 right-0 m-2">
                <div class="match-percentage text-white text-sm font-semibold px-2.5 py-0.5 rounded-full"></div>
            </div>
        </div>
        <div class="p-4">
            <h3 class="match-name text-lg font-semibold text-gray-900 mb-1"></h3>
            <div class="match-info space-y-1 text-sm text-gray-500 mb-4">
                <p class="match-age"></p>
                <p class="match-district"></p>
                <p class="match-religion"></p>
            </div>
            <a href="#" class="match-link block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <?= htmlspecialchars($t['view_profile']) ?>
            </a>
        </div>
    </div>
</template>

<!-- Pagination Template -->
<template id="paginationTemplate">
    <button class="prev-page px-3 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
        &larr;
    </button>
    <div class="page-numbers flex space-x-2"></div>
    <button class="next-page px-3 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
        &rarr;
    </button>
</template>

<script>
$(document).ready(function() {
    let currentRequest = null;
    let currentPage = 1;
    const matchCardTemplate = document.getElementById('matchCardTemplate');
    const paginationTemplate = document.getElementById('paginationTemplate');
    
    // Function to load matches
    function loadMatches(page = 1, filters = {}) {
        const resultsContainer = $('#matchResults');
        const paginationContainer = $('#pagination');
        
        // Add page number to filters
        filters.page = page;
        
        // Show loading state
        resultsContainer.html(`
            <div class="col-span-full text-center py-12 text-gray-500">
                <?= htmlspecialchars($t['loading']) ?>
            </div>
        `);
        
        // Abort previous request if exists
        if (currentRequest) {
            currentRequest.abort();
        }
        
        // Make new request
        currentRequest = $.ajax({
            url: '/api/matches/search.php',
            method: 'POST',
            data: filters,
            success: function(response) {
                if (response.success) {
                    resultsContainer.empty();
                    
                    if (response.matches.length > 0) {
                        response.matches.forEach(match => {
                            const card = matchCardTemplate.content.cloneNode(true);
                            
                            // Set match data
                            const photoEl = card.querySelector('.match-photo');
                            photoEl.src = match.profile_photo || '/assets/images/default-avatar.jpg';
                            photoEl.alt = `${match.name}'s photo`;
                            
                            card.querySelector('.match-percentage').textContent = `${match.match_percentage}% <?= $t['match'] ?>`;
                            card.querySelector('.match-name').textContent = match.name;
                            card.querySelector('.match-age').textContent = `${match.age} <?= $t['years'] ?>`;
                            card.querySelector('.match-district').textContent = match.district;
                            card.querySelector('.match-religion').textContent = match.religion;
                            card.querySelector('.match-link').href = `/profile.php?id=${match.id}`;
                            
                            // Add color classes based on match percentage
                            const percentageEl = card.querySelector('.match-percentage');
                            if (match.match_percentage >= 80) {
                                percentageEl.classList.add('bg-green-500');
                            } else if (match.match_percentage >= 60) {
                                percentageEl.classList.add('bg-yellow-500');
                            } else {
                                percentageEl.classList.add('bg-gray-500');
                            }
                            
                            resultsContainer.append(card);
                        });
                        
                        // Update pagination
                        updatePagination(response.pagination);
                    } else {
                        resultsContainer.html(`
                            <div class="col-span-full text-center py-12 text-gray-500">
                                <?= htmlspecialchars($t['no_matches']) ?>
                            </div>
                        `);
                        paginationContainer.empty();
                    }
                } else {
                    throw new Error(response.message || 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                resultsContainer.html(`
                    <div class="col-span-full text-center py-12 text-red-500">
                        ${status === 'abort' ? '' : 'An error occurred while loading matches. Please try again.'}
                    </div>
                `);
                paginationContainer.empty();
                
                if (DEBUG_MODE && status !== 'abort') {
                    console.error('Ajax Error:', {xhr, status, error});
                }
            }
        });
    }
    
    // Function to update pagination
    function updatePagination(pagination) {
        const container = $('#pagination');
        container.empty();
        
        if (!pagination || pagination.total_pages <= 1) {
            return;
        }
        
        const template = paginationTemplate.content.cloneNode(true);
        const pageNumbers = template.querySelector('.page-numbers');
        
        // Previous button
        const prevBtn = template.querySelector('.prev-page');
        prevBtn.disabled = pagination.current_page <= 1;
        prevBtn.onclick = () => loadMatches(pagination.current_page - 1, getFilters());
        
        // Page numbers
        for (let i = 1; i <= pagination.total_pages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `px-3 py-1 rounded-md border ${i === pagination.current_page ? 'border-indigo-500 bg-indigo-50 text-indigo-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'}`;
            pageBtn.textContent = i;
            pageBtn.onclick = () => loadMatches(i, getFilters());
            pageNumbers.appendChild(pageBtn);
        }
        
        // Next button
        const nextBtn = template.querySelector('.next-page');
        nextBtn.disabled = pagination.current_page >= pagination.total_pages;
        nextBtn.onclick = () => loadMatches(pagination.current_page + 1, getFilters());
        
        container.append(template);
    }
    
    // Function to get current filters
    function getFilters() {
        const formData = new FormData($('#matchFilters')[0]);
        const filters = {};
        for (const [key, value] of formData.entries()) {
            if (value) filters[key] = value;
        }
        return filters;
    }
    
    // Handle filter form submission
    $('#matchFilters').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadMatches(currentPage, getFilters());
    });
    
    // Handle filter reset
    $('#matchFilters').on('reset', function(e) {
        setTimeout(() => {
            currentPage = 1;
            loadMatches(currentPage);
        }, 0);
    });
    
    // Handle religion change to filter castes
    $('#religion').on('change', function() {
        const religionId = $(this).val();
        const casteSelect = $('#caste');
        
        casteSelect.find('option').show();
        if (religionId) {
            casteSelect.find('option:not([value=""]):not([data-religion="' + religionId + '"])').hide();
        }
        
        // Reset caste if it doesn't belong to selected religion
        const currentCaste = casteSelect.val();
        const currentCasteReligion = casteSelect.find('option[value="' + currentCaste + '"]').data('religion');
        if (currentCaste && currentCasteReligion != religionId) {
            casteSelect.val('');
        }
    });
    
    // Load initial matches
    loadMatches(currentPage);
    
    // Trigger initial religion change to setup caste filter
    $('#religion').trigger('change');
    
    <?php if (DEBUG_MODE): ?>
    // Debug event logging
    $('#matchFilters').on('submit reset', function(e) {
        console.log('Filter Event:', e.type, getFilters());
    });
    <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?> 