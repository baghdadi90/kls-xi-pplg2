<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM members WHERE id = $id");
    header("Location: anggota.php");
    exit;
}
?>