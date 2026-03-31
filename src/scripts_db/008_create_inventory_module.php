<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Migrasi: Modul Sarana Prasarana (Inventaris) ---\n";

try {
    $db = Database::getInstance();

    // 1. Tabel Master Kategori Barang (Opsional, kita gabung di item saja biar simpel atau terpisah)
    // Kita buat terpisah agar rapi.
    $sqlCat = "CREATE TABLE IF NOT EXISTS inventory_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;";
    $db->query($sqlCat);
    
    // Seed Kategori Default
    $cats = ['Elektronik', 'Mebel / Furniture', 'Kendaraan', 'Alat Kebersihan', 'Buku / Pustaka', 'Bangunan'];
    foreach($cats as $c) {
        $check = $db->query("SELECT id FROM inventory_categories WHERE name = ?", [$c])->fetch();
        if (!$check) $db->query("INSERT INTO inventory_categories (name) VALUES (?)", [$c]);
    }
    echo "[OK] Tabel 'inventory_categories' siap.\n";

    // 2. Tabel Item Inventaris
    $sqlInv = "CREATE TABLE IF NOT EXISTS inventory_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NULL,
        code VARCHAR(50) UNIQUE COMMENT 'Kode Barang / Barcode',
        name VARCHAR(200) NOT NULL,
        brand VARCHAR(100) NULL COMMENT 'Merk/Model',
        acquisition_date DATE NULL COMMENT 'Tanggal Pengadaan',
        source_fund VARCHAR(100) NULL COMMENT 'Asal Dana (BOS, Yayasan, Hibah)',
        price DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga Satuan',
        quantity INT DEFAULT 1,
        condition_status ENUM('BAIK', 'RUSAK_RINGAN', 'RUSAK_BERAT', 'HILANG') DEFAULT 'BAIK',
        location VARCHAR(100) NULL COMMENT 'Lokasi Barang (Lab, Kelas, Kantor)',
        description TEXT,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES inventory_categories(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES users(id)
    ) ENGINE=INNODB;";
    $db->query($sqlInv);
    echo "[OK] Tabel 'inventory_items' siap.\n";

    // 3. Tambah Menu ke Parent 'Keuangan'
    // Cari ID Parent 'Keuangan'
    $parent = $db->query("SELECT id FROM menus WHERE title = 'Keuangan'")->fetch();
    
    if ($parent) {
        $parentId = $parent['id'];
        
        // Cek Menu Inventaris
        $check = $db->query("SELECT id FROM menus WHERE url = '/finance/inventory'")->fetch();
        if (!$check) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, 'Inventaris Aset', '/finance/inventory', 'box', 5, 1)", [$parentId]);
            $newId = $db->getConnection()->lastInsertId();
            
            // Beri akses ke Admin (1) dan Tata Usaha/Keuangan (jika ada role khusus)
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]); 
            
            echo "[CREATE] Menu 'Inventaris Aset' ditambahkan ke Keuangan.\n";
        } else {
            echo "[SKIP] Menu Inventaris sudah ada.\n";
            // Pastikan permission admin ada (self-healing)
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$check['id']]);
        }
    } else {
        echo "[WARNING] Menu Parent 'Keuangan' tidak ditemukan. Pastikan modul Keuangan sudah aktif.\n";
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Migrasi Selesai ---\n";
