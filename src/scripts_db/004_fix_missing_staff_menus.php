<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Perbaikan Menu Staff yang Hilang ---\n";

try {
    $db = Database::getInstance();
    $roleId = 1; // Super Admin

    // 1. Pastikan Parent Menu 'Kepegawaian' Ada
    $parent = $db->query("SELECT id FROM menus WHERE title = 'Kepegawaian'")->fetch();
    if (!$parent) {
        die("[ERROR] Menu Induk 'Kepegawaian' tidak ditemukan. Jalankan script 002 dulu.\n");
    }
    $parentId = $parent['id'];
    echo "[OK] Parent Menu ID: $parentId\n";

    // 2. Daftar Sub-Menu yang Wajib Ada
    $requiredMenus = [
        ['title' => 'Master Jabatan',      'url' => '/staff/positions', 'icon' => 'tag'],
        ['title' => 'Data Staff',          'url' => '/staff/members',   'icon' => 'users'],
        ['title' => 'Struktur Organisasi', 'url' => '/staff/structure', 'icon' => 'share-2']
    ];

    foreach ($requiredMenus as $m) {
        // Cek apakah menu sudah ada berdasarkan URL
        $check = $db->query("SELECT id FROM menus WHERE url = ?", [$m['url']])->fetch();
        
        if (!$check) {
            // INSERT jika belum ada
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, ?, ?, ?, 1, 1)", 
                        [$parentId, $m['title'], $m['url'], $m['icon']]);
            $newId = $db->getConnection()->lastInsertId();
            echo "[CREATED] Menu '{$m['title']}' berhasil dibuat (ID: $newId).\n";
            
            // Beri Akses
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$roleId, $newId]);
            echo "   -> Akses diberikan ke Super Admin.\n";
        } else {
            // Jika sudah ada, pastikan aksesnya ada
            $menuId = $check['id'];
            echo "[EXIST] Menu '{$m['title']}' sudah ada (ID: $menuId).\n";
            
            $perm = $db->query("SELECT * FROM role_menus WHERE role_id = ? AND menu_id = ?", [$roleId, $menuId])->fetch();
            if (!$perm) {
                $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$roleId, $menuId]);
                echo "   -> [FIX] Akses yang hilang telah ditambahkan.\n";
            } else {
                echo "   -> [OK] Akses aman.\n";
            }
        }
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Selesai. Silakan Refresh Browser ---\n";

