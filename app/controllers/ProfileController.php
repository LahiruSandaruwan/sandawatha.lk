<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Exception;

class ProfileController {
    private $pdo;
    private $error;
    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
    private $maxFileSize = 5242880; // 5MB in bytes

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
     * Get user profile by ID
     * @param int $id User ID
     * @return array|false Profile data or false on failure
     */
    public function getUserProfile(int $id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    u.id,
                    u.name,
                    u.email,
                    u.phone,
                    u.dob,
                    u.gender,
                    u.religion,
                    u.caste,
                    u.district,
                    u.education,
                    u.profession,
                    u.preferred_age_min,
                    u.preferred_age_max,
                    u.preferred_caste,
                    u.preferred_districts,
                    u.profile_photo,
                    u.status,
                    u.created_at,
                    u.last_login,
                    TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) as age
                FROM users u
                WHERE u.id = :id
                AND u.status = 'active'
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$profile) {
                $this->error = "Profile not found";
                return false;
            }

            // Convert preferred districts from comma-separated to array
            if ($profile['preferred_districts']) {
                $profile['preferred_districts'] = explode(',', $profile['preferred_districts']);
            } else {
                $profile['preferred_districts'] = [];
            }

            return $profile;

        } catch (PDOException $e) {
            error_log("Profile fetch error: " . $e->getMessage());
            $this->error = "Failed to fetch profile";
            return false;
        }
    }

    /**
     * Update user profile
     * @param int $id User ID
     * @param array $data Profile data
     * @return bool
     */
    public function updateUserProfile(int $id, array $data): bool {
        try {
            // Validate email format if provided
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->error = "Invalid email format";
                return false;
            }

            // Validate phone format if provided
            if (!empty($data['phone']) && !preg_match('/^(?:\+94|0)?[0-9]{9,10}$/', $data['phone'])) {
                $this->error = "Invalid phone number format";
                return false;
            }

            // Check email/phone uniqueness if changed
            if (!empty($data['email']) || !empty($data['phone'])) {
                $stmt = $this->pdo->prepare("
                    SELECT id FROM users 
                    WHERE (email = :email OR phone = :phone)
                    AND id != :id 
                    LIMIT 1
                ");
                
                $stmt->execute([
                    'email' => $data['email'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'id' => $id
                ]);

                if ($stmt->fetch()) {
                    $this->error = "Email or phone number already registered";
                    return false;
                }
            }

            // Convert preferred districts array to string if provided
            if (isset($data['preferred_districts']) && is_array($data['preferred_districts'])) {
                $data['preferred_districts'] = implode(',', $data['preferred_districts']);
            }

            // Begin transaction
            $this->pdo->beginTransaction();

            // Build update query dynamically based on provided data
            $updateFields = [];
            $params = ['id' => $id];

            $allowedFields = [
                'name', 'email', 'phone', 'religion', 'caste', 'district',
                'education', 'profession', 'preferred_age_min', 'preferred_age_max',
                'preferred_caste', 'preferred_districts'
            ];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "{$field} = :{$field}";
                    $params[$field] = $data[$field];
                }
            }

            if (empty($updateFields)) {
                $this->error = "No fields to update";
                return false;
            }

            $updateFields[] = "updated_at = NOW()";
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            // Commit transaction
            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {
            // Rollback transaction on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            error_log("Profile update error: " . $e->getMessage());
            $this->error = "Failed to update profile";
            return false;
        }
    }

    /**
     * Upload profile photo
     * @param int $userId User ID
     * @return string|false New filename on success, false on failure
     */
    public function uploadProfilePhoto(int $userId) {
        try {
            if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                $this->error = "No file uploaded or upload failed";
                return false;
            }

            $file = $_FILES['photo'];

            // Validate file size
            if ($file['size'] > $this->maxFileSize) {
                $this->error = "File size exceeds 5MB limit";
                return false;
            }

            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $this->allowedImageTypes)) {
                $this->error = "Invalid file type. Only JPG, PNG and WebP images are allowed";
                return false;
            }

            // Generate unique filename
            $extension = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                default => throw new Exception("Invalid image type")
            };

            $filename = uniqid() . '.' . $extension;
            $uploadPath = __DIR__ . '/../../public/uploads/profile_photos/' . $filename;

            // Create directory if it doesn't exist
            $uploadDir = dirname($uploadPath);
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Begin transaction
            $this->pdo->beginTransaction();

            // Get old photo filename
            $stmt = $this->pdo->prepare("SELECT profile_photo FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            $oldPhoto = $stmt->fetchColumn();

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception("Failed to move uploaded file");
            }

            // Update database with new filename
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET profile_photo = :photo, updated_at = NOW() 
                WHERE id = :id
            ");
            
            $stmt->execute([
                'photo' => $filename,
                'id' => $userId
            ]);

            // Delete old photo if exists
            if ($oldPhoto && file_exists($uploadDir . '/' . $oldPhoto)) {
                unlink($uploadDir . '/' . $oldPhoto);
            }

            // Commit transaction
            $this->pdo->commit();

            return $filename;

        } catch (Exception $e) {
            // Rollback transaction on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            error_log("Photo upload error: " . $e->getMessage());
            $this->error = "Failed to upload photo";
            return false;
        }
    }

    /**
     * Get lookup data for dropdowns
     * @param string $type Type of lookup data (religion, district)
     * @return array
     */
    public function getLookupData(string $type): array {
        $data = match ($type) {
            'religion' => [
                'Buddhist',
                'Hindu',
                'Muslim',
                'Christian',
                'Roman Catholic',
                'Other'
            ],
            'district' => [
                'Ampara',
                'Anuradhapura',
                'Badulla',
                'Batticaloa',
                'Colombo',
                'Galle',
                'Gampaha',
                'Hambantota',
                'Jaffna',
                'Kalutara',
                'Kandy',
                'Kegalle',
                'Kilinochchi',
                'Kurunegala',
                'Mannar',
                'Matale',
                'Matara',
                'Monaragala',
                'Mullaitivu',
                'Nuwara Eliya',
                'Polonnaruwa',
                'Puttalam',
                'Ratnapura',
                'Trincomalee',
                'Vavuniya'
            ],
            'education' => [
                'O/L',
                'A/L',
                'Diploma',
                "Bachelor's Degree",
                "Master's Degree",
                'PhD',
                'Other'
            ],
            default => []
        };

        return array_combine($data, $data); // Returns associative array with same keys and values
    }

    /**
     * Get castes by religion
     * @param string $religion Religion name
     * @return array
     */
    public function getCastesByReligion(string $religion): array {
        // This is a simplified example. In production, this should come from database
        return match ($religion) {
            'Buddhist' => [
                'Govigama',
                'Karava',
                'Salagama',
                'Durava',
                'Other'
            ],
            'Hindu' => [
                'Brahmin',
                'Kshatriya',
                'Vaishya',
                'Other'
            ],
            'Muslim' => [
                'Memon',
                'Bohra',
                'Other'
            ],
            default => ['Other']
        };
    }

    /**
     * Delete profile photo
     * @param int $userId User ID
     * @return bool
     */
    public function deleteProfilePhoto(int $userId): bool {
        try {
            // Begin transaction
            $this->pdo->beginTransaction();

            // Get current photo filename
            $stmt = $this->pdo->prepare("SELECT profile_photo FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            $filename = $stmt->fetchColumn();

            if (!$filename) {
                $this->error = "No profile photo found";
                return false;
            }

            // Update database
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET profile_photo = NULL, updated_at = NOW() 
                WHERE id = :id
            ");
            $stmt->execute(['id' => $userId]);

            // Delete file
            $filepath = __DIR__ . '/../../public/uploads/profile_photos/' . $filename;
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            // Commit transaction
            $this->pdo->commit();

            return true;

        } catch (Exception $e) {
            // Rollback transaction on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            error_log("Photo deletion error: " . $e->getMessage());
            $this->error = "Failed to delete photo";
            return false;
        }
    }
} 