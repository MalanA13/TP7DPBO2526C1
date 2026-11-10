<?php

require_once __DIR__ . '/../config/database.php';

class Supplier
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare('SELECT id, nama, kontak, alamat FROM supplier ORDER BY nama');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nama, kontak, alamat FROM supplier WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $nama, string $kontak, string $alamat): bool
    {
        $stmt = $this->db->prepare('INSERT INTO supplier (nama, kontak, alamat) VALUES (?, ?, ?)');
        return $stmt->execute([$nama, $kontak, $alamat]);
    }

    public function update(int $id, string $nama, string $kontak, string $alamat): bool
    {
        $stmt = $this->db->prepare('UPDATE supplier SET nama = ?, kontak = ?, alamat = ? WHERE id = ?');
        return $stmt->execute([$nama, $kontak, $alamat, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM supplier WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function countProduk(int $id): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM produk WHERE id_supplier = ?');
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }
}
