<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Exception;

class AuthController {
    private $pdo;
    private $error;

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
     * Authenticate user with email/phone and password
     * @param string $identifier Email or phone
     * @param string $password Plain text password
     * @param bool $remember Remember me flag
     * @return bool
     */
    public function login(string $identifier, string $password, bool $remember = false): bool {
        try {
            // Validate inputs
            $identifier = trim($identifier);
            if (empty($identifier) || empty($password)) {
                $this->error = "All fields are required";
                return false;
            }

            // Determine if identifier is email or phone
            $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
            $identifierField = $isEmail ? 'email' : 'phone';
            
            // Prepare query with active status check
            $stmt = $this->pdo->prepare("
                SELECT id, name, email, password_hash, status 
                FROM users 
                WHERE {$identifierField} = :identifier 
                LIMIT 1
            ");
            
            $stmt->execute(['identifier' => $identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify user exists and is active
            if (!$user) {
                $this->error = "Account not found";
                return false;
            }

            if ($user['status'] !== 'active') {
                $this->error = "Account is not active";
                return false;
            }

            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                $this->error = "Invalid password";
                return false;
            }

            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + (86400 * 30); // 30 days
                
                // Store token in database
                $stmt = $this->pdo->prepare("
                    INSERT INTO remember_tokens (user_id, token, expires_at) 
                    VALUES (:user_id, :token, :expires_at)
                ");
                
                $stmt->execute([
                    'user_id' => $user['id'],
                    'token' => password_hash($token, PASSWORD_DEFAULT),
                    'expires_at' => date('Y-m-d H:i:s', $expires)
                ]);
                
                // Set secure cookie
                setcookie('remember_token', $token, [
                    'expires' => $expires,
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            }

            // Update last login timestamp
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET last_login = NOW() 
                WHERE id = :id
            ");
            $stmt->execute(['id' => $user['id']]);

            return true;

        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $this->error = "Login failed. Please try again later.";
            return false;
        }
    }

    /**
     * Register a new user
     * @param array $data User registration data
     * @return bool
     */
    public function register(array $data): bool {
        try {
            // Validate required fields
            $required = ['name', 'email', 'phone', 'password', 'dob', 'gender'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $this->error = "All required fields must be filled";
                    return false;
                }
            }

            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->error = "Invalid email format";
                return false;
            }

            // Validate phone (Sri Lankan format)
            if (!preg_match('/^(?:\+94|0)?[0-9]{9,10}$/', $data['phone'])) {
                $this->error = "Invalid phone number format";
                return false;
            }

            // Check if email or phone already exists
            $stmt = $this->pdo->prepare("
                SELECT id FROM users 
                WHERE email = :email OR phone = :phone 
                LIMIT 1
            ");
            
            $stmt->execute([
                'email' => $data['email'],
                'phone' => $data['phone']
            ]);

            if ($stmt->fetch()) {
                $this->error = "Email or phone number already registered";
                return false;
            }

            // Begin transaction
            $this->pdo->beginTransaction();

            // Insert user data
            $stmt = $this->pdo->prepare("
                INSERT INTO users (
                    name, email, phone, password_hash, dob, gender,
                    religion, caste, district, education, profession,
                    preferred_age_min, preferred_age_max, preferred_caste,
                    preferred_districts, profile_photo, status, created_at
                ) VALUES (
                    :name, :email, :phone, :password_hash, :dob, :gender,
                    :religion, :caste, :district, :education, :profession,
                    :preferred_age_min, :preferred_age_max, :preferred_caste,
                    :preferred_districts, :profile_photo, 'active', NOW()
                )
            ");

            $stmt->execute([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'dob' => $data['dob'],
                'gender' => $data['gender'],
                'religion' => $data['religion'] ?? null,
                'caste' => $data['caste'] ?? null,
                'district' => $data['district'] ?? null,
                'education' => $data['education'] ?? null,
                'profession' => $data['profession'] ?? null,
                'preferred_age_min' => $data['preferred_age_min'] ?? null,
                'preferred_age_max' => $data['preferred_age_max'] ?? null,
                'preferred_caste' => $data['preferred_caste'] ?? null,
                'preferred_districts' => $data['preferred_districts'] ?? null,
                'profile_photo' => $data['profile_photo'] ?? null
            ]);

            $userId = $this->pdo->lastInsertId();

            // Commit transaction
            $this->pdo->commit();

            // Start session and log user in
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['user_email'] = $data['email'];

            return true;

        } catch (PDOException $e) {
            // Rollback transaction on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            error_log("Registration error: " . $e->getMessage());
            $this->error = "Registration failed. Please try again later.";
            return false;
        }
    }

    /**
     * Log out the current user
     * @return bool
     */
    public function logout(): bool {
        try {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Clear remember me token if exists
            if (isset($_COOKIE['remember_token'])) {
                // Delete token from database
                $stmt = $this->pdo->prepare("
                    DELETE FROM remember_tokens 
                    WHERE user_id = :user_id
                ");
                $stmt->execute(['user_id' => $_SESSION['user_id'] ?? 0]);

                // Delete cookie
                setcookie('remember_token', '', [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            }

            // Destroy session
            session_unset();
            session_destroy();
            
            return true;

        } catch (Exception $e) {
            error_log("Logout error: " . $e->getMessage());
            $this->error = "Logout failed. Please try again.";
            return false;
        }
    }

    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']);
    }

    /**
     * Validate remember me token and auto-login
     * @return bool
     */
    public function validateRememberToken(): bool {
        try {
            if (!isset($_COOKIE['remember_token'])) {
                return false;
            }

            $token = $_COOKIE['remember_token'];

            // Find valid token
            $stmt = $this->pdo->prepare("
                SELECT t.user_id, t.token, u.name, u.email 
                FROM remember_tokens t
                JOIN users u ON u.id = t.user_id
                WHERE t.expires_at > NOW()
                AND u.status = 'active'
            ");
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($token, $row['token'])) {
                    // Start session if needed
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    // Set session variables
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['user_name'] = $row['name'];
                    $_SESSION['user_email'] = $row['email'];

                    return true;
                }
            }

            return false;

        } catch (Exception $e) {
            error_log("Remember token validation error: " . $e->getMessage());
            return false;
        }
    }
} 