-- 1. Tabel Periode PPDB [cite: 130-132]
CREATE TABLE IF NOT EXISTS ppdb_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Contoh: Gelombang 1 T.A 2024/2025',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 0, -- Hanya satu periode yang boleh aktif
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Jalur Pendaftaran [cite: 143-150]
CREATE TABLE IF NOT EXISTS ppdb_tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL COMMENT 'Reguler, Prestasi, Tahfidz',
    level ENUM('MTS', 'MA', 'PDF') NOT NULL COMMENT 'Jenjang Pendidikan',
    code VARCHAR(10) NOT NULL COMMENT 'Kode Jalur: TR, TP',
    quota INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Seeding Data Awal (Contoh)
INSERT INTO ppdb_periods (name, start_date, end_date, is_active) 
VALUES ('Gelombang 1 T.A 2025/2026', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1);

INSERT INTO ppdb_tracks (name, level, code, quota) VALUES 
('Reguler', 'MTS', 'REG-MTS', 100),
('Prestasi', 'MA', 'PRE-MA', 50);

-- 4. Tambah Menu PPDB Konfigurasi ke Tabel Menu (Agar muncul di Sidebar)
INSERT INTO menus (parent_id, title, url, icon, order_num) 
SELECT id, 'Konfigurasi PPDB', '/ppdb/settings', 'cogs', 99 
FROM menus WHERE title = 'PPDB' LIMIT 1;
