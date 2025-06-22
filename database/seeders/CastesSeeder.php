<?php
/**
 * Seeder: Castes
 */

class CastesSeeder
{
    /**
     * Run the seeder
     */
    public function run(PDO $pdo)
    {
        // Get religion IDs
        $stmt = $pdo->prepare("SELECT id, name FROM religions WHERE name IN ('Buddhist', 'Hindu')");
        $stmt->execute();
        $religions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $religionIds = array_column($religions, 'id', 'name');

        // Buddhist castes
        $buddhistCastes = [
            ['Govigama', 'ගොවිගම', 'கோவிகம'],
            ['Karava', 'කරාව', 'கராவ'],
            ['Salagama', 'සලගම', 'சலகம'],
            ['Durava', 'දුරාව', 'துராவ']
        ];

        // Hindu castes
        $hinduCastes = [
            ['Brahmin', 'බ්‍රාහ්මණ', 'பிராமணர்'],
            ['Kshatriya', 'ක්ෂත්‍රිය', 'க்ஷத்திரியர்'],
            ['Vaisya', 'වෛශ්‍ය', 'வைசியர்'],
            ['Sudra', 'ශූද්‍ර', 'சூத்திரர்']
        ];

        $stmt = $pdo->prepare("INSERT INTO castes (religion_id, name, name_si, name_ta) VALUES (?, ?, ?, ?)");

        // Insert Buddhist castes
        foreach ($buddhistCastes as $caste) {
            $stmt->execute([$religionIds['Buddhist'], ...$caste]);
        }

        // Insert Hindu castes
        foreach ($hinduCastes as $caste) {
            $stmt->execute([$religionIds['Hindu'], ...$caste]);
        }

        return true;
    }
} 