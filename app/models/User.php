<?php
/**
 * User Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class User
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'User')).''.'s';

    // Properties
        private $id;
        private $email;
        private $password;
        private $first_name;
        private $last_name;
        private $gender;
        private $date_of_birth;
        private $religion_id;
        private $caste_id;
        private $district_id;
        private $profile_photo;
        private $bio;
        private $occupation;
        private $education;
        private $height;
        private $marital_status;
        private $drinking;
        private $smoking;
        private $is_verified;
        private $is_premium;
        private $last_active;
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
         * Get first_name
         */
        public function getFirstName()
        {
            return $this->first_name;
        }    
        /**
         * Get last_name
         */
        public function getLastName()
        {
            return $this->last_name;
        }    
        /**
         * Get gender
         */
        public function getGender()
        {
            return $this->gender;
        }    
        /**
         * Get date_of_birth
         */
        public function getDateOfBirth()
        {
            return $this->date_of_birth;
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
         * Get profile_photo
         */
        public function getProfilePhoto()
        {
            return $this->profile_photo;
        }    
        /**
         * Get bio
         */
        public function getBio()
        {
            return $this->bio;
        }    
        /**
         * Get occupation
         */
        public function getOccupation()
        {
            return $this->occupation;
        }    
        /**
         * Get education
         */
        public function getEducation()
        {
            return $this->education;
        }    
        /**
         * Get height
         */
        public function getHeight()
        {
            return $this->height;
        }    
        /**
         * Get marital_status
         */
        public function getMaritalStatus()
        {
            return $this->marital_status;
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
        /**
         * Get is_verified
         */
        public function getIsVerified()
        {
            return $this->is_verified;
        }    
        /**
         * Get is_premium
         */
        public function getIsPremium()
        {
            return $this->is_premium;
        }    
        /**
         * Get last_active
         */
        public function getLastActive()
        {
            return $this->last_active;
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
         * Set first_name
         */
        public function setFirstName($value)
        {
            $this->first_name = $value;
            return $this;
        }    
        /**
         * Set last_name
         */
        public function setLastName($value)
        {
            $this->last_name = $value;
            return $this;
        }    
        /**
         * Set gender
         */
        public function setGender($value)
        {
            $this->gender = $value;
            return $this;
        }    
        /**
         * Set date_of_birth
         */
        public function setDateOfBirth($value)
        {
            $this->date_of_birth = $value;
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
         * Set profile_photo
         */
        public function setProfilePhoto($value)
        {
            $this->profile_photo = $value;
            return $this;
        }    
        /**
         * Set bio
         */
        public function setBio($value)
        {
            $this->bio = $value;
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
         * Set education
         */
        public function setEducation($value)
        {
            $this->education = $value;
            return $this;
        }    
        /**
         * Set height
         */
        public function setHeight($value)
        {
            $this->height = $value;
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
        /**
         * Set is_verified
         */
        public function setIsVerified($value)
        {
            $this->is_verified = $value;
            return $this;
        }    
        /**
         * Set is_premium
         */
        public function setIsPremium($value)
        {
            $this->is_premium = $value;
            return $this;
        }    
        /**
         * Set last_active
         */
        public function setLastActive($value)
        {
            $this->last_active = $value;
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