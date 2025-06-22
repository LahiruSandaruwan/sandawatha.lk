<?php
/**
 * Referral Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class Referral
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'Referral')).''.'s';

    // Properties
        private $id;
        private $referrer_id;
        private $referred_email;
        private $status;
        private $joined_user_id;
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
         * Get referrer_id
         */
        public function getReferrerId()
        {
            return $this->referrer_id;
        }    
        /**
         * Get referred_email
         */
        public function getReferredEmail()
        {
            return $this->referred_email;
        }    
        /**
         * Get status
         */
        public function getStatus()
        {
            return $this->status;
        }    
        /**
         * Get joined_user_id
         */
        public function getJoinedUserId()
        {
            return $this->joined_user_id;
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
         * Set referrer_id
         */
        public function setReferrerId($value)
        {
            $this->referrer_id = $value;
            return $this;
        }    
        /**
         * Set referred_email
         */
        public function setReferredEmail($value)
        {
            $this->referred_email = $value;
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
         * Set joined_user_id
         */
        public function setJoinedUserId($value)
        {
            $this->joined_user_id = $value;
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