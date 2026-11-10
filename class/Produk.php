<?php

require_once __DIR__ . '/../config/database.php';

class Produk
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll(): array
    {
        $sql = 'SELECT p.id, p.nama, p.harga, p.stok, p.satuan,
                       k.nama AS kategori, s.nama AS supplier,
                       p.id_kategori, p.id_supplier
                FROM produk p
                JOIN kategori k ON k.id = p.id_kategori
                JOIN supplier s ON s.id = p.id_supplier
                ORDER BY p.nama';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $sql = 'SELECT p.id, p.nama, p.harga, p.stok, p.satuan,
                       p.id_kategori, p.id_supplier
                FROM produk p WHERE p.id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $nama, int $harga, int $stok, string $satuan, int $idKategori, int $idSupplier): bool
    {
        $sql = 'INSERT INTO produk (nama, harga, stok, satuan, id_kategori, id_supplier)
                VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nama, $harga, $stok, $satuan, $idKategori, $idSupplier]);
    }

    public function update(int $id, string $nama, int $harga, int $stok, string $satuan, int $idKategori, int $idSupplier): bool
    {
        $sql = 'UPDATE produk
                SET nama = ?, harga = ?, stok = ?, satuan = ?, id_kategori = ?, id_supplier = ?
                WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nama, $harga, $stok, $satuan, $idKategori, $idSupplier, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM produk WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
