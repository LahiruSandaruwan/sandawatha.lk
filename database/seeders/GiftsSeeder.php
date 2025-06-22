<?php
/**
 * GiftsSeeder
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * Seeds the gifts table with initial virtual gifts
 */

class GiftsSeeder
{
    /**
     * Run the seeder
     */
    public function run(PDO $pdo)
    {
        // Initial virtual gifts data
        $data = [
            [
                'name' => 'Red Rose',
                'description' => 'A beautiful virtual red rose to express your love',
                'icon' => 'assets/images/gifts/rose.png',
                'price' => 100.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Heart',
                'description' => 'Send a heart to show your interest',
                'icon' => 'assets/images/gifts/heart.png',
                'price' => 50.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ring',
                'description' => 'A virtual ring to show your commitment',
                'icon' => 'assets/images/gifts/ring.png',
                'price' => 200.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        try {
            // Prepare insert statement with named parameters
            $columns = array_keys($data[0]);
            $placeholders = ':' . implode(', :', $columns);
            
            $sql = "INSERT INTO gifts (" . implode(', ', $columns) . ") 
                    VALUES (" . $placeholders . ")";
            
            $stmt = $pdo->prepare($sql);

            // Insert each record
            foreach ($data as $record) {
                if (!$stmt->execute($record)) {
                    throw new Exception("Failed to insert gift: " . implode(', ', $stmt->errorInfo()));
                }
            }

            return true;

        } catch (Exception $e) {
            throw new Exception("Failed to seed gifts: " . $e->getMessage());
        }
    }
}