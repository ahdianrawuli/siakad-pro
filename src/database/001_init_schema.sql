SET FOREIGN_KEY_CHECKS=0;

-- 1. Tabel Roles (Sesuai PDF: Super Admin, Guru, Siswa, dll)
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- 3. Tabel Dynamic Menus (Sesuai PDF Hal 47)
-- Mengelola struktur hierarkis (Parent-Child)
CREATE TABLE IF NOT EXISTS menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL,
    title VARCHAR(50) NOT NULL,
    url VARCHAR(100) DEFAULT '#',
    icon VARCHAR(50) DEFAULT 'circle', -- Icon class (DashTail/FontAwesome)
    order_num INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES menus(id) ON DELETE CASCADE
);

-- 4. Tabel Akses Menu per Role (Permissions)
CREATE TABLE IF NOT EXISTS role_menu (
    role_id INT NOT NULL,
    menu_id INT NOT NULL,
    PRIMARY KEY (role_id, menu_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
);

-- SEEDING DATA AWAL (Initial Data)

-- Insert Roles Default [cite: 21-27]
INSERT INTO roles (name, slug) VALUES 
('Super Admin', 'super-admin'),
('Admin Sekolah', 'admin-sekolah'),
('Guru', 'guru'),
('Siswa', 'siswa'),
('Orang Tua', 'orang-tua'),
('Kepala Sekolah', 'kepala-sekolah');

-- Insert Super Admin (Password: password123)
INSERT INTO users (name, username, email, password, role_id) VALUES 
('System Administrator', 'admin', 'admin@siakad.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert Menu Dasar (Core Flow)
-- Menu Dashboard
INSERT INTO menus (id, parent_id, title, url, icon, order_num) VALUES (1, NULL, 'Dashboard', '/dashboard', 'home', 1);

-- Menu PPDB (Parent) [cite: 72]
INSERT INTO menus (id, parent_id, title, url, icon, order_num) VALUES (2, NULL, 'PPDB', '#', 'user-plus', 2);
-- Sub Menu PPDB
INSERT INTO menus (parent_id, title, url, order_num) VALUES 
(2, 'Data Pendaftar', '/ppdb/registrations', 1),
(2, 'Atur Periode', '/ppdb/periods', 2),
(2, 'Atur Jalur', '/ppdb/tracks', 3);

-- Menu Akademik (Parent) [cite: 355]
INSERT INTO menus (id, parent_id, title, url, icon, order_num) VALUES (3, NULL, 'Akademik', '#', 'book', 3);
-- Sub Menu Akademik
INSERT INTO menus (parent_id, title, url, order_num) VALUES 
(3, 'Data Kurikulum', '/academic/curriculums', 1),
(3, 'Mata Pelajaran', '/academic/subjects', 2),
(3, 'Data Kelas', '/academic/classrooms', 3),
(3, 'Jadwal Pelajaran', '/academic/schedules', 4);

-- Menu Settings (Parent) [cite: 1165]
INSERT INTO menus (id, parent_id, title, url, icon, order_num) VALUES (99, NULL, 'Settings', '#', 'settings', 99);
INSERT INTO menus (parent_id, title, url, order_num) VALUES 
(99, 'Manajemen User', '/settings/users', 1),
(99, 'Manajemen Menu', '/settings/menus', 2); -- Ini fitur Request Anda

-- Mapping Menu ke Super Admin (All Access)
INSERT INTO role_menu (role_id, menu_id) SELECT 1, id FROM menus;

SET FOREIGN_KEY_CHECKS=1;
