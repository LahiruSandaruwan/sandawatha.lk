<?php
/**
 * Migration: create_reports_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateReportsTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS reports (
            id INT PRIMARY KEY AUTO_INCREMENT,
            reporter_id INT NOT NULL,
            reported_id INT NOT NULL,
            reason ENUM('fake_profile', 'inappropriate_content', 'harassment', 'spam', 'other') NOT NULL,
            description TEXT NULL,
            status ENUM('pending', 'investigating', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending',
            resolved_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (reported_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS reports;";
        return $pdo->exec($sql);
    }
}
