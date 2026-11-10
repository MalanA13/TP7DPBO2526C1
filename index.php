<?php
session_start();
require_once __DIR__ . '/class/handler.php';

$page = $_GET['page'] ?? 'produk';
$allowed = ['produk', 'kategori', 'supplier'];
if (!in_array($page, $allowed, true)) {
    $page = 'produk';
}

$data = match ($page) {
    'kategori' => handleKategori(),
    'supplier' => handleSupplier(),
    default => handleProduk(),
};

$flash = flash();

function h(?string $v): string
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

function rupiah(int $n): string
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toko Sembako</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<div class="wrap">
    <header class="top">
        <p class="brand">Warung Berkah<span>Catatan stok sembako</span></p>
        <nav>
            <a href="?page=produk" class="<?= $page === 'produk' ? 'active' : '' ?>">Produk</a>
            <a href="?page=kategori" class="<?= $page === 'kategori' ? 'active' : '' ?>">Kategori</a>
            <a href="?page=supplier" class="<?= $page === 'supplier' ? 'active' : '' ?>">Supplier</a>
        </nav>
    </header>

    <?php if ($flash): ?>
        <div class="flash" role="status"><?= h($flash) ?></div>
    <?php endif; ?>

    <?php if ($page === 'produk'): ?>
        <?php $e = $data['edit']; ?>
        <h2><?= $e ? 'Ubah produk' : 'Tambah produk' ?></h2>
        <form class="editor" method="post">
            <input type="hidden" name="action" value="<?= $e ? 'update' : 'create' ?>">
            <?php if ($e): ?><input type="hidden" name="id" value="<?= (int) $e['id'] ?>"><?php endif; ?>
            <label>Nama
                <input name="nama" required value="<?= h($e['nama'] ?? '') ?>">
            </label>
            <label>Harga (Rp)
                <input name="harga" type="number" min="1" required value="<?= h((string) ($e['harga'] ?? '')) ?>">
            </label>
            <label>Stok
                <input name="stok" type="number" min="0" required value="<?= h((string) ($e['stok'] ?? '0')) ?>">
            </label>
            <label>Satuan
                <input name="satuan" required value="<?= h($e['satuan'] ?? '') ?>" placeholder="kg, botol, bungkus">
            </label>
            <label>Kategori
                <select name="id_kategori" required>
                    <option value="">Pilih</option>
                    <?php foreach ($data['kategoriList'] as $k): ?>
                        <option value="<?= (int) $k['id'] ?>" <?= ($e && (int) $e['id_kategori'] === (int) $k['id']) ? 'selected' : '' ?>><?= h($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Supplier
                <select name="id_supplier" required>
                    <option value="">Pilih</option>
                    <?php foreach ($data['supplierList'] as $s): ?>
                        <option value="<?= (int) $s['id'] ?>" <?= ($e && (int) $e['id_supplier'] === (int) $s['id']) ? 'selected' : '' ?>><?= h($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="actions">
                <button type="submit"><?= $e ? 'Simpan perubahan' : 'Tambah produk' ?></button>
                <?php if ($e): ?><a class="btn ghost" href="?page=produk">Batal</a><?php endif; ?>
            </div>
        </form>

        <h2>Daftar produk</h2>
        <?php if (!$data['rows']): ?>
            <div class="empty">Belum ada produk. Tambahkan lewat form di atas.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th><th>Kategori</th><th>Supplier</th>
                        <th class="num">Harga</th><th class="num">Stok</th><th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($data['rows'] as $r): ?>
                    <tr>
                        <td><?= h($r['nama']) ?></td>
                        <td><?= h($r['kategori']) ?></td>
                        <td><?= h($r['supplier']) ?></td>
                        <td class="num"><?= rupiah((int) $r['harga']) ?></td>
                        <td class="num"><?= (int) $r['stok'] ?> <?= h($r['satuan']) ?></td>
                        <td class="row-actions">
                            <a class="btn ghost" href="?page=produk&edit=<?= (int) $r['id'] ?>">Ubah</a>
                            <form method="post" onsubmit="return confirm('Hapus produk ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <button class="danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif ($page === 'kategori'): ?>
        <?php $e = $data['edit']; ?>
        <h2><?= $e ? 'Ubah kategori' : 'Tambah kategori' ?></h2>
        <form class="editor" method="post">
            <input type="hidden" name="action" value="<?= $e ? 'update' : 'create' ?>">
            <?php if ($e): ?><input type="hidden" name="id" value="<?= (int) $e['id'] ?>"><?php endif; ?>
            <label>Nama kategori
                <input name="nama" required value="<?= h($e['nama'] ?? '') ?>">
            </label>
            <div class="actions">
                <button type="submit"><?= $e ? 'Simpan perubahan' : 'Tambah kategori' ?></button>
                <?php if ($e): ?><a class="btn ghost" href="?page=kategori">Batal</a><?php endif; ?>
            </div>
        </form>

        <h2>Daftar kategori</h2>
        <?php if (!$data['rows']): ?>
            <div class="empty">Belum ada kategori.</div>
        <?php else: ?>
            <table>
                <thead><tr><th>Nama</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($data['rows'] as $r): ?>
                    <tr>
                        <td><?= h($r['nama']) ?></td>
                        <td class="row-actions">
                            <a class="btn ghost" href="?page=kategori&edit=<?= (int) $r['id'] ?>">Ubah</a>
                            <form method="post" onsubmit="return confirm('Hapus kategori ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <button class="danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php else: ?>
        <?php $e = $data['edit']; ?>
        <h2><?= $e ? 'Ubah supplier' : 'Tambah supplier' ?></h2>
        <form class="editor" method="post">
            <input type="hidden" name="action" value="<?= $e ? 'update' : 'create' ?>">
            <?php if ($e): ?><input type="hidden" name="id" value="<?= (int) $e['id'] ?>"><?php endif; ?>
            <label>Nama
                <input name="nama" required value="<?= h($e['nama'] ?? '') ?>">
            </label>
            <label>Kontak
                <input name="kontak" required value="<?= h($e['kontak'] ?? '') ?>">
            </label>
            <label>Alamat
                <input name="alamat" required value="<?= h($e['alamat'] ?? '') ?>">
            </label>
            <div class="actions">
                <button type="submit"><?= $e ? 'Simpan perubahan' : 'Tambah supplier' ?></button>
                <?php if ($e): ?><a class="btn ghost" href="?page=supplier">Batal</a><?php endif; ?>
            </div>
        </form>

        <h2>Daftar supplier</h2>
        <?php if (!$data['rows']): ?>
            <div class="empty">Belum ada supplier.</div>
        <?php else: ?>
            <table>
                <thead><tr><th>Nama</th><th>Kontak</th><th>Alamat</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($data['rows'] as $r): ?>
                    <tr>
                        <td><?= h($r['nama']) ?></td>
                        <td><?= h($r['kontak']) ?></td>
                        <td><?= h($r['alamat']) ?></td>
                        <td class="row-actions">
                            <a class="btn ghost" href="?page=supplier&edit=<?= (int) $r['id'] ?>">Ubah</a>
                            <form method="post" onsubmit="return confirm('Hapus supplier ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <button class="danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
