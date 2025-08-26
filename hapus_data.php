<?php
/**
 * Hapus Data PHP
 * File untuk menghapus data dari database MySQL
 */

// Include file koneksi
require_once 'koneksi_mysql.php';

// Cek apakah parameter id dan table ada
if (isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table_name = $_GET['table'];

    try {
        $conn = koneksiDatabase();

        // Query untuk menghapus data
        $sql = "DELETE FROM $table_name WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<h2>Data berhasil dihapus!</h2>";
        } else {
            echo "<h2>Gagal menghapus data!</h2>";
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<h2>Parameter tidak valid!</h2>";
}

// Redirect back to tampil_data.php
echo "<br><a href='tampil_data.php'>Kembali ke Daftar Data</a>";
?>
