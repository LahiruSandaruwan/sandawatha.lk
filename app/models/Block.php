<?php
/**
 * Block Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class Block
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'Block')).''.'s';

    // Properties
        private $id;
        private $blocker_id;
        private $blocked_user_id;
        private $reason;
        private $created_at;

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
         * Get blocker_id
         */
        public function getBlockerId()
        {
            return $this->blocker_id;
        }    
        /**
         * Get blocked_user_id
         */
        public function getBlockedUserId()
        {
            return $this->blocked_user_id;
        }    
        /**
         * Get reason
         */
        public function getReason()
        {
            return $this->reason;
        }    
        /**
         * Get created_at
         */
        public function getCreatedAt()
        {
            return $this->created_at;
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
         * Set blocker_id
         */
        public function setBlockerId($value)
        {
            $this->blocker_id = $value;
            return $this;
        }    
        /**
         * Set blocked_user_id
         */
        public function setBlockedUserId($value)
        {
            $this->blocked_user_id = $value;
            return $this;
        }    
        /**
         * Set reason
         */
        public function setReason($value)
        {
            $this->reason = $value;
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
}