<?php
/**
 * Migration: create_referrals_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateReferralsTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS referrals (
            id INT PRIMARY KEY AUTO_INCREMENT,
            referrer_id INT NOT NULL,
            referred_id INT NULL,
            code VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            status ENUM('pending', 'registered', 'expired') NOT NULL DEFAULT 'pending',
            expires_at DATETIME NOT NULL,
            registered_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_code (code),
            UNIQUE KEY unique_email (email),
            FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (referred_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS referrals;";
        return $pdo->exec($sql);
    }
}
