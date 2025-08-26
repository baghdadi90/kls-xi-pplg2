<?php
/**
 * Koneksi PHP ke MySQL Database
 * File ini menunjukkan cara membuat koneksi ke database MySQL menggunakan PHP
 */

// Konfigurasi database
$host = "localhost";      // Host database (biasanya localhost)
$username = "root";       // Username database
$password = "";           // Password database
$database = "nama_database"; // Nama database

// Membuat koneksi menggunakan MySQLi (Object-Oriented)
$conn = new mysqli($host, $username, $password, $database);

// Check koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "Koneksi ke MySQL berhasil!<br>";

// Contoh eksekusi query
$sql = "SELECT VERSION() as version";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data dari setiap row
    while($row = $result->fetch_assoc()) {
        echo "Versi MySQL: " . $row["version"] . "<br>";
    }
} else {
    echo "0 results";
}

// Menutup koneksi
$conn->close();

// ==============================================
// ALTERNATIF: Koneksi menggunakan MySQLi (Procedural)
// ==============================================

echo "<br>=== Alternatif: MySQLi Procedural ===<br>";

// Membuat koneksi
$conn_procedural = mysqli_connect($host, $username, $password, $database);

// Check koneksi
if (!$conn_procedural) {
    die("Koneksi procedural gagal: " . mysqli_connect_error());
}

echo "Koneksi procedural berhasil!<br>";

// Contoh query
$result_procedural = mysqli_query($conn_procedural, $sql);

if (mysqli_num_rows($result_procedural) > 0) {
    while($row = mysqli_fetch_assoc($result_procedural)) {
        echo "Versi MySQL (procedural): " . $row["version"] . "<br>";
    }
}

// Menutup koneksi
mysqli_close($conn_procedural);

// ==============================================
// CONTOH PENGGUNAAN DENGAN PREPARED STATEMENT
// ==============================================

echo "<br>=== Contoh Prepared Statement ===<br>";

$conn2 = new mysqli($host, $username, $password, $database);

if ($conn2->connect_error) {
    die("Koneksi 2 gagal: " . $conn2->connect_error);
}

// Contoh prepared statement (lebih aman dari SQL injection)
$stmt = $conn2->prepare("SELECT ? as test");
$param = "Hello MySQL";
$stmt->bind_param("s", $param);
$stmt->execute();
$result2 = $stmt->get_result();

if ($result2->num_rows > 0) {
    while($row = $result2->fetch_assoc()) {
        echo "Test prepared statement: " . $row["test"] . "<br>";
    }
}

$stmt->close();
$conn2->close();

// ==============================================
// FUNGSI UNTUK KONEKSI DATABASE
// ==============================================

function koneksiDatabase() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "nama_database";
    
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }
    
    return $conn;
}

// Contoh penggunaan fungsi
try {
    $koneksi = koneksiDatabase();
    echo "<br>Koneksi melalui fungsi berhasil!";
    $koneksi->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
