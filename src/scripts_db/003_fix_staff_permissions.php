<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Perbaikan Permission Menu Kepegawaian ---\n";

try {
    $db = Database::getInstance();
    $roleId = 1; // ID untuk Super Admin

    // 1. Cari ID Menu Parent 'Kepegawaian'
    $parentMenu = $db->query("SELECT id, title FROM menus WHERE title = 'Kepegawaian'")->fetch();

    if ($parentMenu) {
        $parentId = $parentMenu['id'];
        echo "[INFO] Menu 'Kepegawaian' ditemukan (ID: $parentId).\n";

        // 2. Cek & Beri Akses ke Parent Menu
        $checkParent = $db->query("SELECT * FROM role_menus WHERE role_id = ? AND menu_id = ?", [$roleId, $parentId])->fetch();
        
        if (!$checkParent) {
            $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$roleId, $parentId]);
            echo "[FIX] Akses diberikan ke Super Admin untuk menu: {$parentMenu['title']}.\n";
        } else {
            echo "[OK] Akses parent sudah ada.\n";
        }

        // 3. Cek & Beri Akses ke Sub-Menu (Jabatan, Staff, Struktur)
        $subMenus = $db->query("SELECT id, title FROM menus WHERE parent_id = ?", [$parentId])->fetchAll();
        
        if ($subMenus) {
            foreach ($subMenus as $sub) {
                $checkSub = $db->query("SELECT * FROM role_menus WHERE role_id = ? AND menu_id = ?", [$roleId, $sub['id']])->fetch();
                
                if (!$checkSub) {
                    $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$roleId, $sub['id']]);
                    echo "[FIX] Akses diberikan untuk sub-menu: {$sub['title']}.\n";
                } else {
                    echo "[OK] Akses sub-menu '{$sub['title']}' sudah ada.\n";
                }
            }
        } else {
            echo "[WARNING] Tidak ditemukan sub-menu untuk Kepegawaian.\n";
        }

    } else {
        echo "[ERROR] Menu 'Kepegawaian' TIDAK DITEMUKAN di database.\n";
        echo "Solusi: Jalankan ulang script '002_create_staff_modules.php' terlebih dahulu.\n";
    }

} catch (Exception $e) {
    echo "[ERROR] Terjadi kesalahan: " . $e->getMessage() . "\n";
}

echo "--- Perbaikan Selesai ---\n";
echo "Silakan Logout dan Login kembali untuk melihat perubahan.\n";

