-- 1. Tahun Ajaran (Academic Years)
CREATE TABLE IF NOT EXISTS academic_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL COMMENT '2024/2025',
    semester ENUM('Ganjil', 'Genap') NOT NULL,
    is_active TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Mata Pelajaran (Subjects)
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    type ENUM('NASIONAL', 'PESANTREN', 'MULOK') DEFAULT 'NASIONAL',
    kkm INT DEFAULT 75,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Jadwal Pelajaran (Schedules)
CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id INT NOT NULL,
    classroom_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL, -- Link ke tabel Users (Role Guru)
    day ENUM('SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- 4. Tabel Nilai (Grades)
CREATE TABLE IF NOT EXISTS student_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    schedule_id INT NOT NULL,
    type ENUM('UH1', 'UH2', 'UTS', 'UAS', 'TUGAS') NOT NULL,
    score DECIMAL(5, 2) NOT NULL,
    notes TEXT NULL,
    created_by INT NOT NULL, -- Guru yang input
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (schedule_id) REFERENCES schedules(id)
);

-- Seed Data Awal
INSERT INTO academic_years (name, semester, is_active) VALUES ('2025/2026', 'Ganjil', 1);
INSERT INTO subjects (code, name, type) VALUES 
('MTK', 'Matematika', 'NASIONAL'),
('FQH', 'Fiqih', 'PESANTREN'),
('ARB', 'Bahasa Arab', 'PESANTREN');
