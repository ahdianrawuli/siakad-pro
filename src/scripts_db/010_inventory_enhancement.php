<?php
require_once __DIR__ . '/bootstrap.php';
use App\Core\Database;

echo "--- Migrasi: Pengembangan Modul Inventaris ---\n";

try {
    $db = Database::getInstance();

    // 1. Tabel Riwayat Mutasi Kondisi
    $db->query("CREATE TABLE IF NOT EXISTS inventory_mutations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_id INT NOT NULL,
        old_condition ENUM('BAIK','RUSAK_RINGAN','RUSAK_BERAT','HILANG') NOT NULL,
        new_condition ENUM('BAIK','RUSAK_RINGAN','RUSAK_BERAT','HILANG') NOT NULL,
        notes TEXT,
        changed_by INT NOT NULL,
        changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
        FOREIGN KEY (changed_by) REFERENCES users(id)
    ) ENGINE=INNODB");
    echo "[OK] Tabel 'inventory_mutations' siap.\n";

    // 2. Tabel Peminjaman
    $db->query("CREATE TABLE IF NOT EXISTS inventory_loans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_id INT NOT NULL,
        borrower_name VARCHAR(150) NOT NULL,
        borrower_role VARCHAR(50) NOT NULL COMMENT 'Guru, Staf, Santri',
        quantity INT DEFAULT 1,
        loan_date DATE NOT NULL,
        due_date DATE NOT NULL,
        return_date DATE NULL,
        status ENUM('DIPINJAM','DIKEMBALIKAN','TERLAMBAT') DEFAULT 'DIPINJAM',
        notes TEXT,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id)
    ) ENGINE=INNODB");
    echo "[OK] Tabel 'inventory_loans' siap.\n";

    // 3. Tambah kolom notif_sent ke inventory_items (untuk tracking notif kondisi rusak)
    $col = $db->query("SHOW COLUMNS FROM inventory_items LIKE 'notif_sent'")->fetch();
    if (!$col) {
        $db->query("ALTER TABLE inventory_items ADD COLUMN notif_sent TINYINT(1) DEFAULT 0 COMMENT 'Sudah kirim notif WA kondisi rusak/hilang'");
        echo "[OK] Kolom 'notif_sent' ditambahkan ke inventory_items.\n";
    } else {
        echo "[SKIP] Kolom 'notif_sent' sudah ada.\n";
    }

    echo "--- Migrasi Selesai ---\n";
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}
