-- Pastikan tabel announcements ada
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50) NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pastikan tabel extracurriculars ada
CREATE TABLE IF NOT EXISTS extracurriculars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    schedule_day VARCHAR(20) NULL,
    schedule_time VARCHAR(20) NULL,
    location VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS extracurricular_coaches (
    extracurricular_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (extracurricular_id, user_id),
    FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS student_extracurriculars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    extracurricular_id INT NOT NULL,
    status ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS extracurricular_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    extracurricular_id INT NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS extracurricular_attendances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('H', 'A', 'I', 'S') DEFAULT 'H',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (schedule_id) REFERENCES extracurricular_schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Tambah menu baru untuk siswa
INSERT INTO menus (id, parent_id, title, url, icon, order_num, is_active) VALUES
(213, NULL, 'Pengumuman', '/student/announcements', 'bullhorn', 13, 1),
(214, NULL, 'Ekstrakurikuler', '/student/extracurricular', 'person-running', 14, 1),
(215, NULL, 'Asrama', '/student/boarding', 'bed', 15, 1),
(216, NULL, 'Pelanggaran & Prestasi', '/student/discipline', 'shield-halved', 16, 1),
(217, NULL, 'Surat Keterangan', '/student/letter', 'file-lines', 17, 1),
(218, NULL, 'Kesehatan', '/student/health', 'heart-pulse', 18, 1)
ON DUPLICATE KEY UPDATE title=title;

-- Assign menu ke role siswa (role_id=4)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(4, 213), (4, 214), (4, 215), (4, 216), (4, 217), (4, 218);

-- Seed data dummy announcements
INSERT INTO announcements (title, content, category) VALUES
('Libur Hari Raya Idul Fitri', 'Pesantren libur mulai tanggal 10-20 April 2026. Santri diharapkan pulang ke rumah masing-masing.', 'AKADEMIK'),
('Pendaftaran Ekstrakurikuler Dibuka', 'Pendaftaran ekskul tahun ajaran baru dibuka hingga akhir bulan ini. Silakan daftar di kantor TU.', 'KEGIATAN');
