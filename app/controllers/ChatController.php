<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Exception;

class ChatController {
    private $pdo;
    private $error;
    private $maxMessageLength = 1000;
    private $messagesPerPage = 50;
    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxImageSize = 5242880; // 5MB

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
     * Get chat contacts with latest message preview
     * @param int $userId Current user ID
     * @return array JSON response with contacts and their details
     */
    public function getChatContacts(int $userId): array {
        try {
            // Validate user
            if (!$this->isValidUser($userId)) {
                return $this->jsonResponse(false, "Invalid user");
            }

            // Get contacts with latest message
            $query = "
                WITH LatestMessages AS (
                    SELECT 
                        CASE 
                            WHEN from_user_id = :user_id THEN to_user_id
                            ELSE from_user_id
                        END as contact_id,
                        message,
                        created_at,
                        ROW_NUMBER() OVER (
                            PARTITION BY 
                                CASE 
                                    WHEN from_user_id = :user_id THEN to_user_id
                                    ELSE from_user_id
                                END 
                            ORDER BY created_at DESC
                        ) as rn
                    FROM messages
                    WHERE from_user_id = :user_id OR to_user_id = :user_id
                )
                SELECT 
                    u.id,
                    u.name,
                    u.profile_photo,
                    CASE 
                        WHEN u.last_active >= NOW() - INTERVAL 5 MINUTE THEN 1
                        ELSE 0
                    END as is_online,
                    lm.message as last_message,
                    lm.created_at as last_message_time,
                    (
                        SELECT COUNT(*)
                        FROM messages m
                        WHERE m.from_user_id = u.id
                        AND m.to_user_id = :user_id
                        AND m.is_read = 0
                    ) as unread_count
                FROM users u
                INNER JOIN LatestMessages lm ON lm.contact_id = u.id
                WHERE lm.rn = 1
                AND u.status = 'active'
                ORDER BY lm.created_at DESC";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['user_id' => $userId]);
            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format the response
            foreach ($contacts as &$contact) {
                $contact['is_online'] = (bool)$contact['is_online'];
                $contact['unread_count'] = (int)$contact['unread_count'];
                $contact['last_message'] = htmlspecialchars($contact['last_message']);
                $contact['last_message_time'] = $this->formatMessageTime($contact['last_message_time']);
                $contact['profile_photo'] = $contact['profile_photo'] ?? 'default-avatar.png';
            }

            return $this->jsonResponse(true, "Contacts retrieved successfully", [
                'contacts' => $contacts
            ]);

        } catch (PDOException $e) {
            error_log("Error getting chat contacts: " . $e->getMessage());
            return $this->jsonResponse(false, "Failed to get contacts");
        }
    }

