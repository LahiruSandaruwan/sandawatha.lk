<?php
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Gift.php';
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
    
    if (!isset($data['from_id'], $data['to_id'], $data['gift_type'])) {
        throw new Exception('Missing required fields');
    }

    // Validate user IDs
    $fromId = filter_var($data['from_id'], FILTER_VALIDATE_INT);
    $toId = filter_var($data['to_id'], FILTER_VALIDATE_INT);
    
    if (!$fromId || !$toId) {
        throw new Exception('Invalid user IDs');
    }

    // Verify sender is current user
    if ($fromId !== $_SESSION['user_id']) {
        throw new Exception('Unauthorized sender');
    }

    // Clean and validate message
    $message = isset($data['message']) ? trim($data['message']) : '';
    if (strlen($message) > 200) { // Limit message length
        throw new Exception('Message is too long (maximum 200 characters)');
    }

    // Initialize models
    $userModel = new User($db);
    $giftModel = new Gift($db);
    $notificationModel = new Notification($db);

    // Get users
    $sender = $userModel->getUserById($fromId);
    $recipient = $userModel->getUserById($toId);

    if (!$sender || !$recipient) {
        throw new Exception('One or both users not found');
    }

    // Validate gift type
    $validGiftTypes = [
        'heart',
        'flower',
        'ring',
        'teddy',
        'chocolate',
        'cake'
    ];

    if (!in_array($data['gift_type'], $validGiftTypes)) {
        throw new Exception('Invalid gift type');
    }

    // Check if recipient accepts gifts
    if (!$recipient['accepts_gifts']) {
        throw new Exception('Recipient does not accept gifts');
    }

    // Check daily gift limit for sender
    $dailyGiftCount = $giftModel->getDailyGiftCount($fromId);
    $maxDailyGifts = 10; // Configure as needed

    if ($dailyGiftCount >= $maxDailyGifts) {
        throw new Exception('Daily gift limit reached');
    }

    // Check if sender has already sent a gift to this recipient today
    if ($giftModel->hasGiftedToday($fromId, $toId)) {
        throw new Exception('You have already sent a gift to this user today');
    }

    // Start transaction
    $db->beginTransaction();

    try {
        // Create gift record
        $giftData = [
            'from_user_id' => $fromId,
            'to_user_id' => $toId,
            'gift_type' => $data['gift_type'],
            'message' => $message,
            'sent_at' => date('Y-m-d H:i:s'),
            'status' => 'sent'
        ];

        $giftId = $giftModel->createGift($giftData);

        // Create notification for recipient
        $notificationData = [
            'user_id' => $toId,
            'type' => 'gift_received',
            'reference_id' => $giftId,
            'message' => sprintf(
                '%s sent you a %s%s',
                $sender['name'],
                $data['gift_type'],
                $message ? ' with a message' : ''
            ),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $notificationModel->createNotification($notificationData);

        // Commit transaction
        $db->commit();

        // Get gift details for response
        $gift = $giftModel->getGiftById($giftId);

        // Prepare success response
        $response = [
            'success' => true,
            'message' => 'Gift sent successfully!',
            'data' => [
                'gift_id' => $giftId,
                'gift' => $gift,
                'sender' => [
                    'id' => $sender['id'],
                    'name' => $sender['name']
                ],
                'recipient' => [
                    'id' => $recipient['id'],
                    'name' => $recipient['name']
                ]
            ]
        ];

        // Send email notification to recipient
        sendGiftNotification($recipient, $sender, $data['gift_type'], $message);

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
 * Send email notification to gift recipient
 */
function sendGiftNotification($recipient, $sender, $giftType, $message) {
    $subject = "You received a gift on Sandawatha!";
    
    $emailBody = "Dear {$recipient['name']},\n\n";
    $emailBody .= "{$sender['name']} has sent you a {$giftType}!\n\n";
    
    if ($message) {
        $emailBody .= "Message: {$message}\n\n";
    }
    
    $emailBody .= "Log in to your account to view the gift and respond.\n\n";
    $emailBody .= "Best regards,\nSandawatha Team";
    
    $headers = [
        'From: no-reply@sandawatha.lk',
        'Reply-To: no-reply@sandawatha.lk',
        'X-Mailer: PHP/' . phpversion()
    ];

    // Send email silently (don't throw exception if fails)
    @mail($recipient['email'], $subject, $emailBody, implode("\r\n", $headers));
}

/**
 * Suggested database table structure:
 *
CREATE TABLE gifts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    from_user_id INT NOT NULL,
    to_user_id INT NOT NULL,
    gift_type VARCHAR(50) NOT NULL,
    message VARCHAR(200),
    sent_at DATETIME NOT NULL,
    status ENUM('sent', 'received', 'rejected') NOT NULL DEFAULT 'sent',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (from_user_id) REFERENCES users(id),
    FOREIGN KEY (to_user_id) REFERENCES users(id),
    INDEX idx_from_user (from_user_id),
    INDEX idx_to_user (to_user_id),
    INDEX idx_sent_at (sent_at)
);
 */
?> 