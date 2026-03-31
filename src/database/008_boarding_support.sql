-- --- BAGIAN 1: KEPESANTRENAN ---

-- Tabel Data Kamar / Asrama
CREATE TABLE IF NOT EXISTS dorms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL COMMENT 'Contoh: Gedung A - Kamar 101',
    capacity INT DEFAULT 10,
    gender ENUM('L', 'P') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Mapping Santri ke Kamar (Menambah kolom ke tabel students)
-- Kita alter tabel students yang sudah ada
ALTER TABLE students ADD COLUMN dorm_id INT NULL;
ALTER TABLE students ADD CONSTRAINT fk_dorm FOREIGN KEY (dorm_id) REFERENCES dorms(id) ON DELETE SET NULL;

-- Tabel Perizinan (Izin Keluar/Pulang)
CREATE TABLE IF NOT EXISTS permits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    type ENUM('KELUAR', 'PULANG', 'SAKIT') NOT NULL,
    reason TEXT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'REJECTED', 'RETURNED') DEFAULT 'PENDING',
    approved_by INT NULL, -- User ID Musyrif/Admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- --- BAGIAN 2: SUPPORT (TICKETING) ---

-- Tabel Tiket Pengaduan
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Pelapor (Siswa/Ortu)',
    subject VARCHAR(150) NOT NULL,
    category ENUM('ASRAMA', 'AKADEMIK', 'KEUANGAN', 'KESEHATAN', 'LAINNYA') NOT NULL,
    status ENUM('OPEN', 'ANSWERED', 'CLOSED') DEFAULT 'OPEN',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel Balasan Chat Tiket
CREATE TABLE IF NOT EXISTS ticket_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL COMMENT 'Pengirim pesan',
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Seed Data Asrama
INSERT INTO dorms (name, capacity, gender) VALUES 
('Asrama Hamka - 101', 8, 'L'),
('Asrama Rasuna - 201', 8, 'P');
