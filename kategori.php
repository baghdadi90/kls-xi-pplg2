<?php
include 'koneksi.php';

// Proses Tambah Kategori
if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);

    $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Kategori berhasil ditambahkan!'); window.location='kategori.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan kategori.');</script>";
    }
}

// Proses Hapus Kategori
if (isset($_GET['hapus'])) {
    $id_kategori = $_GET['hapus'];
    $query_hapus = "DELETE FROM kategori WHERE id_kategori = '$id_kategori'";
    
    if (mysqli_query($conn, $query_hapus)) {
        echo "<script>alert('Kategori berhasil dihapus!'); window.location='kategori.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus kategori (mungkin masih digunakan oleh buku).'); window.location='kategori.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">📚 Perpustakaan Digital</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Katalog Buku</a>
                <a class="nav-link active" href="kategori.php">Kategori Buku</a>
                <a class="nav-link" href="peminjaman.php">Form Peminjaman</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <!-- Form Tambah Kategori -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary fw-bold">Tambah Kategori</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nama Kategori</label>
                                <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Sejarah" required>
                            </div>
                            <button type="submit" name="tambah_kategori" class="btn btn-success w-100">Simpan Kategori</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar Kategori -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary fw-bold">Daftar Kategori Buku</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kategori</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM kategori");
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td class="fw-semibold"><?= $row['nama_kategori']; ?></td>
                                        <td>
                                            <a href="kategori.php?hapus=<?= $row['id_kategori']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>