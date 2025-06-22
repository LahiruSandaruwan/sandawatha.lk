<?php
/**
 * Seeder: Districts
 */

class DistrictsSeeder
{
    /**
     * Run the seeder
     */
    public function run(PDO $pdo)
    {
        $districts = [
            // Western Province
            ['Colombo', 'කොළඹ', 'கொழும்பு', 'Western'],
            ['Gampaha', 'ගම්පහ', 'கம்பஹா', 'Western'],
            ['Kalutara', 'කළුතර', 'களுத்துறை', 'Western'],
            
            // Central Province
            ['Kandy', 'මහනුවර', 'கண்டி', 'Central'],
            ['Matale', 'මාතලේ', 'மாத்தளை', 'Central'],
            ['Nuwara Eliya', 'නුවරඑළිය', 'நுவரெலியா', 'Central'],
            
            // Southern Province
            ['Galle', 'ගාල්ල', 'காலி', 'Southern'],
            ['Matara', 'මාතර', 'மாத்தறை', 'Southern'],
            ['Hambantota', 'හම්බන්තොට', 'அம்பாந்தோட்டை', 'Southern'],
            
            // Northern Province
            ['Jaffna', 'යාපනය', 'யாழ்ப்பாணம்', 'Northern'],
            ['Kilinochchi', 'කිලිනොච්චි', 'கிளிநொச்சி', 'Northern'],
            ['Mannar', 'මන්නාරම', 'மன்னார்', 'Northern'],
            ['Vavuniya', 'වවුනියාව', 'வவுனியா', 'Northern'],
            ['Mullaitivu', 'මුලතිව්', 'முல்லைத்தீவு', 'Northern'],
            
            // Eastern Province
            ['Batticaloa', 'මඩකලපුව', 'மட்டக்களப்பு', 'Eastern'],
            ['Ampara', 'අම්පාර', 'அம்பாறை', 'Eastern'],
            ['Trincomalee', 'ත්‍රිකුණාමලය', 'திருகோணமலை', 'Eastern'],
            
            // North Western Province
            ['Kurunegala', 'කුරුණෑගල', 'குருநாகல்', 'North Western'],
            ['Puttalam', 'පුත්තලම', 'புத்தளம்', 'North Western'],
            
            // North Central Province
            ['Anuradhapura', 'අනුරාධපුරය', 'அனுராதபுரம்', 'North Central'],
            ['Polonnaruwa', 'පොළොන්නරුව', 'பொலன்னறுவை', 'North Central'],
            
            // Uva Province
            ['Badulla', 'බදුල්ල', 'பதுளை', 'Uva'],
            ['Monaragala', 'මොණරාගල', 'மொனராகலை', 'Uva'],
            
            // Sabaragamuwa Province
            ['Ratnapura', 'රත්නපුර', 'இரத்தினபுரி', 'Sabaragamuwa'],
            ['Kegalle', 'කෑගල්ල', 'கேகாலை', 'Sabaragamuwa']
        ];

        $stmt = $pdo->prepare("INSERT INTO districts (name, name_si, name_ta, province) VALUES (?, ?, ?, ?)");

        foreach ($districts as $district) {
            $stmt->execute($district);
        }

        return true;
    }
} 