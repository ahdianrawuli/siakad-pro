<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class PpdbConfig {
    // --- PERIODE ---
    public static function getAllPeriods() {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM ppdb_periods ORDER BY start_date DESC")->fetchAll();
    }

    public static function createPeriod($data) {
        $db = Database::getInstance();
        // Jika periode baru aktif, nonaktifkan yang lain
        if(isset($data['is_active']) && $data['is_active'] == 1) {
            $db->query("UPDATE ppdb_periods SET is_active = 0");
        }

        $sql = "INSERT INTO ppdb_periods (name, start_date, end_date, description, is_active) VALUES (?, ?, ?, ?, ?)";
        $db->query($sql, [
            $data['name'], $data['start_date'], $data['end_date'], 
            $data['description'], $data['is_active'] ?? 0
        ]);
    }

    public static function togglePeriod($id) {
        $db = Database::getInstance();
        // Set semua jadi 0 dulu
        $db->query("UPDATE ppdb_periods SET is_active = 0");
        // Set yang dipilih jadi 1
        $db->query("UPDATE ppdb_periods SET is_active = 1 WHERE id = ?", [$id]);
    }

    // --- JALUR (TRACKS) ---
    public static function getAllTracks() {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM ppdb_tracks ORDER BY level, name")->fetchAll();
    }

    public static function createTrack($data) {
        $db = Database::getInstance();
        $sql = "INSERT INTO ppdb_tracks (name, level, code, quota) VALUES (?, ?, ?, ?)";
        $db->query($sql, [
            $data['name'], $data['level'], $data['code'], $data['quota']
        ]);
    }
}
