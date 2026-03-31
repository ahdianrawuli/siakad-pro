<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Perbaikan Menu Rapor ---\n";

try {
    $db = Database::getInstance();

    // 1. Cari atau Buat Parent Menu 'Rapor'
    // Kita cari yang paling mungkin dimaksud sebagai parent rapor
    $parent = $db->query("SELECT id, title FROM menus WHERE title LIKE '%Rapor%' OR title LIKE '%Laporan%' ORDER BY id ASC LIMIT 1")->fetch();

    if (!$parent) {
        // Jika benar-benar tidak ada, buat baru
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (NULL, 'Rapor / Laporan', '#', 'printer', 60, 1)");
        $parentId = $db->getConnection()->lastInsertId();
        echo "[CREATE] Parent Menu 'Rapor / Laporan' dibuat (ID: $parentId).\n";
    } else {
        $parentId = $parent['id'];
        echo "[FOUND] Parent Menu ditemukan: '{$parent['title']}' (ID: $parentId).\n";
    }

    // 2. FORCE Permission untuk Parent Menu (PENTING!)
    // Beri akses ke Admin (1) dan Guru (3)
    $roles = [1, 3]; 
    foreach ($roles as $rid) {
        $check = $db->query("SELECT * FROM role_menus WHERE role_id = ? AND menu_id = ?", [$rid, $parentId])->fetch();
        if (!$check) {
            $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$rid, $parentId]);
            echo "[FIX] Akses Parent Menu diberikan ke Role ID $rid.\n";
        }
    }

    // 3. Pastikan Sub-Menu Terhubung ke Parent yang Benar
    $subMenus = [
        '/report/boarding' => 'Rapor Asrama',
        '/extracurricular' => 'Rapor Ekstrakurikuler',
        '/report/print' => 'Cetak Rapor Akademik' // Jika ada modul rapor lama
    ];

    foreach ($subMenus as $url => $title) {
        $menu = $db->query("SELECT id FROM menus WHERE url = ?", [$url])->fetch();
        if ($menu) {
            // Update Parent ID agar ngumpul jadi satu
            $db->query("UPDATE menus SET parent_id = ? WHERE id = ?", [$parentId, $menu['id']]);
            echo "[UPDATE] Menu '$title' dipindahkan ke bawah Parent ID $parentId.\n";

            // Pastikan Permission Sub-Menu ada
            foreach ($roles as $rid) {
                $checkSub = $db->query("SELECT * FROM role_menus WHERE role_id = ? AND menu_id = ?", [$rid, $menu['id']])->fetch();
                if (!$checkSub) {
                    $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$rid, $menu['id']]);
                    echo "[FIX] Akses '$title' diberikan ke Role ID $rid.\n";
                }
            }
        } else {
            // Jika menu belum ada (kasus file 010 gagal total), buat baru
            $icon = ($url == '/report/boarding') ? 'moon' : 'activity';
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, ?, ?, ?, 99, 1)", [$parentId, $title, $url, $icon]);
            $newId = $db->getConnection()->lastInsertId();
            
            foreach ($roles as $rid) {
                $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$rid, $newId]);
            }
            echo "[CREATE] Menu '$title' dibuat ulang.\n";
        }
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Perbaikan Selesai. Silakan Refresh Browser ---\n";
