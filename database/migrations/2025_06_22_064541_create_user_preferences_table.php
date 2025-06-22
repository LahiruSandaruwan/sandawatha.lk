<?php
/**
 * Migration: create_user_preferences_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateUserPreferencesTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS user_preferences (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            min_age INT NOT NULL DEFAULT 18,
            max_age INT NOT NULL DEFAULT 60,
            preferred_religion_id INT NULL,
            preferred_caste_id INT NULL,
            preferred_district_id INT NULL,
            preferred_marital_status ENUM('never_married', 'divorced', 'widowed', 'any') NOT NULL DEFAULT 'any',
            preferred_education ENUM('any', 'high_school', 'bachelors', 'masters', 'phd') NOT NULL DEFAULT 'any',
            preferred_employment ENUM('any', 'employed', 'self_employed', 'business_owner', 'student') NOT NULL DEFAULT 'any',
            min_height INT NULL,
            max_height INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_id (user_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (preferred_religion_id) REFERENCES religions(id) ON DELETE SET NULL,
            FOREIGN KEY (preferred_caste_id) REFERENCES castes(id) ON DELETE SET NULL,
            FOREIGN KEY (preferred_district_id) REFERENCES districts(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS user_preferences;";
        return $pdo->exec($sql);
    }
}
