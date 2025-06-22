<?php
/**
 * Migration: create_messages_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateMessagesTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS messages (
            id INT PRIMARY KEY AUTO_INCREMENT,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            content TEXT NOT NULL,
            attachment VARCHAR(255) NULL,
            read_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS messages;";
        return $pdo->exec($sql);
    }
}
