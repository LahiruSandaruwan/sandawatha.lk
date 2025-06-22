<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Exception;

class AdminController {
    private $pdo;
    private $error;
    private $maxLoginAttempts = 5;
    private $lockoutDuration = 1800; // 30 minutes
    private $usersPerPage = 20;
    private $sessionTimeout = 7200; // 2 hours

    /**
     * Constructor - initialize database connection
     */
    public function __construct() {
        try {
            require_once __DIR__ . '/../../config/database.php';
            $this->pdo = $pdo;
            
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    /**
     * Admin login with security features
     * @param string $username Admin username
     * @param string $password Admin password
     * @return array JSON response
     */
    public function adminLogin(string $username, string $password): array {
        try {
            // Check if IP is blocked
            if ($this->isIpBlocked()) {
                return $this->jsonResponse(false, "Too many login attempts. Please try again later.");
            }

            // Validate input
            $username = trim($username);
            $password = trim($password);
            
            if (empty($username) || empty($password)) {
                return $this->jsonResponse(false, "Username and password are required");
            }

            // Get admin user
            $stmt = $this->pdo->prepare("
                SELECT id, username, password_hash, status, last_login
                FROM admin_users 
                WHERE username = :username 
                AND status = 'active'
                LIMIT 1
            ");
            
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin || !password_verify($password, $admin['password_hash'])) {
                $this->recordFailedLogin();
                return $this->jsonResponse(false, "Invalid credentials");
            }

            // Update last login and clear failed attempts
            $this->clearFailedLogins();
            $this->updateLastLogin($admin['id']);

            // Set admin session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_last_activity'] = time();
            $_SESSION['admin_ip'] = $_SERVER['REMOTE_ADDR'];

            return $this->jsonResponse(true, "Login successful", [
                'admin_id' => $admin['id'],
                'username' => $admin['username'],
                'last_login' => $admin['last_login']
            ]);

        } catch (PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            return $this->jsonResponse(false, "Login failed");
        }
    }

    /**
     * Get all users with pagination and filters
     * @param int $page Page number
     * @param array $filters Optional filters
     * @return array JSON response
     */
    public function getAllUsers(int $page = 1, array $filters = []): array {
        try {
            if (!$this->isAdminAuthenticated()) {
                return $this->jsonResponse(false, "Unauthorized access");
            }

            // Calculate offset
            $offset = ($page - 1) * $this->usersPerPage;

            // Build query
            $query = "
                SELECT 
                    u.id,
                    u.name,
                    u.email,
                    u.phone,
                    u.status,
                    u.is_verified,
                    u.created_at,
                    u.last_active,
                    (
                        SELECT COUNT(*) 
                        FROM reports r 
                        WHERE r.reported_user_id = u.id
                    ) as report_count
                FROM users u
                WHERE 1=1
            ";

            $params = [];

            // Add filters
            if (!empty($filters['status'])) {
                $query .= " AND u.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['verified'])) {
                $query .= " AND u.is_verified = :verified";
                $params['verified'] = $filters['verified'];
            }

            if (!empty($filters['search'])) {
                $query .= " AND (
                    u.name LIKE :search 
                    OR u.email LIKE :search 
                    OR u.phone LIKE :search
                )";
                $params['search'] = "%{$filters['search']}%";
            }

            // Add sorting
            $query .= " ORDER BY u.created_at DESC";

            // Add pagination
            $query .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $this->usersPerPage;
            $params['offset'] = $offset;

            // Execute query
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count
            $countQuery = str_replace(
                ["SELECT", "ORDER BY", "LIMIT"],
                ["SELECT COUNT(*) as total", "", ""],
                $query
            );
            $stmt = $this->pdo->prepare($countQuery);
            foreach ($params as $key => $value) {
                if (!in_array($key, ['limit', 'offset'])) {
                    $stmt->bindValue($key, $value);
                }
            }
            $stmt->execute();
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return $this->jsonResponse(true, "Users retrieved successfully", [
                'users' => $users,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($totalUsers / $this->usersPerPage),
                    'total_users' => $totalUsers
                ]
            ]);

        } catch (PDOException $e) {
            error_log("Error getting users: " . $e->getMessage());
            return $this->jsonResponse(false, "Failed to get users");
        }
    }

    /**
     * Ban a user
     * @param int $userId User ID to ban
     * @param string $reason Ban reason
     * @return array JSON response
     */
    public function banUser(int $userId, string $reason): array {
        try {
            if (!$this->isAdminAuthenticated()) {
                return $this->jsonResponse(false, "Unauthorized access");
            }

            // Start transaction
            $this->pdo->beginTransaction();

            // Update user status
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET 
                    status = 'banned',
                    banned_at = NOW(),
                    banned_reason = :reason,
                    banned_by = :admin_id
                WHERE id = :user_id
            ");

            $stmt->execute([
                'reason' => $reason,
                'admin_id' => $_SESSION['admin_id'],
                'user_id' => $userId
            ]);

            // Log the action
            $this->logAdminAction('ban_user', $userId, [
                'reason' => $reason
            ]);

            $this->pdo->commit();

            return $this->jsonResponse(true, "User banned successfully");

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error banning user: " . $e->getMessage());
            return $this->jsonResponse(false, "Failed to ban user");
        }
    }

