-- Penilaian Karakter / Adab Asrama
CREATE TABLE IF NOT EXISTS boarding_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    category ENUM('IBADAH', 'ADAB', 'KEBERSIHAN', 'BAHASA') NOT NULL,
    predicate ENUM('SANGAT BAIK', 'BAIK', 'CUKUP', 'KURANG') DEFAULT 'BAIK',
    description TEXT NULL COMMENT 'Catatan Musyrif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Seed Contoh Data (Opsional, agar tidak kosong saat tes)
-- Nanti diisi via menu input nilai asrama (kita skip UI inputnya agar fokus ke Output Rapor dulu)
