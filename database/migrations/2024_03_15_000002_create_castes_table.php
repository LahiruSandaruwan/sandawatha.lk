<?php
/**
 * Migration: create_castes_table
 * Created at: 2024_03_15_000002
 */

class Migration_2024_03_15_000002_create_castes_table
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS castes (
            id INT PRIMARY KEY AUTO_INCREMENT,
            religion_id INT NOT NULL,
            name VARCHAR(50) NOT NULL,
            name_si VARCHAR(50) NOT NULL,
            name_ta VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (religion_id) REFERENCES religions(id),
            UNIQUE KEY unique_name_religion (name, religion_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS castes;";
        return $pdo->exec($sql);
    }
} 