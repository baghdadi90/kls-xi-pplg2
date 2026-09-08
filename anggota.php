<?php
include 'koneksi.php';

// Proses simpan data jika form disubmit
$pesan = "";
if (isset($_POST['submit_anggota'])) {
    $nama_lengkap     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nomor_indentitas = mysqli_real_escape_string($conn, $_POST['nomor_indentitas']);
    $telepon          = mysqli_real_escape_string($conn, $_POST['telepon']);

    $query = "INSERT INTO members (nama_lengkap, nomor_indentitas, telepon) 
              VALUES ('$nama_lengkap', '$nomor_indentitas', '$telepon')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Anggota baru berhasil ditambahkan!'); window.location='anggota.php';</script>";
    } else {
        $pesan = "Gagal menambah anggota: " . mysqli_error($conn);
    }
}

// Ambil daftar anggota dari database
$result_members = mysqli_query($conn, "SELECT * FROM members ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Anggota - Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        h2, h3 { color: #333; }
        .container { display: flex; gap: 20px; }
        .form-box { width: 350px; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; height: fit-content; }
        .table-box { flex-grow: 1; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #333; color: white; }
        .div-form { margin-bottom: 12px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 7px; box-sizing: border-box; }
        button { padding: 9px 15px; background: #0275d8; color: white; border: none; cursor: pointer; border-radius: 3px; }
        button:hover { background: #025aa5; }
        .nav-link { margin-bottom: 15px; display: inline-block; text-decoration: none; color: #0275d8; font-weight: bold; }
        .error { color: red; margin-bottom: 10px; font-size: 14px; }
        a.btn-hapus { padding: 4px 8px; background: #d9534f; color: white; text-decoration: none; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>

    <a href="index.php" class="nav-link">&laquo; Kembali ke Katalog Buku</a>
    <h2>Manajemen Data Anggota Perpustakaan</h2>

    <div class="container">
        <!-- Form Tambah Anggota -->
        <div class="form-box">
            <h3>Tambah Anggota Baru</h3>
            <?php if(!empty($pesan)) echo "<div class='error'>$pesan</div>"; ?>
            <form action="" method="POST">
                <div class="div-form">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama_lengkap" required placeholder="Contoh: Budi Santoso">
                </div>
                <div class="div-form">
                    <label>Nomor Identitas (NIS/NIM/KTP):</label>
                    <input type="text" name="nomor_indentitas" required placeholder="Contoh: 2023001">
                </div>
                <div class="div-form">
                    <label>No. Telepon / WhatsApp:</label>
                    <input type="text" name="telepon" placeholder="Contoh: 081234567890">
                </div>
                <button type="submit" name="submit_anggota">Simpan Anggota</button>
            </form>
        </div>

        <!-- Tabel Daftar Anggota -->
        <div class="table-box">
            <h3>Daftar Anggota Terdaftar</h3>
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>No. Identitas</th>
                    <th>No. Telepon</th>
                    <th>Aksi</th>
                </tr>
                <?php 
                $no = 1;
                if (mysqli_num_rows($result_members) > 0) :
                    while ($row = mysqli_fetch_assoc($result_members)) : 
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td><?= htmlspecialchars($row['nomor_indentitas']); ?></td>
                    <td><?= htmlspecialchars($row['telepon']); ?></td>
                    <td>
                        <a href="hapus_anggota.php?id=<?= $row['id']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus anggota ini?')">Hapus</a>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                else :
                ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada data anggota.</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

</body>
</html>