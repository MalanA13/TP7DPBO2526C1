<?php

// CLI test: CRUD semua entitas, plus guard FK.
// Jalankan: php test.php

require_once __DIR__ . '/class/Kategori.php';
require_once __DIR__ . '/class/Supplier.php';
require_once __DIR__ . '/class/Produk.php';

$fail = 0;

function check(string $label, bool $ok): void
{
    global $fail;
    echo ($ok ? 'PASS' : 'FAIL') . "  $label\n";
    if (!$ok) {
        $fail++;
    }
}

$kategori = new Kategori();
$supplier = new Supplier();
$produk = new Produk();

// Kategori: create, read, update
$kategori->create('Test Kategori');
$all = $kategori->getAll();
$found = null;
foreach ($all as $row) {
    if ($row['nama'] === 'Test Kategori') {
        $found = $row;
    }
}
check('kategori create + read', $found !== null);

$kategori->update((int) $found['id'], 'Test Kategori Ubah');
$again = $kategori->getById((int) $found['id']);
check('kategori update', $again['nama'] === 'Test Kategori Ubah');

// Supplier: create, read, update
$supplier->create('Test Supplier', '0800000000', 'Jl. Test');
$allS = $supplier->getAll();
$foundS = null;
foreach ($allS as $row) {
    if ($row['nama'] === 'Test Supplier') {
        $foundS = $row;
    }
}
check('supplier create + read', $foundS !== null && $foundS['kontak'] === '0800000000');

$supplier->update((int) $foundS['id'], 'Test Supplier Ubah', '0811111111', 'Jl. Baru');
$againS = $supplier->getById((int) $foundS['id']);
check('supplier update', $againS['nama'] === 'Test Supplier Ubah' && $againS['alamat'] === 'Jl. Baru');

// Produk: create pakai FK yang baru dibuat, read dengan join
$produk->create('Test Produk', 9999, 3, 'pcs', (int) $found['id'], (int) $foundS['id']);
$allP = $produk->getAll();
$foundP = null;
foreach ($allP as $row) {
    if ($row['nama'] === 'Test Produk') {
        $foundP = $row;
    }
}
check('produk create + join', $foundP !== null
    && $foundP['kategori'] === 'Test Kategori Ubah'
    && $foundP['supplier'] === 'Test Supplier Ubah');

$produk->update((int) $foundP['id'], 'Test Produk Ubah', 11111, 7, 'dus', (int) $found['id'], (int) $foundS['id']);
$againP = $produk->getById((int) $foundP['id']);
check('produk update', $againP['nama'] === 'Test Produk Ubah' && (int) $againP['harga'] === 11111 && (int) $againP['stok'] === 7);

// FK guard: kategori yang masih dipakai produk tidak boleh terhapus
$blocked = false;
try {
    $kategori->delete((int) $found['id']);
} catch (PDOException $e) {
    $blocked = true;
}
check('fk block delete kategori', $blocked && $kategori->getById((int) $found['id']) !== null);

// Delete berurutan: produk dulu, baru parent
check('produk delete', $produk->delete((int) $foundP['id']) && $produk->getById((int) $foundP['id']) === null);
check('kategori delete after', $kategori->delete((int) $found['id']) && $kategori->getById((int) $found['id']) === null);
check('supplier delete after', $supplier->delete((int) $foundS['id']) && $supplier->getById((int) $foundS['id']) === null);

// Data awal tetap utuh
check('seed data intact', count($produk->getAll()) === 8);

echo $fail === 0 ? "\nSemua lulus.\n" : "\n$fail gagal.\n";
exit($fail === 0 ? 0 : 1);
