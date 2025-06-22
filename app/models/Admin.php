<?php
/**
 * Admin Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class Admin
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'Admin')).''.'s';

    // Properties
        private $id;
        private $username;
        private $email;
        private $password;
        private $role;
        private $last_login;
        private $created_at;
        private $updated_at;

    /**
     * Constructor
     */
    public function __construct(PDO $pdo = null)
    {
        $this->pdo = $pdo ?: require dirname(__DIR__, 2) . '/config/database.php';
    }

    /**
     * Find by ID
     */
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new record
     */
    public function create(array $data)
    {
        // Implementation
    }

    /**
     * Update record
     */
    public function update($id, array $data)
    {
        // Implementation
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get all records
     */
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Getters        
        /**
         * Get id
         */
        public function getId()
        {
            return $this->id;
        }    
        /**
         * Get username
         */
        public function getUsername()
        {
            return $this->username;
        }    
        /**
         * Get email
         */
        public function getEmail()
        {
            return $this->email;
        }    
        /**
         * Get password
         */
        public function getPassword()
        {
            return $this->password;
        }    
        /**
         * Get role
         */
        public function getRole()
        {
            return $this->role;
        }    
        /**
         * Get last_login
         */
        public function getLastLogin()
        {
            return $this->last_login;
        }    
        /**
         * Get created_at
         */
        public function getCreatedAt()
        {
            return $this->created_at;
        }    
        /**
         * Get updated_at
         */
        public function getUpdatedAt()
        {
            return $this->updated_at;
        }

    // Setters        
        /**
         * Set id
         */
        public function setId($value)
        {
            $this->id = $value;
            return $this;
        }    
        /**
         * Set username
         */
        public function setUsername($value)
        {
            $this->username = $value;
            return $this;
        }    
        /**
         * Set email
         */
        public function setEmail($value)
        {
            $this->email = $value;
            return $this;
        }    
        /**
         * Set password
         */
        public function setPassword($value)
        {
            $this->password = $value;
            return $this;
        }    
        /**
         * Set role
         */
        public function setRole($value)
        {
            $this->role = $value;
            return $this;
        }    
        /**
         * Set last_login
         */
        public function setLastLogin($value)
        {
            $this->last_login = $value;
            return $this;
        }    
        /**
         * Set created_at
         */
        public function setCreatedAt($value)
        {
            $this->created_at = $value;
            return $this;
        }    
        /**
         * Set updated_at
         */
        public function setUpdatedAt($value)
        {
            $this->updated_at = $value;
            return $this;
        }
}