    /**
     * Get messages between two users
     * @param int $userId Current user ID
     * @param int $toUserId Other user ID
     * @param int $page Page number (optional)
     * @return array JSON response with messages
     */
    public function getMessages(int $userId, int $toUserId, int $page = 1): array {
        try {
            // Validate users
            if (!$this->isValidUser($userId) || !$this->isValidUser($toUserId)) {
                return $this->jsonResponse(false, "Invalid user");
            }

            // Calculate offset
            $offset = ($page - 1) * $this->messagesPerPage;

            // Get messages
            $query = "
                SELECT 
                    m.id,
                    m.from_user_id,
                    m.to_user_id,
                    m.message,
                    m.message_type,
                    m.created_at,
                    m.is_read,
                    u.name as sender_name,
                    u.profile_photo as sender_photo
                FROM messages m
                INNER JOIN users u ON u.id = m.from_user_id
                WHERE (
                    (m.from_user_id = :user_id AND m.to_user_id = :to_user_id) OR
                    (m.from_user_id = :to_user_id AND m.to_user_id = :user_id)
                )
                ORDER BY m.created_at DESC
                LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':to_user_id', $toUserId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $this->messagesPerPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Mark messages as read
            $this->markMessagesAsRead($userId, $toUserId);

            // Format messages
            foreach ($messages as &$message) {
                $message['is_read'] = (bool)$message['is_read'];
                $message['is_sender'] = $message['from_user_id'] == $userId;
                $message['message'] = htmlspecialchars($message['message']);
                $message['time'] = $this->formatMessageTime($message['created_at']);
                $message['sender_photo'] = $message['sender_photo'] ?? 'default-avatar.png';
            }

            // Get total count for pagination
            $countStmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM messages 
                WHERE (from_user_id = :user_id AND to_user_id = :to_user_id) OR
                      (from_user_id = :to_user_id AND to_user_id = :user_id)
            ");
            $countStmt->execute([
                'user_id' => $userId,
                'to_user_id' => $toUserId
            ]);
            $totalMessages = $countStmt->fetchColumn();

            return $this->jsonResponse(true, "Messages retrieved successfully", [
                'messages' => array_reverse($messages), // Reverse for chronological order
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($totalMessages / $this->messagesPerPage),
                    'total_messages' => $totalMessages
                ]
            ]);

        } catch (PDOException $e) {
            error_log("Error getting messages: " . $e->getMessage());
            return $this->jsonResponse(false, "Failed to get messages");
        }
    }

    /**
     * Send a message
     * @param int $fromId Sender user ID
     * @param int $toId Recipient user ID
     * @param string $message Message content
     * @param string $type Message type (text/image)
     * @return array JSON response
     */
    public function sendMessage(int $fromId, int $toId, string $message, string $type = 'text'): array {
        try {
            // Validate users
            if (!$this->isValidUser($fromId) || !$this->isValidUser($toId)) {
                return $this->jsonResponse(false, "Invalid user");
            }

            // Validate message
            $message = trim($message);
            if (empty($message)) {
                return $this->jsonResponse(false, "Message cannot be empty");
            }

            if (strlen($message) > $this->maxMessageLength) {
                return $this->jsonResponse(false, "Message too long");
            }

            // Handle different message types
            if ($type === 'image') {
                if (!$this->isValidImage($message)) {
                    return $this->jsonResponse(false, "Invalid image");
                }
                $message = $this->processAndStoreImage($message);
            }

            // Insert message
            $stmt = $this->pdo->prepare("
                INSERT INTO messages (
                    from_user_id, 
                    to_user_id, 
                    message, 
                    message_type,
                    created_at
                ) VALUES (
                    :from_id,
                    :to_id,
                    :message,
                    :type,
                    NOW()
                )
            ");

            $stmt->execute([
                'from_id' => $fromId,
                'to_id' => $toId,
                'message' => $message,
                'type' => $type
            ]);

            $messageId = $this->pdo->lastInsertId();

            // Get message details for response
            $stmt = $this->pdo->prepare("
                SELECT 
                    m.*,
                    u.name as sender_name,
                    u.profile_photo as sender_photo
                FROM messages m
                INNER JOIN users u ON u.id = m.from_user_id
                WHERE m.id = :message_id
            ");
            $stmt->execute(['message_id' => $messageId]);
            $messageDetails = $stmt->fetch(PDO::FETCH_ASSOC);

            // Format message for response
            $response = [
                'id' => $messageDetails['id'],
                'message' => htmlspecialchars($messageDetails['message']),
                'type' => $messageDetails['message_type'],
                'time' => $this->formatMessageTime($messageDetails['created_at']),
                'is_sender' => true,
                'sender_name' => $messageDetails['sender_name'],
                'sender_photo' => $messageDetails['sender_photo'] ?? 'default-avatar.png'
            ];

            return $this->jsonResponse(true, "Message sent successfully", [
                'message' => $response
            ]);

        } catch (PDOException $e) {
            error_log("Error sending message: " . $e->getMessage());
            return $this->jsonResponse(false, "Failed to send message");
        }
    }

    /**
     * Mark messages as read
     * @param int $userId Current user ID
     * @param int $fromUserId Sender user ID
     * @return bool Success status
     */
    private function markMessagesAsRead(int $userId, int $fromUserId): bool {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE messages 
                SET is_read = 1 
                WHERE to_user_id = :user_id 
                AND from_user_id = :from_user_id 
                AND is_read = 0
            ");

            return $stmt->execute([
                'user_id' => $userId,
                'from_user_id' => $fromUserId
            ]);

        } catch (PDOException $e) {
            error_log("Error marking messages as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate user exists and is active
     * @param int $userId User ID to validate
     * @return bool Validation result
     */
    private function isValidUser(int $userId): bool {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM users 
                WHERE id = :user_id 
                AND status = 'active'
            ");
            $stmt->execute(['user_id' => $userId]);
            return (bool)$stmt->fetchColumn();

        } catch (PDOException $e) {
            error_log("Error validating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format message timestamp
     * @param string $timestamp MySQL timestamp
     * @return string Formatted time
     */
    private function formatMessageTime(string $timestamp): string {
        $messageTime = strtotime($timestamp);
        $now = time();
        $diff = $now - $messageTime;

        if ($diff < 60) {
            return "Just now";
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . "m ago";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . "h ago";
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . "d ago";
        } else {
            return date("M j, Y", $messageTime);
        }
    }

    /**
     * Validate image data
     * @param string $imageData Base64 encoded image data
     * @return bool Validation result
     */
    private function isValidImage(string $imageData): bool {
        try {
            if (strlen($imageData) > $this->maxImageSize) {
                return false;
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $tempFile = tempnam(sys_get_temp_dir(), 'chat_img_');
            file_put_contents($tempFile, base64_decode($imageData));
            $mimeType = $finfo->file($tempFile);
            unlink($tempFile);

            return in_array($mimeType, $this->allowedImageTypes);

        } catch (Exception $e) {
            error_log("Error validating image: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Process and store image
     * @param string $imageData Base64 encoded image data
     * @return string Image URL
     */
    private function processAndStoreImage(string $imageData): string {
        try {
            $imageData = base64_decode($imageData);
            $fileName = uniqid('chat_img_') . '.jpg';
            $filePath = __DIR__ . '/../../public/uploads/chat/' . $fileName;
            
            if (!is_dir(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            file_put_contents($filePath, $imageData);
            return '/uploads/chat/' . $fileName;

        } catch (Exception $e) {
            error_log("Error processing image: " . $e->getMessage());
            throw new Exception("Failed to process image");
        }
    }

    /**
     * Format JSON response
     * @param bool $success Success status
     * @param string $message Response message
     * @param array $data Optional data
     * @return array Response array
     */
    private function jsonResponse(bool $success, string $message, array $data = []): array {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];
    }
} 