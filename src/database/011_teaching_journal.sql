-- 1. Header Jurnal Mengajar
CREATE TABLE IF NOT EXISTS teaching_journals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NOT NULL, -- Terhubung ke Jadwal Tetap
    date DATE NOT NULL,
    topic VARCHAR(255) NOT NULL COMMENT 'Materi/Bahasan Hari Ini',
    notes TEXT NULL COMMENT 'Catatan kejadian khusus di kelas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id)
);

-- 2. Absensi Per Mata Pelajaran (Linked ke Jurnal)
CREATE TABLE IF NOT EXISTS journal_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('H', 'S', 'I', 'A') DEFAULT 'H',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (journal_id) REFERENCES teaching_journals(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id)
);
