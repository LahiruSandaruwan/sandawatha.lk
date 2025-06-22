<?php
/**
 * UserPreference Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class UserPreference
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'UserPreference')).''.'s';

    // Properties
        private $id;
        private $user_id;
        private $min_age;
        private $max_age;
        private $religion_id;
        private $caste_id;
        private $district_id;
        private $marital_status;
        private $min_height;
        private $max_height;
        private $education;
        private $occupation;
        private $drinking;
        private $smoking;

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
         * Get min_age
         */
        public function getMinAge()
        {
            return $this->min_age;
        }    
        /**
         * Get max_age
         */
        public function getMaxAge()
        {
            return $this->max_age;
        }    
        /**
         * Get religion_id
         */
        public function getReligionId()
        {
            return $this->religion_id;
        }    
        /**
         * Get caste_id
         */
        public function getCasteId()
        {
            return $this->caste_id;
        }    
        /**
         * Get district_id
         */
        public function getDistrictId()
        {
            return $this->district_id;
        }    
        /**
         * Get marital_status
         */
        public function getMaritalStatus()
        {
            return $this->marital_status;
        }    
        /**
         * Get min_height
         */
        public function getMinHeight()
        {
            return $this->min_height;
        }    
        /**
         * Get max_height
         */
        public function getMaxHeight()
        {
            return $this->max_height;
        }    
        /**
         * Get education
         */
        public function getEducation()
        {
            return $this->education;
        }    
        /**
         * Get occupation
         */
        public function getOccupation()
        {
            return $this->occupation;
        }    
        /**
         * Get drinking
         */
        public function getDrinking()
        {
            return $this->drinking;
        }    
        /**
         * Get smoking
         */
        public function getSmoking()
        {
            return $this->smoking;
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
         * Set min_age
         */
        public function setMinAge($value)
        {
            $this->min_age = $value;
            return $this;
        }    
        /**
         * Set max_age
         */
        public function setMaxAge($value)
        {
            $this->max_age = $value;
            return $this;
        }    
        /**
         * Set religion_id
         */
        public function setReligionId($value)
        {
            $this->religion_id = $value;
            return $this;
        }    
        /**
         * Set caste_id
         */
        public function setCasteId($value)
        {
            $this->caste_id = $value;
            return $this;
        }    
        /**
         * Set district_id
         */
        public function setDistrictId($value)
        {
            $this->district_id = $value;
            return $this;
        }    
        /**
         * Set marital_status
         */
        public function setMaritalStatus($value)
        {
            $this->marital_status = $value;
            return $this;
        }    
        /**
         * Set min_height
         */
        public function setMinHeight($value)
        {
            $this->min_height = $value;
            return $this;
        }    
        /**
         * Set max_height
         */
        public function setMaxHeight($value)
        {
            $this->max_height = $value;
            return $this;
        }    
        /**
         * Set education
         */
        public function setEducation($value)
        {
            $this->education = $value;
            return $this;
        }    
        /**
         * Set occupation
         */
        public function setOccupation($value)
        {
            $this->occupation = $value;
            return $this;
        }    
        /**
         * Set drinking
         */
        public function setDrinking($value)
        {
            $this->drinking = $value;
            return $this;
        }    
        /**
         * Set smoking
         */
        public function setSmoking($value)
        {
            $this->smoking = $value;
            return $this;
        }
}