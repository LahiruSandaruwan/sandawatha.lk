<?php
/**
 * Migration: create_subscriptions_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateSubscriptionsTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS subscriptions (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            plan ENUM('free', 'basic', 'premium', 'platinum') NOT NULL DEFAULT 'free',
            status ENUM('active', 'cancelled', 'expired') NOT NULL DEFAULT 'active',
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            payment_method VARCHAR(50) NULL,
            payment_id VARCHAR(100) NULL,
            amount DECIMAL(10,2) NOT NULL,
            auto_renew BOOLEAN NOT NULL DEFAULT FALSE,
            cancelled_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS subscriptions;";
        return $pdo->exec($sql);
    }
}
