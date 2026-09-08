<?php
include 'koneksi.php';

// Ambil semua data buku
$query = "SELECT * FROM books ORDER BY id DESC";
$result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Katalog Buku - Perpustakaan</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f1f5f9;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        /* NAVIGASI */
        .nav-menu {
            background: #1e293b;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            transition: 0.3s;
        }

        .nav-menu a:hover {
            background: #334155;
        }

        /* HEADER */
        .header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .header h2 {
            margin: 0 0 8px 0;
            color: #1e293b;
        }

        .header p {
            margin: 0;
            color: #64748b;
        }

        /* BUTTON TAMBAH */
        .btn-add {
            display: inline-block;
            padding: 10px 16px;
            background: #16a34a;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .btn-add:hover {
            background: #15803d;
        }

        /* TABLE */
        .table-container {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th {
            background: #1e293b;
            color: white;
            padding: 13px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:hover {
            background: #f8fafc;
        }

        /* BUTTON AKSI */
        .btn {
            display: inline-block;
            padding: 7px 11px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
            margin: 2px;
        }

        .btn-edit {
            background: #2563eb;
        }

        .btn-edit:hover {
            background: #1d4ed8;
        }

        .btn-delete {
            background: #dc2626;
        }

        .btn-delete:hover {
            background: #b91c1c;
        }

        /* STOK */
        .stok {
            font-weight: bold;
        }

        .stok-habis {
            color: #dc2626;
        }

        .stok-tersedia {
            color: #16a34a;
        }

        /* DATA KOSONG */
        .empty {
            text-align: center;
            padding: 30px;
            color: #64748b;
        }

        /* RESPONSIVE */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .nav-menu {
                flex-direction: column;
            }

            .nav-menu a {
                display: block;
            }

            .header {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- MENU NAVIGASI -->
    <div class="nav-menu">
        <a href="index.php">📚 Katalog Buku</a>
        <a href="anggota.php">👥 Manajemen Anggota</a>
        <a href="peminjaman.php">🔄 Transaksi Peminjaman</a>
    </div>

    <!-- HEADER -->
    <div class="header">
        <h2>📚 Katalog Buku Perpustakaan</h2>
        <p>Daftar koleksi buku yang tersedia di perpustakaan.</p>
    </div>

    <!-- BUTTON TAMBAH -->
    <a href="tambah_buku.php" class="btn-add">
        ➕ Tambah Buku Baru
    </a>

    <!-- TABEL -->
    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Penerbit</th>
                    <th>Tahun</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php
            $no = 1;

            if (mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {
            ?>

                <tr>

                    <td>
                        <?= $no++; ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['judul_buku']); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['penulis']); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['penerbit']); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['tahun']); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['kategori']); ?>
                    </td>

                    <td class="stok 
                        <?= ($row['stok'] <= 0) ? 'stok-habis' : 'stok-tersedia'; ?>">
                        
                        <?= htmlspecialchars($row['stok']); ?>

                        <?php if ($row['stok'] <= 0): ?>
                            <small>(Habis)</small>
                        <?php endif; ?>

                    </td>

                    <td>

                        <!-- EDIT -->
                        <a
                            href="edit_buku.php?id=<?= urlencode($row['id']); ?>"
                            class="btn btn-edit">
                            ✏️ Edit
                        </a>

                        <!-- HAPUS -->
                        <a
                            href="hapus_buku.php?id=<?= urlencode($row['id']); ?>"
                            class="btn btn-delete"
                            onclick="return confirm('Yakin ingin menghapus buku ini?');">
                            🗑️ Hapus
                        </a>

                    </td>

                </tr>

            <?php
                }

            } else {
            ?>

                <tr>
                    <td colspan="8" class="empty">
                        📖 Belum ada data buku di katalog.
                    </td>
                </tr>

            <?php
            }
            ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>