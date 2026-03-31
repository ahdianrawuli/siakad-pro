<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Pemindahan Menu Rapor ke Level Utama ---\n";

try {
    $db = Database::getInstance();

    // 1. Buat Menu Parent Baru 'Rapor / Laporan' di Root (parent_id NULL)
    // Pastikan belum ada menu dengan nama ini yang parent_id-nya NULL
    $checkParent = $db->query("SELECT id FROM menus WHERE title = 'Rapor / Laporan' AND parent_id IS NULL")->fetch();

    if (!$checkParent) {
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (NULL, 'Rapor / Laporan', '#', 'printer', 55, 1)"); // Urutan 55 (antara Akademik & Keuangan)
        $parentId = $db->getConnection()->lastInsertId();
        echo "[CREATE] Menu Parent Utama 'Rapor / Laporan' dibuat (ID: $parentId).\n";
        
        // Beri Akses ke Admin & Guru
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$parentId]); // Admin
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (3, ?)", [$parentId]); // Guru
    } else {
        $parentId = $checkParent['id'];
        echo "[INFO] Menu Parent Utama sudah ada (ID: $parentId).\n";
    }

    // 2. Pindahkan Sub-Menu yang Salah Kamar (ID 220, 221, 222) ke Parent Baru
    // Berdasarkan data user: 220 (Rapor Asrama), 221 (Rapor Ekstra), 222 (Cetak Rapor)
    // Kita cari berdasarkan URL biar lebih aman
    $urls = [
        '/report/boarding',
        '/extracurricular',
        '/report/print'
    ];

    foreach ($urls as $url) {
        $menu = $db->query("SELECT id, title FROM menus WHERE url = ?", [$url])->fetch();
        if ($menu) {
            $db->query("UPDATE menus SET parent_id = ? WHERE id = ?", [$parentId, $menu['id']]);
            echo "[MOVE] Menu '{$menu['title']}' dipindahkan ke Parent ID $parentId.\n";
            
            // Pastikan permission sub-menu aman
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$menu['id']]);
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (3, ?)", [$menu['id']]);
        }
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Perbaikan Selesai. Silakan Refresh Browser ---\n";
