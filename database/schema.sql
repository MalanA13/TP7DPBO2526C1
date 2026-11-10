-- Toko Sembako: katalog produk
-- 3 tabel, 2 relasi FK (produk -> kategori, produk -> supplier)

CREATE DATABASE IF NOT EXISTS toko_sembako;
USE toko_sembako;

DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS supplier;

CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL
);

CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    kontak VARCHAR(50) NOT NULL,
    alamat VARCHAR(255) NOT NULL
);

CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    harga INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    satuan VARCHAR(30) NOT NULL DEFAULT 'pcs',
    id_kategori INT NOT NULL,
    id_supplier INT NOT NULL,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE RESTRICT,
    FOREIGN KEY (id_supplier) REFERENCES supplier(id) ON DELETE RESTRICT
);

INSERT INTO kategori (nama) VALUES
    ('Beras'),
    ('Minyak'),
    ('Gula'),
    ('Tepung'),
    ('Bumbu');

INSERT INTO supplier (nama, kontak, alamat) VALUES
    ('CV Beras Sejahtera', '081234567890', 'Jl. Pasar Induk No. 12, Bandung'),
    ('PT Minyak Nusantara', '082345678901', 'Jl. Industri Raya No. 5, Bekasi'),
    ('UD Manis Jaya', '083456789012', 'Jl. Raya Cirebon Km 8, Cirebon');

INSERT INTO produk (nama, harga, stok, satuan, id_kategori, id_supplier) VALUES
    ('Beras Premium 5kg', 75000, 40, 'karung', 1, 1),
    ('Beras Medium 5kg', 62000, 55, 'karung', 1, 1),
    ('Minyak Goreng 2L', 34000, 80, 'botol', 2, 2),
    ('Minyak Goreng 1L', 18000, 120, 'botol', 2, 2),
    ('Gula Pasir 1kg', 16000, 90, 'bungkus', 3, 3),
    ('Tepung Terigu 1kg', 12000, 70, 'bungkus', 4, 3),
    ('Garam Dapur 500g', 5000, 150, 'bungkus', 5, 3),
    ('Kecap Manis 600ml', 15000, 60, 'botol', 5, 2);
