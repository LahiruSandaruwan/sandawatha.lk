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
require_once __DIR__ . '/../models/Verification.php';

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

    // Initialize models
    $userModel = new User($db);
    $verificationModel = new Verification($db);

    // Get current user
    $userId = $_SESSION['user_id'];
    $user = $userModel->getUserById($userId);

    if (!$user) {
        throw new Exception('User not found');
    }

    // Check if user is already verified or pending verification
    if ($user['verification_status'] === 'verified') {
        throw new Exception('User is already verified');
    }

    if ($user['verification_status'] === 'pending') {
        throw new Exception('Verification is already pending review');
    }

    // Validate file uploads
    if (!isset($_FILES['front_id']) || !isset($_FILES['back_id'])) {
        throw new Exception('Both front and back ID images are required');
    }

    // Configuration
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $uploadPath = __DIR__ . '/../storage/ids/';
    $uniquePrefix = uniqid($userId . '_');

    // Create upload directory if it doesn't exist
    if (!file_exists($uploadPath)) {
        if (!mkdir($uploadPath, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Function to validate and upload file
    function validateAndUploadFile($file, $side, $uploadPath, $uniquePrefix, $allowedTypes, $maxFileSize) {
        // Check file size
        if ($file['size'] > $maxFileSize) {
            throw new Exception("$side ID image exceeds maximum file size (5MB)");
        }

        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception("$side ID image must be JPEG or PNG");
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $uniquePrefix . $side . '.' . $extension;
        $targetPath = $uploadPath . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Failed to upload $side ID image");
        }

        // Set proper permissions
        chmod($targetPath, 0644);

        return $filename;
    }

    // Upload front ID
    $frontIdFilename = validateAndUploadFile(
        $_FILES['front_id'],
        'front',
        $uploadPath,
        $uniquePrefix,
        $allowedTypes,
        $maxFileSize
    );

    // Upload back ID
    $backIdFilename = validateAndUploadFile(
        $_FILES['back_id'],
        'back',
        $uploadPath,
        $uniquePrefix,
        $allowedTypes,
        $maxFileSize
    );

    // Start transaction
    $db->beginTransaction();

    try {
        // Create verification record
        $verificationData = [
            'user_id' => $userId,
            'front_id_path' => $frontIdFilename,
            'back_id_path' => $backIdFilename,
            'submission_date' => date('Y-m-d H:i:s'),
            'status' => 'pending',
            'notes' => null
        ];

        $verificationId = $verificationModel->createVerification($verificationData);

        // Update user verification status
        $userModel->updateVerificationStatus($userId, 'pending');

        // Commit transaction
        $db->commit();

        // Send email notification to admin
        sendAdminNotification($user['email']);

        // Prepare success response
        $response = [
            'success' => true,
            'message' => 'ID verification submitted successfully. We will review your documents shortly.',
            'data' => [
                'verification_id' => $verificationId,
                'status' => 'pending'
            ]
        ];

    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();

        // Delete uploaded files if they exist
        if (isset($frontIdFilename) && file_exists($uploadPath . $frontIdFilename)) {
            unlink($uploadPath . $frontIdFilename);
        }
        if (isset($backIdFilename) && file_exists($uploadPath . $backIdFilename)) {
            unlink($uploadPath . $backIdFilename);
        }

        throw $e;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
exit;

/**
 * Send email notification to admin
 */
function sendAdminNotification($userEmail) {
    // Get admin email from config
    $adminEmail = getenv('ADMIN_EMAIL') ?: 'admin@sandawatha.lk';
    
    $subject = 'New ID Verification Request';
    $message = "A new ID verification request has been submitted by user: $userEmail\n\n";
    $message .= "Please review the verification in the admin dashboard.";
    
    $headers = [
        'From: no-reply@sandawatha.lk',
        'Reply-To: no-reply@sandawatha.lk',
        'X-Mailer: PHP/' . phpversion()
    ];

    // Send email silently (don't throw exception if fails)
    @mail($adminEmail, $subject, $message, implode("\r\n", $headers));
}
?> 