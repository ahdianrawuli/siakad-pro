-- 1. Tabel Pengaturan Global (Key-Value Store)
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed Data Awal Identitas Sekolah
INSERT INTO settings (setting_key, setting_value) VALUES 
('school_name', 'Pondok Pesantren Thawalib Parabek'),
('school_address', 'Jl. Raya Bukittinggi - Medan Km. 4, Agam, Sumatera Barat'),
('school_phone', '0752-123456'),
('school_email', 'info@thawalibparabek.sch.id'),
('school_website', 'www.thawalibparabek.sch.id'),
('school_logo', 'default_logo.png'),
('app_version', '1.0.0');

-- 2. Pastikan Tabel Roles ada (untuk dropdown user management)
-- Jika sebelumnya roles hardcoded, kita buat tabel resminya sekarang untuk kerapian
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL
);

-- Seed Roles jika kosong (Abaikan warning jika sudah ada)
INSERT INTO roles (id, name, slug) VALUES 
(1, 'Super Admin', 'super-admin'),
(2, 'Administrator', 'admin'),
(3, 'Guru / Ustadz', 'guru'),
(4, 'Santri / Siswa', 'siswa'),
(5, 'Wali Murid', 'wali')
ON DUPLICATE KEY UPDATE name=name;
