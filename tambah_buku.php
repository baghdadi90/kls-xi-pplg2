<?php
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $judul    = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis  = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun    = (int)$_POST['tahun'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $stok     = (int)$_POST['stok'];

    $query = "INSERT INTO books (judul_buku, penulis, penerbit, tahun, kategori, stok) 
              VALUES ('$judul', '$penulis', '$penerbit', '$tahun', '$kategori', '$stok')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal menambah data: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Buku</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { width: 400px; background: #f9f9f9; padding: 20px; border: 1px solid #ccc; }
        div { margin-bottom: 10px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #0275d8; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h3>Form Tambah Buku Baru</h3>
    <form action="" method="POST">
        <div><label>Judul Buku:</label><input type="text" name="judul" required></div>
        <div><label>Penulis:</label><input type="text" name="penulis" required></div>
        <div><label>Penerbit:</label><input type="text" name="penerbit" required></div>
        <div><label>Tahun Terbit:</label><input type="number" name="tahun" required></div>
        <div><label>Kategori:</label><input type="text" name="kategori" required></div>
        <div><label>Stok:</label><input type="number" name="stok" required></div>
        <button type="submit" name="submit">Simpan Buku</button>
    </form>
    <br><a href="index.php">&laquo; Kembali ke Katalog</a>
</body>
</html>