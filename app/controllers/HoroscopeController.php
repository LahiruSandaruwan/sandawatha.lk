<?php
namespace App\Controllers;

class HoroscopeController {
    // Zodiac signs data with date ranges and icons (using Font Awesome icons)
    private array $zodiacSigns = [
        'Aries' => [
            'start_date' => '03-21',
            'end_date' => '04-19',
            'icon' => 'fa-solid fa-ram',
            'element' => 'fire'
        ],
        'Taurus' => [
            'start_date' => '04-20',
            'end_date' => '05-20',
            'icon' => 'fa-solid fa-bull',
            'element' => 'earth'
        ],
        'Gemini' => [
            'start_date' => '05-21',
            'end_date' => '06-20',
            'icon' => 'fa-solid fa-masks-theater',
            'element' => 'air'
        ],
        'Cancer' => [
            'start_date' => '06-21',
            'end_date' => '07-22',
            'icon' => 'fa-solid fa-crab',
            'element' => 'water'
        ],
        'Leo' => [
            'start_date' => '07-23',
            'end_date' => '08-22',
            'icon' => 'fa-solid fa-lion',
            'element' => 'fire'
        ],
        'Virgo' => [
            'start_date' => '08-23',
            'end_date' => '09-22',
            'icon' => 'fa-solid fa-user',
            'element' => 'earth'
        ],
        'Libra' => [
            'start_date' => '09-23',
            'end_date' => '10-22',
            'icon' => 'fa-solid fa-scale-balanced',
            'element' => 'air'
        ],
        'Scorpio' => [
            'start_date' => '10-23',
            'end_date' => '11-21',
            'icon' => 'fa-solid fa-scorpion',
            'element' => 'water'
        ],
        'Sagittarius' => [
            'start_date' => '11-22',
            'end_date' => '12-21',
            'icon' => 'fa-solid fa-bow-arrow',
            'element' => 'fire'
        ],
        'Capricorn' => [
            'start_date' => '12-22',
            'end_date' => '01-19',
            'icon' => 'fa-solid fa-goat',
            'element' => 'earth'
        ],
        'Aquarius' => [
            'start_date' => '01-20',
            'end_date' => '02-18',
            'icon' => 'fa-solid fa-water',
            'element' => 'air'
        ],
        'Pisces' => [
            'start_date' => '02-19',
            'end_date' => '03-20',
            'icon' => 'fa-solid fa-fish',
            'element' => 'water'
        ]
    ];

    // Compatibility matrix based on elements and traditional astrology
    private array $compatibilityMatrix = [
        'fire' => [
            'fire' => 90,   // Very compatible
            'air' => 85,    // Highly compatible
            'earth' => 65,  // Moderately compatible
            'water' => 45   // Less compatible
        ],
        'earth' => [
            'fire' => 65,   // Moderately compatible
            'air' => 45,    // Less compatible
            'earth' => 95,  // Extremely compatible
            'water' => 90   // Very compatible
        ],
        'air' => [
            'fire' => 85,   // Highly compatible
            'air' => 90,    // Very compatible
            'earth' => 45,  // Less compatible
            'water' => 65   // Moderately compatible
        ],
        'water' => [
            'fire' => 45,   // Less compatible
            'air' => 65,    // Moderately compatible
            'earth' => 90,  // Very compatible
            'water' => 95   // Extremely compatible
        ]
    ];

