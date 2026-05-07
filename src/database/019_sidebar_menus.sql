-- ============================================================
-- Migration 019: Pindahkan semua menu hardcode ke database
-- ============================================================

-- Fix URL yang salah
UPDATE menus SET url = '/finance/billing' WHERE id = 202 AND url = '/finance';
UPDATE menus SET url = '/student/biodata' WHERE id = 201 AND url = '/student/profile';

-- Menu Rapor
INSERT IGNORE INTO menus (id, parent_id, title, url, icon, order_num) VALUES
(230, NULL, 'Rapor',       '#',                  'file-signature', 40),
(231, 230,  'Rapor Siswa', '/reports/students',  'file-lines',     1),
(232, 230,  'Rapor Asrama','/reports/boarding',  'moon',           2);

-- Menu Poskestren
INSERT IGNORE INTO menus (id, parent_id, title, url, icon, order_num) VALUES
(235, NULL, 'Poskestren',  '#',                      'house-medical', 45),
(236, 235,  'Data Pasien', '/poskestren/patients',   'user-injured',  1),
(237, 235,  'Data Petugas','/poskestren/staff',      'user-nurse',    2);

-- Menu Pengumuman
INSERT IGNORE INTO menus (id, parent_id, title, url, icon, order_num) VALUES
(240, NULL, 'Pengumuman',  '/announcements', 'bullhorn', 50);

-- Menu Portal Santri Kandidat (role siswa, belum aktif) — id 900-999
INSERT IGNORE INTO menus (id, parent_id, title, url, icon, order_num) VALUES
(900, NULL, 'Progress',    '/student/dashboard', 'chart-line',          1),
(901, NULL, 'Data Santri', '/student/biodata',   'address-card',        2),
(902, NULL, 'Pembayaran',  '/student/payment',   'file-invoice-dollar', 3),
(903, NULL, 'Dokumen',     '/student/documents', 'folder-open',         4),
(904, NULL, 'Resume',      '/student/resume',    'file-lines',          5);

-- Submenu yang dulu diinjeksi manual di sidebar
INSERT IGNORE INTO menus (id, parent_id, title, url, icon, order_num) VALUES
(260, 20,  'Pengaturan PPDB', '/school/ppdb',    'cog',    10),
(261, 90,  'Menu',            '/settings/menus', 'list',   1),
(262, 90,  'Roles',           '/settings/roles', 'shield', 2);

-- Assign menu baru ke Super Admin (role_id=1)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(1, 230),(1, 231),(1, 232),
(1, 235),(1, 236),(1, 237),
(1, 240),
(1, 260),(1, 261),(1, 262);

-- Assign menu portal santri ke role Siswa (role_id=4)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(4, 201),(4, 202),(4, 203),(4, 204),
(4, 900),(4, 901),(4, 902),(4, 903),(4, 904);

-- ============================================================
-- ASSIGN menu ke semua role (Guru, Admin, Kepala Sekolah, Staff, Orang Tua)
-- ============================================================

-- GURU (role_id=3)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(3,1),(3,30),(3,33),(3,35),(3,36),(3,212),(3,213),
(3,40),(3,41),(3,42),(3,43),(3,44),(3,45),(3,46),(3,209),(3,210),(3,211),(3,216),(3,222),
(3,50),(3,52),(3,53),(3,54),(3,55),(3,218),(3,219),(3,220),
(3,224),(3,221),(3,225),(3,226),(3,227),
(3,230),(3,231),(3,232),(3,240),(3,100),(3,101);

-- TATA USAHA / ADMIN (role_id=2)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(2,1),
(2,10),(2,11),(2,12),(2,13),(2,14),(2,15),
(2,20),(2,21),(2,22),(2,23),(2,24),(2,260),
(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,36),(2,37),(2,212),(2,213),
(2,40),(2,41),(2,42),(2,43),(2,44),(2,45),(2,46),(2,209),(2,210),(2,211),(2,216),(2,222),
(2,50),(2,51),(2,52),(2,53),(2,54),(2,55),(2,214),(2,218),(2,219),(2,220),
(2,60),(2,61),(2,62),(2,63),(2,217),
(2,205),(2,206),(2,207),(2,208),(2,215),
(2,224),(2,221),(2,225),(2,226),(2,227),
(2,230),(2,231),(2,232),(2,235),(2,236),(2,237),(2,240),(2,100),(2,101);

-- KEPALA SEKOLAH (role_id=6)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(6,1),
(6,10),(6,11),(6,12),(6,13),(6,14),(6,15),
(6,20),(6,21),(6,22),(6,23),(6,24),(6,260),
(6,30),(6,31),(6,32),(6,33),(6,34),(6,35),(6,36),(6,37),(6,212),(6,213),
(6,40),(6,41),(6,42),(6,43),(6,44),(6,45),(6,46),(6,209),(6,210),(6,211),(6,216),(6,222),
(6,50),(6,51),(6,52),(6,53),(6,54),(6,55),(6,214),(6,218),(6,219),(6,220),
(6,60),(6,61),(6,62),(6,63),(6,217),
(6,205),(6,206),(6,207),(6,208),(6,215),
(6,224),(6,221),(6,225),(6,226),(6,227),
(6,230),(6,231),(6,232),(6,235),(6,236),(6,237),(6,240),(6,100),(6,101);

-- STAFF / KARYAWAN (role_id=7)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(7,1),(7,205),(7,206),(7,207),(7,208),(7,215),
(7,60),(7,61),(7,62),(7,63),(7,217),(7,240);

-- ORANG TUA (role_id=5)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(5,1),(5,30),(5,31),(5,33),(5,35),(5,60),(5,61),(5,63),(5,240);
