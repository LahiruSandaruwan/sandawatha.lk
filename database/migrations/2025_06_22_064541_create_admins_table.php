<?php
/**
 * Migration: create_admins_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateAdminsTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS admins (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            role ENUM('super_admin', 'admin', 'moderator') NOT NULL DEFAULT 'admin',
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            last_login DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_username (username),
            UNIQUE KEY unique_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS admins;";
        return $pdo->exec($sql);
    }
}