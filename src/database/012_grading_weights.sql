-- Tabel Konfigurasi Bobot Nilai (Per Tahun Ajaran)
CREATE TABLE IF NOT EXISTS grading_weights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id INT NOT NULL,
    weight_daily INT DEFAULT 40 COMMENT 'Persentase Nilai Harian (UH/Tugas)',
    weight_uts INT DEFAULT 30 COMMENT 'Persentase UTS',
    weight_uas INT DEFAULT 30 COMMENT 'Persentase UAS',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);

-- Seed Default untuk Tahun Ajaran Aktif (Asumsi ID 1)
INSERT INTO grading_weights (academic_year_id, weight_daily, weight_uts, weight_uas) 
SELECT id, 40, 30, 30 FROM academic_years WHERE is_active = 1 LIMIT 1;
