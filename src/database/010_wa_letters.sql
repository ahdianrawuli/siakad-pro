-- 1. Tabel Template Surat
CREATE TABLE IF NOT EXISTS letter_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE COMMENT 'keterangan_aktif, panggilan_ortu',
    name VARCHAR(100) NOT NULL,
    content TEXT NOT NULL COMMENT 'Isi surat dengan placeholder {nama}, {nis}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Data Awal (Contoh Surat Keterangan Aktif)
INSERT INTO letter_templates (code, name, content) VALUES 
('keterangan_aktif', 'Surat Keterangan Aktif Belajar', 
'<p>Yang bertanda tangan di bawah ini Kepala Sekolah menerangkan bahwa:</p>
<ul>
<li>Nama: <strong>{nama}</strong></li>
<li>NIS: <strong>{nis}</strong></li>
<li>Kelas: <strong>{kelas}</strong></li>
</ul>
<p>Adalah benar siswa aktif di sekolah kami pada Tahun Ajaran saat ini. Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya.</p>');
