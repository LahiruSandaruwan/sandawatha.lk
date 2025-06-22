<?php
/**
 * Subscription Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class Subscription
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'Subscription')).''.'s';

    // Properties
        private $id;
        private $user_id;
        private $plan;
        private $amount;
        private $start_date;
        private $end_date;
        private $status;
        private $payment_method;
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
         * Get plan
         */
        public function getPlan()
        {
            return $this->plan;
        }    
        /**
         * Get amount
         */
        public function getAmount()
        {
            return $this->amount;
        }    
        /**
         * Get start_date
         */
        public function getStartDate()
        {
            return $this->start_date;
        }    
        /**
         * Get end_date
         */
        public function getEndDate()
        {
            return $this->end_date;
        }    
        /**
         * Get status
         */
        public function getStatus()
        {
            return $this->status;
        }    
        /**
         * Get payment_method
         */
        public function getPaymentMethod()
        {
            return $this->payment_method;
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
         * Set plan
         */
        public function setPlan($value)
        {
            $this->plan = $value;
            return $this;
        }    
        /**
         * Set amount
         */
        public function setAmount($value)
        {
            $this->amount = $value;
            return $this;
        }    
        /**
         * Set start_date
         */
        public function setStartDate($value)
        {
            $this->start_date = $value;
            return $this;
        }    
        /**
         * Set end_date
         */
        public function setEndDate($value)
        {
            $this->end_date = $value;
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
         * Set payment_method
         */
        public function setPaymentMethod($value)
        {
            $this->payment_method = $value;
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