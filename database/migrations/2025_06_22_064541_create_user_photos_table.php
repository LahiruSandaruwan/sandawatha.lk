<?php
/**
 * Migration: create_user_photos_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateUserPhotosTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS user_photos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            photo_path VARCHAR(255) NOT NULL,
            is_primary BOOLEAN NOT NULL DEFAULT FALSE,
            status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
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
        $sql = "DROP TABLE IF EXISTS user_photos;";
        return $pdo->exec($sql);
    }
}