    /**
     * Verify a user
     * @param int $userId User ID to verify
     * @return array JSON response
     */
    public function verifyUser(int $userId): array {
        try {
            if (!$this->isAdminAuthenticated()) {
                return $this->jsonResponse(false, "Unauthorized access");
            }

            // Start transaction
            $this->pdo->beginTransaction();

            // Update user verification status
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET 
                    is_verified = 1,
                    verified_at = NOW(),
                    verified_by = :admin_id
                WHERE id = :user_id
            ");

            $stmt->execute([
                'admin_id' => $_SESSION['admin_id'],
                'user_id' => $userId
            ]);

            // Log the action
            $this->logAdminAction('verify_user', $userId);

            $this->pdo->commit();

            return $this->jsonResponse(true, "User verified successfully");

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error verifying user: " . $e->getMessage());
            return $this->jsonResponse(false, "Failed to verify user");
        }
    }

    /**
     * Get user reports with pagination
     * @param int $page Page number
     * @param string $status Report status filter
     * @return array JSON response
     */
    public function getReports(int $page = 1, string $status = 'pending'): array {
        try {
            if (!$this->isAdminAuthenticated()) {
                return $this->jsonResponse(false, "Unauthorized access");
            }

            $offset = ($page - 1) * $this->usersPerPage;

            // Get reports
            $query = "
                SELECT 
                    r.id,
                    r.reporter_id,
                    r.reported_user_id,
                    r.reason,
                    r.details,
                    r.status,
                    r.created_at,
                    u1.name as reporter_name,
                    u2.name as reported_user_name,
                    u2.email as reported_user_email
                FROM reports r
                INNER JOIN users u1 ON u1.id = r.reporter_id
                INNER JOIN users u2 ON u2.id = r.reported_user_id
                WHERE r.status = :status
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':limit', $this->usersPerPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM reports 
                WHERE status = :status
            ");
            $stmt->execute(['status' => $status]);
            $totalReports = $stmt->fetchColumn();

            return $this->jsonResponse(true, "Reports retrieved successfully", [
                'reports' => $reports,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($totalReports / $this->usersPerPage),
                    'total_reports' => $totalReports
                ]
            ]);

        } catch (PDOException $e) {
            error_log("Error getting reports: " . $e->getMessage());
            return $this->jsonResponse(false, "Failed to get reports");
        }
    }

    /**
     * Check if admin is authenticated
     * @return bool Authentication status
     */
    private function isAdminAuthenticated(): bool {
        if (!isset($_SESSION['admin_id']) || 
            !isset($_SESSION['admin_last_activity']) || 
            !isset($_SESSION['admin_ip'])) {
            return false;
        }

        // Check session timeout
        if (time() - $_SESSION['admin_last_activity'] > $this->sessionTimeout) {
            $this->adminLogout();
            return false;
        }

        // Check IP binding
        if ($_SESSION['admin_ip'] !== $_SERVER['REMOTE_ADDR']) {
            $this->adminLogout();
            return false;
        }

        // Update last activity
        $_SESSION['admin_last_activity'] = time();
        return true;
    }

    /**
     * Log admin actions
     * @param string $action Action type
     * @param int $targetId Target user ID
     * @param array $details Additional details
     * @return bool Success status
     */
    private function logAdminAction(string $action, int $targetId, array $details = []): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO admin_logs (
                    admin_id,
                    action,
                    target_id,
                    details,
                    ip_address,
                    created_at
                ) VALUES (
                    :admin_id,
                    :action,
                    :target_id,
                    :details,
                    :ip_address,
                    NOW()
                )
            ");

            return $stmt->execute([
                'admin_id' => $_SESSION['admin_id'],
                'action' => $action,
                'target_id' => $targetId,
                'details' => json_encode($details),
                'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);

        } catch (PDOException $e) {
            error_log("Error logging admin action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if IP is blocked due to failed login attempts
     * @return bool Blocked status
     */
    private function isIpBlocked(): bool {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM failed_logins 
                WHERE ip_address = :ip 
                AND created_at > NOW() - INTERVAL :lockout SECOND
            ");

            $stmt->execute([
                'ip' => $_SERVER['REMOTE_ADDR'],
                'lockout' => $this->lockoutDuration
            ]);

            return $stmt->fetchColumn() >= $this->maxLoginAttempts;

        } catch (PDOException $e) {
            error_log("Error checking IP block: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Record failed login attempt
     * @return bool Success status
     */
    private function recordFailedLogin(): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO failed_logins (
                    ip_address,
                    created_at
                ) VALUES (
                    :ip,
                    NOW()
                )
            ");

            return $stmt->execute([
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);

        } catch (PDOException $e) {
            error_log("Error recording failed login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear failed login attempts
     * @return bool Success status
     */
    private function clearFailedLogins(): bool {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM failed_logins 
                WHERE ip_address = :ip
            ");

            return $stmt->execute([
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);

        } catch (PDOException $e) {
            error_log("Error clearing failed logins: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update admin's last login timestamp
     * @param int $adminId Admin user ID
     * @return bool Success status
     */
    private function updateLastLogin(int $adminId): bool {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE admin_users 
                SET last_login = NOW() 
                WHERE id = :admin_id
            ");

            return $stmt->execute([
                'admin_id' => $adminId
            ]);

        } catch (PDOException $e) {
            error_log("Error updating last login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Admin logout
     * @return bool Success status
     */
    public function adminLogout(): bool {
        try {
            // Clear admin session
            unset($_SESSION['admin_id']);
            unset($_SESSION['admin_username']);
            unset($_SESSION['admin_last_activity']);
            unset($_SESSION['admin_ip']);
            
            return true;

        } catch (Exception $e) {
            error_log("Error during logout: " . $e->getMessage());
            return false;
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