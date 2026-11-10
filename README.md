# Toko Sembako

Katalog produk toko sembako. PHP native, OOP, PDO prepared statement.

## Tema

Pencatatan stok sembako: produk, kategori, dan supplier. Satu produk milik satu kategori dan dipasok satu supplier.

## Database

Database: `toko_sembako` (MySQL/MariaDB).

| Tabel | Kolom | Relasi |
|---|---|---|
| kategori | id, nama | - |
| supplier | id, nama, kontak, alamat | - |
| produk | id, nama, harga, stok, satuan, id_kategori, id_supplier | FK ke kategori dan supplier, ON DELETE RESTRICT |

File SQL: `database/schema.sql` (struktur + data awal).

## Struktur folder

```
TP/
├── index.php            titik masuk, routing ?page=, render view
├── class/
│   ├── Kategori.php     CRUD tabel kategori
│   ├── Supplier.php     CRUD tabel supplier
│   ├── Produk.php       CRUD tabel produk (join ke kategori & supplier)
│   └── handler.php      proses POST form, validasi, redirect + flash
├── config/
│   └── database.php     koneksi PDO (singleton)
├── database/
│   └── schema.sql
├── style/
│   └── style.css
└── test.php             uji CRUD lewat CLI
```

## Flow

1. Browser buka `index.php?page=produk|kategori|supplier`.
2. `index.php` panggil handler sesuai halaman.
3. Kalau ada POST, handler validasi input, panggil method class, simpan flash message, redirect (PRG).
4. Class jalankan query lewat PDO prepared statement. Tidak ada query mentah.
5. `index.php` render form + tabel dari data yang dikembalikan handler.

Hapus kategori atau supplier ditolak kalau masih dipakai produk (cek di handler, plus FK di database sebagai pengaman kedua).

## Menjalankan

Butuh PHP 8+ dengan ekstensi `pdo_mysql`, dan MySQL/MariaDB.

```bash
# buat database
mysql -u root -p < database/schema.sql

# sesuaikan host, user, password di config/database.php

# jalankan
php -S 127.0.0.1:8099
# buka http://127.0.0.1:8099
```

Uji logika tanpa browser:

```bash
php test.php
```

Mencakup create, read, update, delete untuk ketiga tabel, join produk, penolakan hapus saat FK masih dipakai, dan keutuhan data awal.
