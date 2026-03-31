<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Perbaikan Menyeluruh Menu Kepegawaian ---\n";

try {
    $db = Database::getInstance();
    $roleId = 1; // Super Admin

    // 1. PASTIKAN PARENT MENU ADA
    $parent = $db->query("SELECT id FROM menus WHERE title = 'Kepegawaian'")->fetch();
    
    if (!$parent) {
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (NULL, 'Kepegawaian', '#', 'briefcase', 15, 1)");
        $parentId = $db->getConnection()->lastInsertId();
        echo "[CREATE] Parent Menu 'Kepegawaian' dibuat (ID: $parentId).\n";
    } else {
        $parentId = $parent['id'];
        echo "[OK] Parent Menu 'Kepegawaian' sudah ada (ID: $parentId).\n";
    }

    // 2. DAFTAR SUB-MENU YANG WAJIB ADA
    $subMenus = [
        ['title' => 'Master Jabatan',      'url' => '/staff/positions', 'icon' => 'tag'],
        ['title' => 'Data Staff',          'url' => '/staff/members',   'icon' => 'users'],
        ['title' => 'Struktur Organisasi', 'url' => '/staff/structure', 'icon' => 'share-2']
    ];

    // 3. CEK & BUAT SUB-MENU SATU PER SATU
    foreach ($subMenus as $menu) {
        // Cek berdasarkan URL agar lebih akurat
        $check = $db->query("SELECT id FROM menus WHERE url = ? AND parent_id = ?", [$menu['url'], $parentId])->fetch();
        
        if (!$check) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, ?, ?, ?, 1, 1)", 
                        [$parentId, $menu['title'], $menu['url'], $menu['icon']]);
            $subId = $db->getConnection()->lastInsertId();
            echo "[CREATE] Sub-menu '{$menu['title']}' dibuat.\n";
        } else {
            $subId = $check['id'];
            echo "[OK] Sub-menu '{$menu['title']}' sudah ada.\n";
        }

        // 4. BERI AKSES KE SUPER ADMIN (Untuk Sub-Menu ini)
        $permCheck = $db->query("SELECT * FROM role_menus WHERE role_id = ? AND menu_id = ?", [$roleId, $subId])->fetch();
        if (!$permCheck) {
            $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$roleId, $subId]);
            echo "   -> [FIX] Izin akses diberikan.\n";
        }
    }

    // 5. BERI AKSES KE PARENT MENU
    $permParent = $db->query("SELECT * FROM role_menus WHERE role_id = ? AND menu_id = ?", [$roleId, $parentId])->fetch();
    if (!$permParent) {
        $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$roleId, $parentId]);
        echo "[FIX] Izin akses Parent Menu diberikan.\n";
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Selesai. Silakan Refresh Browser / Login Ulang ---\n";

