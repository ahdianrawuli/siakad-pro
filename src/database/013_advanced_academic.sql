-- 1. Update Tabel Kelas (Tambah Wali Kelas)
ALTER TABLE classrooms ADD COLUMN homeroom_teacher_id INT NULL;
ALTER TABLE classrooms ADD CONSTRAINT fk_homeroom FOREIGN KEY (homeroom_teacher_id) REFERENCES users(id) ON DELETE SET NULL;

-- 2. Kalender Akademik
CREATE TABLE IF NOT EXISTS academic_calendar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    type ENUM('LIBUR', 'UJIAN', 'KEGIATAN') DEFAULT 'KEGIATAN',
    color VARCHAR(20) DEFAULT '#3788d8', -- Untuk warna di UI
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Bank Soal (Repository Ujian)
CREATE TABLE IF NOT EXISTS exam_banks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    file_path VARCHAR(255) NOT NULL, -- Upload PDF/Docx
    type ENUM('UTS', 'UAS', 'QUIZ', 'LATIHAN') DEFAULT 'LATIHAN',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- 4. Jurnal Kitab (Khusus Pesantren)
-- Mencatat progres ngaji kitab (Bab/Halaman)
CREATE TABLE IF NOT EXISTS kitab_journals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    class_name VARCHAR(100) NOT NULL COMMENT 'Nama Halaqah / Kelas Kitab',
    kitab_name VARCHAR(100) NOT NULL COMMENT 'Contoh: Jurumiyah, Taqrib',
    date DATE NOT NULL,
    start_page INT DEFAULT 0,
    end_page INT DEFAULT 0,
    chapter VARCHAR(200) NULL COMMENT 'Bab yang dibahas',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
