-- 1. Tabel Kelas / Rombel [cite: 409-415]
CREATE TABLE IF NOT EXISTS classrooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL COMMENT 'Contoh: 7A, 10 IPA 1',
    level VARCHAR(20) NOT NULL COMMENT '7, 8, 9, 10, 11, 12',
    major VARCHAR(50) DEFAULT 'UMUM' COMMENT 'IPA, IPS, MAK, ULYA',
    capacity INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Siswa Aktif (Data Induk) [cite: 154-160]
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL UNIQUE, -- Link ke akun login
    candidate_id INT NULL UNIQUE, -- Link ke data PPDB (History)
    classroom_id INT NULL, -- Kelas saat ini
    
    nis VARCHAR(20) NOT NULL UNIQUE,
    nisn VARCHAR(20) NULL,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    birth_place VARCHAR(50),
    birth_date DATE,
    address TEXT,
    
    parent_name VARCHAR(100),
    parent_phone VARCHAR(20),
    
    status ENUM('ACTIVE', 'GRADUATED', 'MOVED', 'DROPPED') DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE SET NULL
);

-- 3. Tabel Absensi Harian [cite: 587-600]
CREATE TABLE IF NOT EXISTS attendances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    classroom_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('H', 'S', 'I', 'A') NOT NULL COMMENT 'H=Hadir, S=Sakit, I=Izin, A=Alpa',
    notes TEXT NULL,
    recorded_by INT NULL COMMENT 'User ID staff yang input',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (student_id, date), -- Satu siswa cuma bisa diabsen sekali sehari
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE
);

-- Seeding Kelas Dummy
INSERT INTO classrooms (name, level, major) VALUES 
('7-A (Putra)', '7', 'MTS'),
('7-B (Putri)', '7', 'MTS'),
('10-IPA (Putra)', '10', 'MA');
