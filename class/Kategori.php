<?php

require_once __DIR__ . '/../config/database.php';

class Kategori
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare('SELECT id, nama FROM kategori ORDER BY nama');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nama FROM kategori WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $nama): bool
    {
        $stmt = $this->db->prepare('INSERT INTO kategori (nama) VALUES (?)');
        return $stmt->execute([$nama]);
    }

    public function update(int $id, string $nama): bool
    {
        $stmt = $this->db->prepare('UPDATE kategori SET nama = ? WHERE id = ?');
        return $stmt->execute([$nama, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM kategori WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // Jumlah produk yang masih memakai kategori ini.
    public function countProduk(int $id): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM produk WHERE id_kategori = ?');
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }
}
