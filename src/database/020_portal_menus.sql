-- ============================================================
-- Migration 020: Menu Portal Orang Tua & Menu Siswa Aktif
-- ============================================================

-- Menu Portal Orang Tua (role_id=5, id 950-959)
INSERT IGNORE INTO menus (id, parent_id, title, url, icon, order_num) VALUES
(950, NULL, 'Beranda',      '/portal/orangtua',       'house',          1),
(951, NULL, 'Data Anak',    '/portal/orangtua/anak',  'child',          2);

-- Menu Siswa Aktif (tambahan ke id 200-299)
INSERT IGNORE INTO menus (id, parent_id, title, url, icon, order_num) VALUES
(910, NULL, 'Jadwal',       '/student/schedule',   'calendar-days',   3),
(911, NULL, 'Absensi',      '/student/attendance', 'calendar-check',  4),
(912, NULL, 'Nilai',        '/student/grades',     'star',            5);

-- Assign menu portal orang tua ke role Orang Tua (role_id=5)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(5, 950), (5, 951);

-- Assign menu siswa aktif ke role Siswa (role_id=4)
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(4, 910), (4, 911), (4, 912);

-- Assign menu siswa aktif ke Super Admin (role_id=1) untuk testing
INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES
(1, 910), (1, 911), (1, 912), (1, 950), (1, 951);
