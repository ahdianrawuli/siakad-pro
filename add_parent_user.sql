-- Tambah akun orang tua demo
-- Username: orangtua | Password: 123456
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role_id`, `status`, `created_at`, `updated_at`)
VALUES (
    200,
    'M RIZAL (Wali ABDUL QADIR)',
    'orangtua',
    'orangtua@thawalib.sch.id',
    '$2y$10$Cq06BBKaunVgGqRny70Wc.f0brzkLnr.vaP9jxKktvq7u1Q28DveC',
    5,
    'active',
    NOW(),
    NOW()
);

-- Hubungkan ke siswa 25260001 (id=194)
UPDATE `students` SET `parent_user_id` = 200 WHERE `id` = 194;
