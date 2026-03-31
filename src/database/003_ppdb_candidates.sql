-- Tabel Calon Santri (Data Mentah Pendaftaran)
CREATE TABLE IF NOT EXISTS student_candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_no VARCHAR(20) NOT NULL UNIQUE COMMENT 'Format: REG-YYYY-XXXX',
    user_id INT NULL COMMENT 'Link ke tabel Users jika akun sudah dibuat',
    
    -- Tab 1: Administrasi [cite: 38]
    ppdb_track_id INT NOT NULL,
    exam_location ENUM('ONLINE', 'OFFLINE') DEFAULT 'OFFLINE',
    whatsapp_number VARCHAR(20) NOT NULL,
    
    -- Tab 2: Biodata [cite: 39]
    nisn VARCHAR(20) NULL,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    birth_place VARCHAR(50),
    birth_date DATE,
    address TEXT,
    
    -- Tab 3: Riwayat Pendidikan [cite: 40]
    school_origin VARCHAR(100),
    school_address TEXT,
    
    -- Tab 4: Orang Tua [cite: 41]
    father_name VARCHAR(100),
    father_job VARCHAR(50),
    father_phone VARCHAR(20),
    mother_name VARCHAR(100),
    mother_job VARCHAR(50),
    mother_phone VARCHAR(20),
    
    -- Status
    registration_status ENUM('PENDING', 'VERIFIED', 'PAID', 'ACCEPTED', 'REJECTED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ppdb_track_id) REFERENCES ppdb_tracks(id)
);
