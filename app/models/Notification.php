<?php
/**
 * Notification Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class Notification
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'Notification')).''.'s';

    // Properties
        private $id;
        private $user_id;
        private $type;
        private $content;
        private $is_read;
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
         * Get user_id
         */
        public function getUserId()
        {
            return $this->user_id;
        }    
        /**
         * Get type
         */
        public function getType()
        {
            return $this->type;
        }    
        /**
         * Get content
         */
        public function getContent()
        {
            return $this->content;
        }    
        /**
         * Get is_read
         */
        public function getIsRead()
        {
            return $this->is_read;
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
         * Set user_id
         */
        public function setUserId($value)
        {
            $this->user_id = $value;
            return $this;
        }    
        /**
         * Set type
         */
        public function setType($value)
        {
            $this->type = $value;
            return $this;
        }    
        /**
         * Set content
         */
        public function setContent($value)
        {
            $this->content = $value;
            return $this;
        }    
        /**
         * Set is_read
         */
        public function setIsRead($value)
        {
            $this->is_read = $value;
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