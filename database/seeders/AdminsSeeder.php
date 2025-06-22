<?php
/**
 * AdminsSeeder
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 * 
 * Seeds the admins table with initial admin user
 */

class AdminsSeeder
{
    /**
     * Run the seeder
     */
    public function run(PDO $pdo)
    {
        try {
            // Initial admin user data
            $data = [
                'username' => 'admin',
                'email' => 'admin@sandawatha.lk',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'role' => 'super_admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Prepare insert statement
            $sql = "INSERT INTO admins (username, email, password, first_name, last_name, role, status, created_at, updated_at) 
                   VALUES (:username, :email, :password, :first_name, :last_name, :role, :status, :created_at, :updated_at)";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($data);

        } catch (Exception $e) {
            throw new Exception("Failed to seed admin user: " . $e->getMessage());
        }
    }
}