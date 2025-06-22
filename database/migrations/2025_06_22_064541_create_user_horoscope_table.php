<?php
/**
 * Migration: create_user_horoscope_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateUserHoroscopeTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS user_horoscope (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            zodiac_sign ENUM('aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo', 'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius', 'pisces') NOT NULL,
            nakshatra VARCHAR(50) NULL,
            rashi VARCHAR(50) NULL,
            gana VARCHAR(50) NULL,
            nadi VARCHAR(50) NULL,
            birth_time TIME NULL,
            birth_place VARCHAR(100) NULL,
            horoscope_image VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_id (user_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS user_horoscope;";
        return $pdo->exec($sql);
    }
}
