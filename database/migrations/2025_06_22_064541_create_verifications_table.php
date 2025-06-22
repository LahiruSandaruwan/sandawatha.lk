<?php
/**
 * Migration: create_verifications_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateVerificationsTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS verifications (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            type ENUM('email', 'phone', 'identity') NOT NULL,
            token VARCHAR(100) NOT NULL,
            code VARCHAR(6) NULL,
            expires_at DATETIME NOT NULL,
            verified_at DATETIME NULL,
            attempts INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_token (token),
            UNIQUE KEY unique_user_type (user_id, type),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS verifications;";
        return $pdo->exec($sql);
    }
}
