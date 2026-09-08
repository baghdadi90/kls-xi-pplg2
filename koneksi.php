<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_perpustakaan";

// Membuat koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek jika koneksi gagal
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>