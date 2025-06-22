<?php
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Horoscope.php';
require_once __DIR__ . '/../models/Preference.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate input
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user1_id']) || !isset($data['user2_id'])) {
        throw new Exception('Both user IDs are required');
    }

    $user1Id = filter_var($data['user1_id'], FILTER_VALIDATE_INT);
    $user2Id = filter_var($data['user2_id'], FILTER_VALIDATE_INT);

    if (!$user1Id || !$user2Id) {
        throw new Exception('Invalid user IDs');
    }

    // Initialize models
    $userModel = new User($db);
    $horoscopeModel = new Horoscope($db);
    $preferenceModel = new Preference($db);

    // Get user details
    $user1 = $userModel->getUserById($user1Id);
    $user2 = $userModel->getUserById($user2Id);

    if (!$user1 || !$user2) {
        throw new Exception('One or both users not found');
    }

    // Get preferences
    $user1Prefs = $preferenceModel->getPreferences($user1Id);
    $user2Prefs = $preferenceModel->getPreferences($user2Id);

    // Get horoscopes
    $user1Horoscope = $horoscopeModel->getHoroscope($user1Id);
    $user2Horoscope = $horoscopeModel->getHoroscope($user2Id);

    // Initialize scoring system
    $scores = [
        'basic_compatibility' => 0,
        'preferences_match' => 0,
        'horoscope_compatibility' => 0,
        'caste_religion_match' => 0
    ];
    $explanations = [];

    // 1. Basic Compatibility Score (Age, Location) - 25%
    $scores['basic_compatibility'] = calculateBasicCompatibility($user1, $user2, $user1Prefs, $user2Prefs, $explanations);

    // 2. Preferences Match Score - 25%
    $scores['preferences_match'] = calculatePreferencesMatch($user1, $user2, $user1Prefs, $user2Prefs, $explanations);

    // 3. Horoscope Compatibility - 25%
    $scores['horoscope_compatibility'] = calculateHoroscopeMatch($user1Horoscope, $user2Horoscope, $explanations);

    // 4. Caste and Religion Match - 25%
    $scores['caste_religion_match'] = calculateCasteReligionMatch($user1, $user2, $user1Prefs, $user2Prefs, $explanations);

    // Calculate final score (weighted average)
    $finalScore = array_sum($scores) / count($scores);

    // Generate match message
    $matchMessage = generateMatchMessage($finalScore);

    // Prepare response
    $response = [
        'success' => true,
        'data' => [
            'match_score' => round($finalScore, 1),
            'message' => $matchMessage,
            'detailed_scores' => $scores,
            'explanations' => $explanations
        ]
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
exit;

/**
 * Calculate basic compatibility score based on age and location
 */
function calculateBasicCompatibility($user1, $user2, $prefs1, $prefs2, &$explanations) {
    $score = 0;
    $maxScore = 25;

    // Age compatibility (15 points)
    $age1 = calculateAge($user1['date_of_birth']);
    $age2 = calculateAge($user2['date_of_birth']);
    
    $ageDiff = abs($age1 - $age2);
    if ($ageDiff <= 5) {
        $score += 15;
        $explanations[] = "Age difference is ideal ($ageDiff years)";
    } elseif ($ageDiff <= 10) {
        $score += 10;
        $explanations[] = "Age difference is acceptable ($ageDiff years)";
    } else {
        $score += 5;
        $explanations[] = "Age difference is significant ($ageDiff years)";
    }

    // Location compatibility (10 points)
    if ($user1['district'] === $user2['district']) {
        $score += 10;
        $explanations[] = "Same district - perfect location match";
    } elseif ($user1['province'] === $user2['province']) {
        $score += 5;
        $explanations[] = "Same province - good location match";
    }

    return ($score / $maxScore) * 25; // Normalize to 25% weight
}

/**
 * Calculate preferences match score
 */
function calculatePreferencesMatch($user1, $user2, $prefs1, $prefs2, &$explanations) {
    $score = 0;
    $maxScore = 25;

    // Education level match (10 points)
    if (isEducationCompatible($user1['education'], $prefs2['preferred_education'])) {
        $score += 5;
        $explanations[] = "Education level matches preferences";
    }
    if (isEducationCompatible($user2['education'], $prefs1['preferred_education'])) {
        $score += 5;
        $explanations[] = "Education level matches partner's preferences";
    }

    // Occupation match (10 points)
    if (isOccupationCompatible($user1['occupation'], $prefs2['preferred_occupation'])) {
        $score += 5;
        $explanations[] = "Occupation matches preferences";
    }
    if (isOccupationCompatible($user2['occupation'], $prefs1['preferred_occupation'])) {
        $score += 5;
        $explanations[] = "Occupation matches partner's preferences";
    }

    // Lifestyle preferences (5 points)
    $lifestyleScore = compareLifestylePreferences($prefs1, $prefs2);
    $score += $lifestyleScore;
    $explanations[] = "Lifestyle compatibility: " . ($lifestyleScore * 20) . "%";

    return ($score / $maxScore) * 25; // Normalize to 25% weight
}

/**
 * Calculate horoscope compatibility score
 */
function calculateHoroscopeMatch($horoscope1, $horoscope2, &$explanations) {
    $score = 0;
    $maxScore = 25;

    // Nakshatra compatibility (10 points)
    $nakshatraScore = calculateNakshatraCompatibility($horoscope1['nakshatra'], $horoscope2['nakshatra']);
    $score += $nakshatraScore;
    $explanations[] = "Nakshatra compatibility: " . ($nakshatraScore * 10) . "%";

    // Gana match (5 points)
    if ($horoscope1['gana'] === $horoscope2['gana']) {
        $score += 5;
        $explanations[] = "Gana match is favorable";
    }

    // Planetary positions (10 points)
    $planetaryScore = comparePlanetaryPositions($horoscope1, $horoscope2);
    $score += $planetaryScore;
    $explanations[] = "Planetary positions compatibility: " . ($planetaryScore * 10) . "%";

    return ($score / $maxScore) * 25; // Normalize to 25% weight
}

/**
 * Calculate caste and religion match score
 */
function calculateCasteReligionMatch($user1, $user2, $prefs1, $prefs2, &$explanations) {
    $score = 0;
    $maxScore = 25;

    // Religion match (15 points)
    if ($user1['religion'] === $user2['religion']) {
        $score += 15;
        $explanations[] = "Same religion - perfect match";
    } elseif (isReligionCompatible($user1['religion'], $prefs2['preferred_religion']) &&
              isReligionCompatible($user2['religion'], $prefs1['preferred_religion'])) {
        $score += 10;
        $explanations[] = "Different religions but mutually acceptable";
    }

    // Caste match (10 points)
    if ($user1['caste'] === $user2['caste']) {
        $score += 10;
        $explanations[] = "Same caste - perfect match";
    } elseif (isCasteCompatible($user1['caste'], $prefs2['preferred_caste']) &&
              isCasteCompatible($user2['caste'], $prefs1['preferred_caste'])) {
        $score += 5;
        $explanations[] = "Different castes but mutually acceptable";
    }

    return ($score / $maxScore) * 25; // Normalize to 25% weight
}

/**
 * Generate match message based on final score
 */
function generateMatchMessage($score) {
    if ($score >= 90) {
        return "Exceptional Match! You have remarkable compatibility across all aspects.";
    } elseif ($score >= 80) {
        return "Excellent Match! You have strong compatibility in most areas.";
    } elseif ($score >= 70) {
        return "Very Good Match! You have good compatibility with some areas for growth.";
    } elseif ($score >= 60) {
        return "Good Match! You have decent compatibility with room for understanding.";
    } elseif ($score >= 50) {
        return "Fair Match! You have moderate compatibility with opportunities for compromise.";
    } else {
        return "Basic Match! While there are some matching aspects, you may face challenges in certain areas.";
    }
}

/**
 * Calculate age from date of birth
 */
function calculateAge($dob) {
    return date_diff(date_create($dob), date_create('today'))->y;
}

/**
 * Check if education levels are compatible
 */
function isEducationCompatible($education, $preferredEducation) {
    if (empty($preferredEducation)) {
        return true;
    }
    return in_array($education, (array)$preferredEducation);
}

/**
 * Check if occupations are compatible
 */
function isOccupationCompatible($occupation, $preferredOccupation) {
    if (empty($preferredOccupation)) {
        return true;
    }
    return in_array($occupation, (array)$preferredOccupation);
}

/**
 * Compare lifestyle preferences
 */
function compareLifestylePreferences($prefs1, $prefs2) {
    $score = 0;
    $totalFactors = 5;

    // Compare various lifestyle factors
    if ($prefs1['lifestyle_type'] === $prefs2['lifestyle_type']) $score++;
    if ($prefs1['food_preference'] === $prefs2['food_preference']) $score++;
    if ($prefs1['smoking_preference'] === $prefs2['smoking_preference']) $score++;
    if ($prefs1['drinking_preference'] === $prefs2['drinking_preference']) $score++;
    if ($prefs1['living_arrangement'] === $prefs2['living_arrangement']) $score++;

    return ($score / $totalFactors) * 5;
}

/**
 * Calculate Nakshatra compatibility
 */
function calculateNakshatraCompatibility($nakshatra1, $nakshatra2) {
    // Implement traditional Nakshatra matching logic
    // This is a simplified version - implement detailed logic based on your requirements
    $nakshatraGroups = [
        'compatible' => [
            ['Ashwini', 'Bharani', 'Krittika'],
            ['Rohini', 'Mrigashira', 'Ardra'],
            // Add more traditional groupings
        ]
    ];

    foreach ($nakshatraGroups['compatible'] as $group) {
        if (in_array($nakshatra1, $group) && in_array($nakshatra2, $group)) {
            return 10;
        }
    }

    return 5; // Default compatibility score
}

/**
 * Compare planetary positions
 */
function comparePlanetaryPositions($horoscope1, $horoscope2) {
    $score = 0;
    $totalFactors = 4;

    // Check major planetary aspects
    if (isPlanetaryAspectFavorable($horoscope1['sun_position'], $horoscope2['sun_position'])) $score++;
    if (isPlanetaryAspectFavorable($horoscope1['moon_position'], $horoscope2['moon_position'])) $score++;
    if (isPlanetaryAspectFavorable($horoscope1['venus_position'], $horoscope2['venus_position'])) $score++;
    if (isPlanetaryAspectFavorable($horoscope1['mars_position'], $horoscope2['mars_position'])) $score++;

    return ($score / $totalFactors) * 10;
}

/**
 * Check if planetary aspect is favorable
 */
function isPlanetaryAspectFavorable($position1, $position2) {
    // Implement traditional planetary aspect calculation
    // This is a simplified version - implement detailed logic based on your requirements
    $difference = abs($position1 - $position2);
    return ($difference === 0 || $difference === 120 || $difference === 60);
}

/**
 * Check if religions are compatible
 */
function isReligionCompatible($religion, $preferredReligion) {
    if (empty($preferredReligion)) {
        return true;
    }
    return in_array($religion, (array)$preferredReligion);
}

/**
 * Check if castes are compatible
 */
function isCasteCompatible($caste, $preferredCaste) {
    if (empty($preferredCaste)) {
        return true;
    }
    return in_array($caste, (array)$preferredCaste);
}
?> 