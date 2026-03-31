-- 1. Rekam Medis (Poskestren)
CREATE TABLE IF NOT EXISTS health_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    complaint TEXT NOT NULL COMMENT 'Keluhan: Pusing, Demam',
    diagnosis VARCHAR(200) NULL COMMENT 'Diagnosa Petugas',
    treatment TEXT NULL COMMENT 'Tindakan/Obat yang diberikan',
    status ENUM('RAWAT_JALAN', 'RAWAT_INAP', 'RUJUK_RS') DEFAULT 'RAWAT_JALAN',
    officer_id INT NOT NULL COMMENT 'Petugas yang menangani',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (officer_id) REFERENCES users(id)
);

-- 2. Monitoring Tahfidz / Tilawah
CREATE TABLE IF NOT EXISTS worship_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    teacher_id INT NOT NULL, -- Musyrif/Penyimak
    date DATE NOT NULL,
    type ENUM('ZIYADAH', 'MUROJAAH', 'TILAWAH') NOT NULL COMMENT 'Ziyadah=Hafalan Baru, Murojaah=Ulang',
    surah_name VARCHAR(50) NOT NULL COMMENT 'Nama Surat',
    verses VARCHAR(50) NOT NULL COMMENT 'Ayat berapa s.d berapa',
    grade ENUM('A', 'B', 'C') DEFAULT 'A' COMMENT 'Kelancaran',
    note VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);
