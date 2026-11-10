<?php

require_once __DIR__ . '/../class/Kategori.php';
require_once __DIR__ . '/../class/Supplier.php';
require_once __DIR__ . '/../class/Produk.php';

// Ambil flash message dari redirect sebelumnya.
function flash(): ?string
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $msg = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $msg;
}

function setFlash(string $msg): void
{
    $_SESSION['flash'] = $msg;
}

function redirect(string $page): void
{
    header('Location: index.php?page=' . $page);
    exit;
}

// --- Kategori ---

function handleKategori(): array
{
    $model = new Kategori();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nama = trim($_POST['nama'] ?? '');
        if ($nama === '') {
            setFlash('Nama kategori tidak boleh kosong.');
        } else {
            $model->create($nama);
            setFlash('Kategori ditambahkan.');
        }
        redirect('kategori');
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        if ($id && $nama !== '') {
            $model->update($id, $nama);
            setFlash('Kategori diperbarui.');
        }
        redirect('kategori');
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($model->countProduk($id) > 0) {
            setFlash('Kategori masih dipakai produk, tidak bisa dihapus.');
        } else {
            $model->delete($id);
            setFlash('Kategori dihapus.');
        }
        redirect('kategori');
    }

    $edit = null;
    if (isset($_GET['edit'])) {
        $edit = $model->getById((int) $_GET['edit']);
    }

    return ['rows' => $model->getAll(), 'edit' => $edit];
}

// --- Supplier ---

function handleSupplier(): array
{
    $model = new Supplier();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nama = trim($_POST['nama'] ?? '');
        $kontak = trim($_POST['kontak'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        if ($nama === '' || $kontak === '' || $alamat === '') {
            setFlash('Semua field supplier wajib diisi.');
        } else {
            $model->create($nama, $kontak, $alamat);
            setFlash('Supplier ditambahkan.');
        }
        redirect('supplier');
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        $kontak = trim($_POST['kontak'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        if ($id && $nama !== '' && $kontak !== '' && $alamat !== '') {
            $model->update($id, $nama, $kontak, $alamat);
            setFlash('Supplier diperbarui.');
        }
        redirect('supplier');
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($model->countProduk($id) > 0) {
            setFlash('Supplier masih memasok produk, tidak bisa dihapus.');
        } else {
            $model->delete($id);
            setFlash('Supplier dihapus.');
        }
        redirect('supplier');
    }

    $edit = null;
    if (isset($_GET['edit'])) {
        $edit = $model->getById((int) $_GET['edit']);
    }

    return ['rows' => $model->getAll(), 'edit' => $edit];
}

// --- Produk ---

function handleProduk(): array
{
    $model = new Produk();
    $kategori = new Kategori();
    $supplier = new Supplier();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nama = trim($_POST['nama'] ?? '');
        $harga = (int) ($_POST['harga'] ?? 0);
        $stok = (int) ($_POST['stok'] ?? 0);
        $satuan = trim($_POST['satuan'] ?? '');
        $idKategori = (int) ($_POST['id_kategori'] ?? 0);
        $idSupplier = (int) ($_POST['id_supplier'] ?? 0);

        if ($nama === '' || $harga <= 0 || $satuan === '' || !$idKategori || !$idSupplier) {
            setFlash('Data produk belum lengkap.');
        } else {
            $model->create($nama, $harga, $stok, $satuan, $idKategori, $idSupplier);
            setFlash('Produk ditambahkan.');
        }
        redirect('produk');
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        $harga = (int) ($_POST['harga'] ?? 0);
        $stok = (int) ($_POST['stok'] ?? 0);
        $satuan = trim($_POST['satuan'] ?? '');
        $idKategori = (int) ($_POST['id_kategori'] ?? 0);
        $idSupplier = (int) ($_POST['id_supplier'] ?? 0);

        if ($id && $nama !== '' && $harga > 0 && $satuan !== '' && $idKategori && $idSupplier) {
            $model->update($id, $nama, $harga, $stok, $satuan, $idKategori, $idSupplier);
            setFlash('Produk diperbarui.');
        }
        redirect('produk');
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $model->delete($id);
        setFlash('Produk dihapus.');
        redirect('produk');
    }

    $edit = null;
    if (isset($_GET['edit'])) {
        $edit = $model->getById((int) $_GET['edit']);
    }

    return [
        'rows' => $model->getAll(),
        'edit' => $edit,
        'kategoriList' => $kategori->getAll(),
        'supplierList' => $supplier->getAll(),
    ];
}
