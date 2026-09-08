<?php
include 'koneksi.php';

// Proses Simpan Peminjaman
$pesan = "";
if (isset($_POST['pinjam'])) {
    $book_id   = (int)$_POST['book_id'];
    $member_id = (int)$_POST['member_id'];
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime('+7 days')); // Tempo 7 hari

    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // 1. Cek stok buku
        $cek_stok = mysqli_query($conn, "SELECT stok FROM books WHERE id = $book_id FOR UPDATE");
        $data_buku = mysqli_fetch_assoc($cek_stok);

        if ($data_buku['stok'] > 0) {
            // 2. Insert ke tabel borrowings
            $query_pinjam = "INSERT INTO borrowings (book_id, member_id, tanggal_pinjam, tanggal_harus_kembali, status) 
                             VALUES ($book_id, $member_id, '$tgl_pinjam', '$tgl_kembali', 'Dipinjam')";
            mysqli_query($conn, $query_pinjam);

            // 3. Kurangi stok buku
            mysqli_query($conn, "UPDATE books SET stok = stok - 1 WHERE id = $book_id");

            // Commit jika berhasil
            mysqli_commit($conn);
            echo "<script>alert('Peminjaman berhasil dicatat!'); window.location='peminjaman.php';</script>";
        } else {
            throw new Exception("Stok buku habis!");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $pesan = "Gagal: " . $e->getMessage();
    }
}

// Ambil data untuk dropdown
$books = mysqli_query($conn, "SELECT * FROM books WHERE stok > 0");
$members = mysqli_query($conn, "SELECT * FROM members");

// Ambil data riwayat peminjaman beserta nama buku dan anggotanya
$query_riwayat = "SELECT borrowings.*, books.judul_buku, members.nama_lengkap, members.nomor_indentitas 
                  FROM borrowings 
                  JOIN books ON borrowings.book_id = books.id 
                  JOIN members ON borrowings.member_id = members.id 
                  ORDER BY borrowings.id DESC";
$result_riwayat = mysqli_query($conn, $query_riwayat);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Peminjaman - Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        h2, h3 { color: #333; }
        .container { display: flex; gap: 20px; }
        .form-box { width: 350px; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; height: fit-content; }
        .table-box { flex-grow: 1; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 14px; }
        th { background-color: #333; color: white; }
        .div-form { margin-bottom: 12px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select { width: 100%; padding: 7px; box-sizing: border-box; }
        button { padding: 9px 15px; background: #0275d8; color: white; border: none; cursor: pointer; border-radius: 3px; }
        button:hover { background: #025aa5; }
        .nav-link { margin-bottom: 15px; display: inline-block; text-decoration: none; color: #0275d8; font-weight: bold; }
        .error { color: red; margin-bottom: 10px; font-size: 14px; }
        .badge-dipinjam { background: #f0ad4e; color: white; padding: 3px 6px; border-radius: 3px; font-size: 12px; }
        .badge-kembali { background: #5cb85c; color: white; padding: 3px 6px; border-radius: 3px; font-size: 12px; }
        a.btn-aksi { padding: 4px 8px; background: #5bc0de; color: white; text-decoration: none; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>

    <a href="index.php" class="nav-link">&laquo; Kembali ke Katalog Buku</a> | 
    <a href="anggota.php" class="nav-link">Kelola Anggota &raquo;</a>
    <h2>Transaksi Peminjaman Buku</h2>

    <div class="container">
        <!-- Form Peminjaman -->
        <div class="form-box">
            <h3>Form Pinjam Buku</h3>
            <?php if(!empty($pesan)) echo "<div class='error'>$pesan</div>"; ?>
            <form action="" method="POST">
                <div class="div-form">
                    <label>Pilih Buku:</label>
                    <select name="book_id" required>
                        <option value="">-- Pilih Buku (Stok > 0) --</option>
                        <?php while($b = mysqli_fetch_assoc($books)): ?>
                            <option value="<?= $b['id']; ?>"><?= htmlspecialchars($b['judul_buku']); ?> (Stok: <?= $b['stok']; ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="div-form">
                    <label>Pilih Anggota:</label>
                    <select name="member_id" required>
                        <option value="">-- Pilih Anggota --</option>
                        <?php while($m = mysqli_fetch_assoc($members)): ?>
                            <option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['nama_lengkap']); ?> (<?= $m['nomor_indentitas']; ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="pinjam">Proses Peminjaman</button>
            </form>
        </div>

        <!-- Tabel Riwayat Peminjaman -->
        <div class="table-box">
            <h3>Riwayat Transaksi Peminjaman</h3>
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    <th>Judul Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Status</th>
                </tr>
                <?php 
                $no = 1;
                if (mysqli_num_rows($result_riwayat) > 0) :
                    while ($row = mysqli_fetch_assoc($result_riwayat)) : 
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td><?= htmlspecialchars($row['judul_buku']); ?></td>
                    <td><?= $row['tanggal_pinjam']; ?></td>
                    <td><?= $row['tanggal_harus_kembali']; ?></td>
                    <td>
                        <?php if($row['status'] == 'Dipinjam'): ?>
                            <span class="badge-dipinjam">Dipinjam</span>
                        <?php else: ?>
                            <span class="badge-kembali">Dikembalikan</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                else :
                ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada data transaksi peminjaman.</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

</body>
</html>