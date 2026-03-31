-- 1. Tabel Pembayaran PPDB [cite: 48-49]
CREATE TABLE IF NOT EXISTS ppdb_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    proof_file VARCHAR(255) NOT NULL COMMENT 'Lokasi file bukti transfer',
    status ENUM('PENDING', 'VERIFIED', 'REJECTED') DEFAULT 'PENDING',
    notes TEXT NULL COMMENT 'Catatan dari admin jika ditolak',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidate_id) REFERENCES student_candidates(id) ON DELETE CASCADE
);

-- 2. Tabel Dokumen Pendukung [cite: 50-51]
CREATE TABLE IF NOT EXISTS ppdb_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_id INT NOT NULL,
    doc_type ENUM('KK', 'AKTA', 'IJAZAH', 'FOTO') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    status ENUM('PENDING', 'VALID', 'INVALID') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidate_id) REFERENCES student_candidates(id) ON DELETE CASCADE
);
