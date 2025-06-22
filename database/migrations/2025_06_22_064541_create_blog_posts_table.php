<?php
/**
 * Migration: create_blog_posts_table
 * Created at: 2025_06_22_064541
 */

class Migration_2025_06_22_064541_CreateBlogPostsTable
{
    /**
     * Run the migration
     */
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS blog_posts (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            author_id INT NOT NULL,
            status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
            published_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_slug (slug),
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $pdo->exec($sql);
    }

    /**
     * Reverse the migration
     */
    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS blog_posts;";
        return $pdo->exec($sql);
    }
}