    /**
     * Get zodiac sign details based on date of birth
     * @param string $dob Date of birth in Y-m-d format
     * @return array|null Zodiac sign details or null if invalid date
     */
    public function getZodiacSign(string $dob): ?array {
        try {
            // Validate date format
            $date = \DateTime::createFromFormat('Y-m-d', $dob);
            if (!$date || $date->format('Y-m-d') !== $dob) {
                return null;
            }

            $month = (int)$date->format('m');
            $day = (int)$date->format('d');
            
            // Find matching zodiac sign
            foreach ($this->zodiacSigns as $sign => $details) {
                $startDate = \DateTime::createFromFormat(
                    'Y-m-d', 
                    $date->format('Y') . '-' . $details['start_date']
                );
                $endDate = \DateTime::createFromFormat(
                    'Y-m-d', 
                    $date->format('Y') . '-' . $details['end_date']
                );

                // Handle year boundary for Capricorn
                if ($sign === 'Capricorn' && $month === 1) {
                    $startDate->modify('-1 year');
                }

                $checkDate = \DateTime::createFromFormat('Y-m-d', $dob);
                
                if (($checkDate >= $startDate && $checkDate <= $endDate) ||
                    ($sign === 'Capricorn' && $month === 12 && $day >= 22)) {
                    return [
                        'name' => $sign,
                        'icon' => $details['icon'],
                        'element' => $details['element'],
                        'start_date' => $details['start_date'],
                        'end_date' => $details['end_date']
                    ];
                }
            }

            return null;

        } catch (\Exception $e) {
            error_log("Error calculating zodiac sign: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get compatibility details between two zodiac signs
     * @param string $sign1 First zodiac sign name
     * @param string $sign2 Second zodiac sign name
     * @return array|null Compatibility details or null if invalid signs
     */
    public function getCompatibility(string $sign1, string $sign2): ?array {
        try {
            // Validate signs
            if (!isset($this->zodiacSigns[$sign1]) || !isset($this->zodiacSigns[$sign2])) {
                return null;
            }

            $element1 = $this->zodiacSigns[$sign1]['element'];
            $element2 = $this->zodiacSigns[$sign2]['element'];

            // Get base compatibility score from elements
            $baseScore = $this->compatibilityMatrix[$element1][$element2];

            // Additional compatibility factors
            $additionalScore = $this->calculateAdditionalCompatibility($sign1, $sign2);
            
            // Calculate final score
            $finalScore = min(100, max(0, $baseScore + $additionalScore));

            // Get compatibility description
            $description = $this->getCompatibilityDescription($finalScore);

            return [
                'sign1' => [
                    'name' => $sign1,
                    'icon' => $this->zodiacSigns[$sign1]['icon'],
                    'element' => $element1
                ],
                'sign2' => [
                    'name' => $sign2,
                    'icon' => $this->zodiacSigns[$sign2]['icon'],
                    'element' => $element2
                ],
                'score' => $finalScore,
                'description' => $description,
                'element_compatibility' => [
                    'element1' => $element1,
                    'element2' => $element2,
                    'base_score' => $baseScore
                ]
            ];

        } catch (\Exception $e) {
            error_log("Error calculating compatibility: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate additional compatibility factors beyond elements
     * @param string $sign1 First zodiac sign
     * @param string $sign2 Second zodiac sign
     * @return int Additional compatibility points (-10 to +10)
     */
    private function calculateAdditionalCompatibility(string $sign1, string $sign2): int {
        $score = 0;

        // Opposite signs (considered powerful connections)
        $opposites = [
            'Aries' => 'Libra',
            'Taurus' => 'Scorpio',
            'Gemini' => 'Sagittarius',
            'Cancer' => 'Capricorn',
            'Leo' => 'Aquarius',
            'Virgo' => 'Pisces'
        ];

        // Check for opposite signs (can be intense and transformative)
        if (($opposites[$sign1] === $sign2) || ($opposites[$sign2] === $sign1)) {
            $score += 5;
        }

        // Check for neighboring signs (can be challenging)
        $signList = array_keys($this->zodiacSigns);
        $pos1 = array_search($sign1, $signList);
        $pos2 = array_search($sign2, $signList);
        
        if (abs($pos1 - $pos2) === 1 || abs($pos1 - $pos2) === 11) {
            $score -= 5;
        }

        // Check for trine aspects (120 degrees, very harmonious)
        $distance = abs($pos1 - $pos2);
        if ($distance === 4 || $distance === 8) {
            $score += 10;
        }

        return $score;
    }

    /**
     * Get descriptive text for compatibility score
     * @param int $score Compatibility score
     * @return string Description of compatibility
     */
    private function getCompatibilityDescription(int $score): string {
        if ($score >= 90) {
            return "Exceptional match! These signs have a natural and powerful connection.";
        } elseif ($score >= 80) {
            return "Very compatible! These signs complement each other well.";
        } elseif ($score >= 70) {
            return "Good compatibility. This relationship has strong potential.";
        } elseif ($score >= 60) {
            return "Moderate compatibility. Success requires understanding and compromise.";
        } elseif ($score >= 50) {
            return "Average compatibility. Extra effort may be needed to maintain harmony.";
        } else {
            return "Challenging compatibility. This relationship requires work and patience.";
        }
    }
} 