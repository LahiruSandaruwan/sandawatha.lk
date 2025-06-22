<?php
/**
 * Horoscope Model
 * Sandawatha.lk - Sri Lankan Matrimonial Site
 */

class Horoscope
{
    // Database connection
    private $pdo;
    
    // Table name
    private $table = ''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', 'Horoscope')).''.'s';

    // Properties
        private $id;
        private $user_id;
        private $birth_time;
        private $birth_place;
        private $nakatha;
        private $gana;
        private $zodiac;
        private $rashi;
        private $nekatha;
        private $horoscope_image;

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
         * Get birth_time
         */
        public function getBirthTime()
        {
            return $this->birth_time;
        }    
        /**
         * Get birth_place
         */
        public function getBirthPlace()
        {
            return $this->birth_place;
        }    
        /**
         * Get nakatha
         */
        public function getNakatha()
        {
            return $this->nakatha;
        }    
        /**
         * Get gana
         */
        public function getGana()
        {
            return $this->gana;
        }    
        /**
         * Get zodiac
         */
        public function getZodiac()
        {
            return $this->zodiac;
        }    
        /**
         * Get rashi
         */
        public function getRashi()
        {
            return $this->rashi;
        }    
        /**
         * Get nekatha
         */
        public function getNekatha()
        {
            return $this->nekatha;
        }    
        /**
         * Get horoscope_image
         */
        public function getHoroscopeImage()
        {
            return $this->horoscope_image;
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
         * Set birth_time
         */
        public function setBirthTime($value)
        {
            $this->birth_time = $value;
            return $this;
        }    
        /**
         * Set birth_place
         */
        public function setBirthPlace($value)
        {
            $this->birth_place = $value;
            return $this;
        }    
        /**
         * Set nakatha
         */
        public function setNakatha($value)
        {
            $this->nakatha = $value;
            return $this;
        }    
        /**
         * Set gana
         */
        public function setGana($value)
        {
            $this->gana = $value;
            return $this;
        }    
        /**
         * Set zodiac
         */
        public function setZodiac($value)
        {
            $this->zodiac = $value;
            return $this;
        }    
        /**
         * Set rashi
         */
        public function setRashi($value)
        {
            $this->rashi = $value;
            return $this;
        }    
        /**
         * Set nekatha
         */
        public function setNekatha($value)
        {
            $this->nekatha = $value;
            return $this;
        }    
        /**
         * Set horoscope_image
         */
        public function setHoroscopeImage($value)
        {
            $this->horoscope_image = $value;
            return $this;
        }
}