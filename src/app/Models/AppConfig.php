<?php
namespace App\Models;
use App\Core\Database;

class AppConfig {
    // Ambil satu value
    public static function get($key, $default = '') {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        $res = $stmt->fetch();
        return $res ? $res['setting_value'] : $default;
    }

    // Ambil semua (untuk form edit)
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM settings");
        $results = $stmt->fetchAll();
        
        $config = [];
        foreach($results as $r) {
            $config[$r['setting_key']] = $r['setting_value'];
        }
        return $config;
    }

    // Update
    public static function set($key, $value) {
        $db = Database::getInstance();
        // Insert or Update (Upsert logic via REPLACE INTO or ON DUPLICATE)
        $db->query("REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
    }
}
