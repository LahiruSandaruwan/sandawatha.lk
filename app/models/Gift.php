<?php
/**
 * Gift Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class Gift
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'Gift')).''.'s';

    // Properties
        private $id;
        private $sender_id;
        private $receiver_id;
        private $gift_type;
        private $message;
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
         * Get gift_type
         */
        public function getGiftType()
        {
            return $this->gift_type;
        }    
        /**
         * Get message
         */
        public function getMessage()
        {
            return $this->message;
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
         * Set gift_type
         */
        public function setGiftType($value)
        {
            $this->gift_type = $value;
            return $this;
        }    
        /**
         * Set message
         */
        public function setMessage($value)
        {
            $this->message = $value;
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