<?php
/**
 * Migration: create_users_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateUsersTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            gender ENUM('male', 'female') NOT NULL,
            date_of_birth DATE NOT NULL,
            religion_id INT NULL,
            caste_id INT NULL,
            district_id INT NULL,
            phone VARCHAR(20) NULL,
            address TEXT NULL,
            bio TEXT NULL,
            profile_photo VARCHAR(255) NULL,
            email_verified_at DATETIME NULL,
            phone_verified_at DATETIME NULL,
            status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
            last_login DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_email (email),
            FOREIGN KEY (religion_id) REFERENCES religions(id) ON DELETE SET NULL,
            FOREIGN KEY (caste_id) REFERENCES castes(id) ON DELETE SET NULL,
            FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS users;";
        return $pdo->exec($sql);
    }
}