<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class Menu {
    public static function getAll() {
        $db = Database::getInstance();
        // Ambil menu dan urutkan
        $stmt = $db->query("SELECT * FROM menus ORDER BY parent_id ASC, order_num ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = Database::getInstance();
        $sql = "INSERT INTO menus (parent_id, title, url, icon, order_num) VALUES (?, ?, ?, ?, ?)";
        $db->query($sql, [
            $data['parent_id'] ?: NULL,
            $data['title'],
            $data['url'],
            $data['icon'],
            $data['order_num']
        ]);
    }
}
