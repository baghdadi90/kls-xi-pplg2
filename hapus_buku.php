<?php
include 'koneksi.php';

// Cek apakah parameter ID ada di URL
if (isset($_GET['id'])) {
    $id_buku = $_GET['id'];

    // Query hapus data buku
    $query = "DELETE FROM buku WHERE id_buku = '$id_buku'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Buku berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus buku.'); window.location='index.php';</script>";
    }
} else {
    header("location: index.php");
}
?>