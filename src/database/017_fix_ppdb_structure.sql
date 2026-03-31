-- 1. Perbaiki Tabel Jalur (Tracks) - Tambah kolom description
-- Menggunakan prosedur agar tidak error jika kolom sudah ada
SET @dbname = DATABASE();
SET @tablename = "ppdb_tracks";
SET @columnname = "description";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE ppdb_tracks ADD COLUMN description TEXT NULL;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Buat Tabel Periode (Batches) yang Hilang
CREATE TABLE IF NOT EXISTS ppdb_batches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Gelombang 1, Gelombang 2',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Seed Data Awal Periode (Jika Kosong)
INSERT INTO ppdb_batches (name, start_date, end_date, is_active)
SELECT 'Gelombang 1 (2025/2026)', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 MONTH), 1
WHERE NOT EXISTS (SELECT * FROM ppdb_batches);
