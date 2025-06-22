<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Exception;

class MatchController {
    private $pdo;
    private $error;
    private $aiEndpoint = '/api/match-ai.php';
    private $maxSuggestions = 6;
    private $defaultSearchLimit = 20;
    private $minMatchScore = 50; // Minimum match score (0-100)

    /**
     * Constructor - initialize database connection
     */
    public function __construct() {
        try {
            require_once __DIR__ . '/../../config/database.php';
            $this->pdo = $pdo;
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    /**
     * Get the last error message
     * @return string
     */
    public function getError(): string {
        return $this->error ?? '';
    }

    /**
     * Search for matches based on user preferences and filters
     * @param int $userId User ID
     * @param array $filters Optional additional filters
     * @return array|false Array of matches or false on failure
     */
    public function searchMatches(int $userId, array $filters = []): array|false {
        try {
            // Get user's profile and preferences
            $stmt = $this->pdo->prepare("
                SELECT 
                    gender, age, religion, caste, district,
                    preferred_age_min, preferred_age_max,
                    preferred_caste, preferred_districts
                FROM users 
                WHERE id = :id 
                AND status = 'active'
                LIMIT 1
            ");
            
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->error = "User not found";
                return false;
            }

            // Build base query
            $query = "
                SELECT 
                    u.id,
                    u.name,
                    u.gender,
                    u.religion,
                    u.caste,
                    u.district,
                    u.education,
                    u.profession,
                    u.profile_photo,
                    TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) as age
                FROM users u
                WHERE u.id != :user_id
                AND u.status = 'active'
                AND u.profile_photo IS NOT NULL
            ";
            
            $params = ['user_id' => $userId];

            // Add gender preference
            $preferredGender = $user['gender'] === 'male' ? 'female' : 'male';
            $query .= " AND u.gender = :preferred_gender";
            $params['preferred_gender'] = $preferredGender;

            // Add age preference
            if ($user['preferred_age_min']) {
                $query .= " AND TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) >= :min_age";
                $params['min_age'] = $user['preferred_age_min'];
            }
            if ($user['preferred_age_max']) {
                $query .= " AND TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) <= :max_age";
                $params['max_age'] = $user['preferred_age_max'];
            }

            // Add religion filter
            if (!empty($filters['religion'])) {
                $query .= " AND u.religion = :religion";
                $params['religion'] = $filters['religion'];
            }

            // Add caste filter
            if (!empty($user['preferred_caste'])) {
                $query .= " AND u.caste = :caste";
                $params['caste'] = $user['preferred_caste'];
            }

            // Add district filter
            if (!empty($user['preferred_districts'])) {
                $districts = explode(',', $user['preferred_districts']);
                $query .= " AND u.district IN (" . implode(',', array_fill(0, count($districts), '?')) . ")";
                foreach ($districts as $district) {
                    $params[] = $district;
                }
            }

            // Add education filter
            if (!empty($filters['education'])) {
                $query .= " AND u.education = :education";
                $params['education'] = $filters['education'];
            }

            // Add custom filters
            if (!empty($filters['age_min'])) {
                $query .= " AND TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) >= :filter_age_min";
                $params['filter_age_min'] = $filters['age_min'];
            }
            if (!empty($filters['age_max'])) {
                $query .= " AND TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) <= :filter_age_max";
                $params['filter_age_max'] = $filters['age_max'];
            }

            // Add limit
            $limit = isset($filters['limit']) ? min((int)$filters['limit'], 50) : $this->defaultSearchLimit;
            $query .= " LIMIT :limit";
            $params['limit'] = $limit;

            // Execute query
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue(is_int($key) ? $key + 1 : $key, $value);
            }
            $stmt->execute();
            $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($matches)) {
                return [];
            }

            // Get AI match scores
            $matches = $this->calculateMatchScores($userId, $matches);

            // Sort by match score
            usort($matches, fn($a, $b) => $b['match_score'] <=> $a['match_score']);

            // Filter out low scores
            $matches = array_filter($matches, fn($match) => $match['match_score'] >= $this->minMatchScore);

            return $matches;

        } catch (PDOException $e) {
            error_log("Match search error: " . $e->getMessage());
            $this->error = "Failed to search matches";
            return false;
        }
    }

    /**
     * Get match suggestions for user
     * @param int $userId User ID
     * @return array|false Array of suggested matches or false on failure
     */
    public function getMatchSuggestions(int $userId): array|false {
        try {
            // Get user's interaction history
            $stmt = $this->pdo->prepare("
                SELECT 
                    matched_user_id,
                    interaction_type,
                    created_at
                FROM user_interactions
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT 100
            ");
            
            $stmt->execute(['user_id' => $userId]);
            $interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get excluded user IDs (already interacted with)
            $excludedIds = array_column($interactions, 'matched_user_id');
            $excludedIds[] = $userId;

            // Get user's preferences
            $stmt = $this->pdo->prepare("
                SELECT 
                    gender, religion, caste, district,
                    preferred_age_min, preferred_age_max,
                    preferred_caste, preferred_districts,
                    TIMESTAMPDIFF(YEAR, dob, CURDATE()) as age
                FROM users 
                WHERE id = :id 
                AND status = 'active'
                LIMIT 1
            ");
            
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->error = "User not found";
                return false;
            }

            // Build suggestion query with smart criteria
            $query = "
                SELECT 
                    u.id,
                    u.name,
                    u.gender,
                    u.religion,
                    u.caste,
                    u.district,
                    u.education,
                    u.profession,
                    u.profile_photo,
                    TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) as age,
                    (
                        CASE 
                            WHEN u.religion = :religion THEN 20 
                            ELSE 0 
                        END +
                        CASE 
                            WHEN u.district = :district THEN 15 
                            ELSE 0 
                        END +
                        CASE 
                            WHEN u.caste = :caste THEN 10 
                            ELSE 0 
                        END +
                        CASE 
                            WHEN ABS(TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) - :user_age) <= 5 THEN 15
                            ELSE 0 
                        END
                    ) as initial_score
                FROM users u
                WHERE u.id NOT IN (" . implode(',', array_fill(0, count($excludedIds), '?')) . ")
                AND u.status = 'active'
                AND u.profile_photo IS NOT NULL
                AND u.gender = :preferred_gender
                HAVING initial_score > 20
                ORDER BY initial_score DESC, RAND()
                LIMIT :limit
            ";

            // Prepare parameters
            $params = $excludedIds;
            $params[] = $user['religion'];
            $params[] = $user['district'];
            $params[] = $user['caste'];
            $params[] = $user['age'];
            $params[] = $user['gender'] === 'male' ? 'female' : 'male';
            $params[] = $this->maxSuggestions;

            // Execute query
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key + 1, $value);
            }
            $stmt->execute();
            $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($suggestions)) {
                return [];
            }

            // Get AI match scores
            $suggestions = $this->calculateMatchScores($userId, $suggestions);

            // Sort by match score
            usort($suggestions, fn($a, $b) => $b['match_score'] <=> $a['match_score']);

            return array_slice($suggestions, 0, $this->maxSuggestions);

        } catch (PDOException $e) {
            error_log("Match suggestions error: " . $e->getMessage());
            $this->error = "Failed to get match suggestions";
            return false;
        }
    }

    /**
     * Calculate match scores using AI endpoint
     * @param int $userId User ID
     * @param array $matches Array of potential matches
     * @return array Matches with scores
     */
    private function calculateMatchScores(int $userId, array $matches): array {
        try {
            // Prepare data for AI scoring
            $scoringData = [
                'user_id' => $userId,
                'matches' => array_map(fn($match) => [
                    'id' => $match['id'],
                    'age' => $match['age'],
                    'religion' => $match['religion'],
                    'caste' => $match['caste'],
                    'district' => $match['district'],
                    'education' => $match['education'],
                    'profession' => $match['profession']
                ], $matches)
            ];

            // Call AI endpoint
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $_SERVER['HTTP_HOST'] . $this->aiEndpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($scoringData),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-API-Key: ' . $_ENV['AI_API_KEY'] ?? ''
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $scores = json_decode($response, true);
                
                // Merge scores with matches
                foreach ($matches as &$match) {
                    $match['match_score'] = $scores[$match['id']] ?? 0;
                }
                
                return $matches;
            }

            // Fallback to basic scoring if AI fails
            foreach ($matches as &$match) {
                $match['match_score'] = $this->calculateBasicScore($match);
            }

            return $matches;

        } catch (Exception $e) {
            error_log("Match score calculation error: " . $e->getMessage());
            
            // Fallback to basic scoring
            foreach ($matches as &$match) {
                $match['match_score'] = $this->calculateBasicScore($match);
            }

            return $matches;
        }
    }

    /**
     * Calculate basic match score without AI
     * @param array $match Match data
     * @return int Score between 0-100
     */
    private function calculateBasicScore(array $match): int {
        $score = 60; // Base score

        // Add points based on profile completeness
        $requiredFields = ['name', 'age', 'religion', 'caste', 'district', 'education', 'profession', 'profile_photo'];
        $completeness = count(array_filter($match, fn($value) => !empty($value) && in_array(key($match), $requiredFields)));
        $score += ($completeness / count($requiredFields)) * 20;

        // Add points for verified status (if implemented)
        if (!empty($match['is_verified'])) {
            $score += 10;
        }

        // Cap score at 100
        return min(100, $score);
    }

    /**
     * Record user interaction with a match
     * @param int $userId User ID
     * @param int $matchedUserId Matched user ID
     * @param string $type Interaction type (view, like, message)
     * @return bool
     */
    public function recordInteraction(int $userId, int $matchedUserId, string $type): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO user_interactions (
                    user_id, matched_user_id, interaction_type, created_at
                ) VALUES (
                    :user_id, :matched_user_id, :type, NOW()
                )
            ");

            return $stmt->execute([
                'user_id' => $userId,
                'matched_user_id' => $matchedUserId,
                'type' => $type
            ]);

        } catch (PDOException $e) {
            error_log("Interaction recording error: " . $e->getMessage());
            $this->error = "Failed to record interaction";
            return false;
        }
    }
} 