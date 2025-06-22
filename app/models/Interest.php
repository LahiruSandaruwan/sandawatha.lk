<?php
/**
 * Interest Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class Interest
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'Interest')).''.'s';

    // Properties
        private $id;
        private $sender_id;
        private $receiver_id;
        private $status;
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
         * Get sender_id
         */
        public function getSenderId()
        {
            return $this->sender_id;
        }    
        /**
         * Get receiver_id
         */
        public function getReceiverId()
        {
            return $this->receiver_id;
        }    
        /**
         * Get status
         */
        public function getStatus()
        {
            return $this->status;
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
         * Set sender_id
         */
        public function setSenderId($value)
        {
            $this->sender_id = $value;
            return $this;
        }    
        /**
         * Set receiver_id
         */
        public function setReceiverId($value)
        {
            $this->receiver_id = $value;
            return $this;
        }    
        /**
         * Set status
         */
        public function setStatus($value)
        {
            $this->status = $value;
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