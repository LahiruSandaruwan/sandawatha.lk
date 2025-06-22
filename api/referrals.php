<?php
header('Content-Type: application/json');

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Referral.php';
require_once __DIR__ . '/../models/Reward.php';
require_once __DIR__ . '/../models/Notification.php';

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
    
    if (!isset($data['new_user_id'], $data['referral_code'])) {
        throw new Exception('Missing required fields');
    }

    // Validate user ID
    $newUserId = filter_var($data['new_user_id'], FILTER_VALIDATE_INT);
    if (!$newUserId) {
        throw new Exception('Invalid user ID');
    }

    // Clean referral code
    $referralCode = trim($data['referral_code']);
    if (empty($referralCode)) {
        throw new Exception('Invalid referral code');
    }

    // Initialize models
    $userModel = new User($db);
    $referralModel = new Referral($db);
    $rewardModel = new Reward($db);
    $notificationModel = new Notification($db);

    // Get users
    $newUser = $userModel->getUserById($newUserId);
    $referrer = $userModel->getUserByReferralCode($referralCode);

    if (!$newUser) {
        throw new Exception('New user not found');
    }

    if (!$referrer) {
        throw new Exception('Invalid referral code');
    }

    // Validate referral
    if ($newUser['id'] === $referrer['id']) {
        throw new Exception('Cannot refer yourself');
    }

    // Check if user was already referred
    if ($referralModel->wasUserReferred($newUser['id'])) {
        throw new Exception('User was already referred');
    }

    // Check referrer's monthly limit
    $monthlyReferralCount = $referralModel->getMonthlyReferralCount($referrer['id']);
    $maxMonthlyReferrals = 10; // Configure as needed

    if ($monthlyReferralCount >= $maxMonthlyReferrals) {
        throw new Exception('Referrer has reached monthly limit');
    }

    // Configure rewards
    $rewards = [
        'referrer' => [
            'coins' => 100,
            'premium_days' => 1
        ],
        'referred' => [
            'coins' => 50,
            'premium_days' => 1
        ]
    ];

    // Start transaction
    $db->beginTransaction();

    try {
        // Create referral record
        $referralData = [
            'referrer_id' => $referrer['id'],
            'referred_id' => $newUser['id'],
            'status' => 'completed',
            'referred_at' => date('Y-m-d H:i:s')
        ];

        $referralId = $referralModel->createReferral($referralData);

        // Add rewards for referrer
        $referrerRewardData = [
            'user_id' => $referrer['id'],
            'type' => 'referral_bonus',
            'coins' => $rewards['referrer']['coins'],
            'premium_days' => $rewards['referrer']['premium_days'],
            'reference_id' => $referralId,
            'awarded_at' => date('Y-m-d H:i:s')
        ];

        $rewardModel->addReward($referrerRewardData);
        $userModel->updateUserRewards(
            $referrer['id'], 
            $rewards['referrer']['coins'], 
            $rewards['referrer']['premium_days']
        );

        // Add rewards for referred user
        $referredRewardData = [
            'user_id' => $newUser['id'],
            'type' => 'referral_bonus',
            'coins' => $rewards['referred']['coins'],
            'premium_days' => $rewards['referred']['premium_days'],
            'reference_id' => $referralId,
            'awarded_at' => date('Y-m-d H:i:s')
        ];

        $rewardModel->addReward($referredRewardData);
        $userModel->updateUserRewards(
            $newUser['id'], 
            $rewards['referred']['coins'], 
            $rewards['referred']['premium_days']
        );

        // Create notifications
        // For referrer
        $referrerNotification = [
            'user_id' => $referrer['id'],
            'type' => 'referral_success',
            'reference_id' => $referralId,
            'message' => sprintf(
                'You earned %d coins and %d premium day(s) for referring %s!',
                $rewards['referrer']['coins'],
                $rewards['referrer']['premium_days'],
                $newUser['name']
            ),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $notificationModel->createNotification($referrerNotification);

        // For referred user
        $referredNotification = [
            'user_id' => $newUser['id'],
            'type' => 'referral_bonus',
            'reference_id' => $referralId,
            'message' => sprintf(
                'Welcome bonus: %d coins and %d premium day(s) for joining via referral!',
                $rewards['referred']['coins'],
                $rewards['referred']['premium_days']
            ),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $notificationModel->createNotification($referredNotification);

        // Commit transaction
        $db->commit();

        // Send email notifications
        sendReferralEmails($referrer, $newUser, $rewards);

        // Prepare success response
        $response = [
            'success' => true,
            'message' => 'Referral processed successfully!',
            'data' => [
                'referral_id' => $referralId,
                'referrer' => [
                    'id' => $referrer['id'],
                    'name' => $referrer['name'],
                    'rewards' => $rewards['referrer']
                ],
                'referred' => [
                    'id' => $newUser['id'],
                    'name' => $newUser['name'],
                    'rewards' => $rewards['referred']
                ]
            ]
        ];

    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
exit;

/**
 * Send email notifications to both users
 */
function sendReferralEmails($referrer, $referred, $rewards) {
    // Email to referrer
    $referrerSubject = "Referral Bonus Earned!";
    $referrerBody = "Dear {$referrer['name']},\n\n";
    $referrerBody .= "Great news! {$referred['name']} has joined Sandawatha using your referral code.\n\n";
    $referrerBody .= "You've earned:\n";
    $referrerBody .= "- {$rewards['referrer']['coins']} coins\n";
    $referrerBody .= "- {$rewards['referrer']['premium_days']} premium day(s)\n\n";
    $referrerBody .= "Keep referring friends to earn more rewards!\n\n";
    $referrerBody .= "Best regards,\nSandawatha Team";

    // Email to referred user
    $referredSubject = "Welcome Bonus Activated!";
    $referredBody = "Dear {$referred['name']},\n\n";
    $referredBody .= "Welcome to Sandawatha! Your account has been created with a referral bonus.\n\n";
    $referredBody .= "You've received:\n";
    $referredBody .= "- {$rewards['referred']['coins']} coins\n";
    $referredBody .= "- {$rewards['referred']['premium_days']} premium day(s)\n\n";
    $referredBody .= "Start exploring Sandawatha and find your perfect match!\n\n";
    $referredBody .= "Best regards,\nSandawatha Team";

    $headers = [
        'From: no-reply@sandawatha.lk',
        'Reply-To: no-reply@sandawatha.lk',
        'X-Mailer: PHP/' . phpversion()
    ];

    // Send emails silently
    @mail($referrer['email'], $referrerSubject, $referrerBody, implode("\r\n", $headers));
    @mail($referred['email'], $referredSubject, $referredBody, implode("\r\n", $headers));
}

/**
 * Suggested database table structures:
 *
CREATE TABLE referrals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    referrer_id INT NOT NULL,
    referred_id INT NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'completed',
    referred_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id),
    FOREIGN KEY (referred_id) REFERENCES users(id),
    UNIQUE KEY unique_referred (referred_id),
    INDEX idx_referrer (referrer_id),
    INDEX idx_referred_at (referred_at)
);

CREATE TABLE rewards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('referral_bonus', 'signup_bonus', 'activity_bonus') NOT NULL,
    coins INT NOT NULL DEFAULT 0,
    premium_days INT NOT NULL DEFAULT 0,
    reference_id INT,
    awarded_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_type (user_id, type),
    INDEX idx_awarded_at (awarded_at)
);

-- Add these columns to users table if not exists:
ALTER TABLE users
ADD COLUMN coins INT NOT NULL DEFAULT 0,
ADD COLUMN premium_until DATE NULL,
ADD COLUMN referral_code VARCHAR(10) UNIQUE NOT NULL;
 */
?> 