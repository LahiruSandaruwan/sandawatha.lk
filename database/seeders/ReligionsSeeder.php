<?php
/**
 * Seeder: Religions
 */

class ReligionsSeeder
{
    /**
     * Run the seeder
     */
    public function run(PDO $pdo)
    {
        $religions = [
            ['Buddhist', 'බෞද්ධ', 'பௌத்தம்'],
            ['Hindu', 'හින්දු', 'இந்து'],
            ['Christian', 'ක්‍රිස්තියානි', 'கிறிஸ்தவம்'],
            ['Islam', 'ඉස්ලාම්', 'இஸ்லாம்']
        ];

        $stmt = $pdo->prepare("INSERT INTO religions (name, name_si, name_ta) VALUES (?, ?, ?)");

        foreach ($religions as $religion) {
            $stmt->execute($religion);
        }

        return true;
    }
} 