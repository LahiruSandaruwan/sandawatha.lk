#!/usr/bin/env php
<?php
/**
 * Database Rollback Command
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

// Ensure script is run from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

class RollbackCommand
{
    private $pdo;
    private $migrationsPath;

    public function __construct()
    {
        $this->pdo = require dirname(__DIR__) . '/config/database.php';
        $this->migrationsPath = dirname(__DIR__) . '/database/migrations';
    }

    /**
     * Get last batch migrations
     */
    private function getLastBatchMigrations()
    {
        $stmt = $this->pdo->query("
            SELECT migration 
            FROM migrations 
            WHERE batch = (SELECT MAX(batch) FROM migrations)
            ORDER BY id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Run rollback
     */
    public function run()
    {
        // Get migrations from last batch
        $migrations = $this->getLastBatchMigrations();
        if (empty($migrations)) {
            echo "Nothing to rollback.\n";
            return;
        }

        $count = 0;
        foreach ($migrations as $migrationName) {
            echo "Rolling back: {$migrationName}\n";

            // Include and instantiate migration class
            $file = $this->migrationsPath . '/' . $migrationName;
            if (!file_exists($file)) {
                echo "✗ Migration file not found: {$migrationName}\n";
                continue;
            }

            require_once $file;
            $className = 'Migration_' . substr($migrationName, 0, -4); // Remove .php
            $migration = new $className();

            try {
                // Begin transaction
                $this->pdo->beginTransaction();

                // Run rollback
                $migration->down($this->pdo);

                // Remove migration record
                $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE migration = ?");
                $stmt->execute([$migrationName]);

                // Commit transaction
                $this->pdo->commit();
                $count++;
                echo "✓ Rollback completed: {$migrationName}\n";
            } catch (Exception $e) {
                // Rollback transaction
                $this->pdo->rollBack();
                echo "✗ Error in rollback {$migrationName}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }

        if ($count > 0) {
            echo "\nSuccessfully rolled back {$count} " . ($count === 1 ? 'migration' : 'migrations') . ".\n";
        }
    }
}

// Run rollback
$command = new RollbackCommand();
$command->run();