<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class User {
    public static function findByUsername($username) {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT users.*, roles.name as role_name, roles.slug as role_slug 
            FROM users 
            JOIN roles ON users.role_id = roles.id 
            WHERE users.username = :username AND users.status = 'active'
        ", ['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT users.*, roles.name as role_name 
            FROM users 
            JOIN roles ON users.role_id = roles.id 
            ORDER BY users.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = Database::getInstance();
        $sql = "INSERT INTO users (name, username, email, password, role_id, status) VALUES (?, ?, ?, ?, ?, ?)";
        $db->query($sql, [
            $data['name'], $data['username'], $data['email'], 
            password_hash($data['password'], PASSWORD_BCRYPT), 
            $data['role_id'], 'active'
        ]);
    }
    
    // Fungsi Hapus User (Soft Delete disarankan, tapi Hard Delete dulu untuk fase ini)
    public static function delete($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM users WHERE id = :id", ['id' => $id]);
    }
}
