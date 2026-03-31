-- 1. Master Jenis Pelanggaran (Poin)
CREATE TABLE IF NOT EXISTS violation_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Merokok, Terlambat, Berkelahi',
    points INT NOT NULL DEFAULT 5 COMMENT 'Bobot Poin',
    category ENUM('RINGAN', 'SEDANG', 'BERAT') DEFAULT 'RINGAN'
);

-- 2. Catatan Pelanggaran Siswa
CREATE TABLE IF NOT EXISTS student_violations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    violation_type_id INT NOT NULL,
    date DATE NOT NULL,
    note TEXT, -- Kronologi
    reported_by INT NULL, -- Guru pelapor
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (violation_type_id) REFERENCES violation_types(id)
);

-- 3. Catatan Prestasi Siswa
CREATE TABLE IF NOT EXISTS student_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(150) NOT NULL COMMENT 'Juara 1 Lomba Pidato',
    level ENUM('SEKOLAH', 'KECAMATAN', 'KABUPATEN', 'PROVINSI', 'NASIONAL') DEFAULT 'SEKOLAH',
    date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- 4. Log Bimbingan Konseling (BK)
CREATE TABLE IF NOT EXISTS counseling_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    counselor_id INT NOT NULL, -- Guru BK
    date DATE NOT NULL,
    issue VARCHAR(200) NOT NULL COMMENT 'Masalah Akademik/Sosial/Pribadi',
    result TEXT COMMENT 'Hasil konseling / Tindak lanjut',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (counselor_id) REFERENCES users(id)
);

-- Seed Data Pelanggaran
INSERT INTO violation_types (name, points, category) VALUES 
('Terlambat Masuk Sekolah', 5, 'RINGAN'),
('Tidak Membawa Buku Pelajaran', 5, 'RINGAN'),
('Membolos Jam Pelajaran', 20, 'SEDANG'),
('Merokok di Lingkungan Sekolah', 100, 'BERAT'),
('Berkelahi', 75, 'BERAT');
