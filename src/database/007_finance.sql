-- 1. Jenis Tagihan (Fee Types)
CREATE TABLE IF NOT EXISTS fee_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Contoh: SPP Juli 2025, Uang Pangkal',
    amount DECIMAL(12, 2) NOT NULL DEFAULT 0,
    type ENUM('MONTHLY', 'ONETIME') DEFAULT 'MONTHLY',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tagihan Siswa (Bills)
CREATE TABLE IF NOT EXISTS bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    fee_type_id INT NOT NULL,
    amount DECIMAL(12, 2) NOT NULL COMMENT 'Nominal tagihan (bisa beda dr master jika ada diskon)',
    description VARCHAR(255) NULL,
    status ENUM('UNPAID', 'PAID') DEFAULT 'UNPAID',
    due_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (fee_type_id) REFERENCES fee_types(id)
);

-- 3. Transaksi Pembayaran (Transactions)
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bill_id INT NOT NULL,
    amount_paid DECIMAL(12, 2) NOT NULL,
    payment_method ENUM('CASH', 'TRANSFER') DEFAULT 'CASH',
    payment_date DATE NOT NULL,
    notes TEXT NULL,
    admin_id INT NOT NULL, -- Siapa yang input (Kasir)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bill_id) REFERENCES bills(id),
    FOREIGN KEY (admin_id) REFERENCES users(id)
);

-- Seed Data Jenis Tagihan
INSERT INTO fee_types (name, amount, type) VALUES 
('Uang Pembangunan', 5000000, 'ONETIME'),
('SPP Agustus 2025', 350000, 'MONTHLY');
