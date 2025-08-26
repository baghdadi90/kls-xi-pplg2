<?php
/**
 * Tampil dan Edit Data PHP
 * File untuk menampilkan dan mengedit data dari database MySQL
 */

// Include file koneksi
require_once 'koneksi_mysql.php';

// Fungsi untuk menampilkan data dari tabel
function tampilData($table_name) {
    try {
        $conn = koneksiDatabase();
        
        // Query untuk menampilkan data
        $sql = "SELECT * FROM $table_name";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo "<h2>Data dari tabel: $table_name</h2>";
            echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
            
            // Header tabel
            echo "<tr style='background-color: #f2f2f2;'>";
            $field_info = $result->fetch_fields();
            foreach ($field_info as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "<th>Aksi</th>";
            echo "</tr>";
            
            // Data tabel
            $result->data_seek(0); // Reset pointer
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach($row as $key => $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "<td>";
                echo "<a href='edit_data.php?id=" . $row['id'] . "&table=$table_name' style='margin-right: 10px;'>Edit</a>";
                echo "<a href='hapus_data.php?id=" . $row['id'] . "&table=$table_name' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Tidak ada data dalam tabel $table_name</p>";
        }
        
        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fungsi untuk mendapatkan daftar tabel
function getTableList() {
    try {
        $conn = koneksiDatabase();
        
        $sql = "SHOW TABLES";
        $result = $conn->query($sql);
        
        $tables = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
        }
        
        $conn->close();
        return $tables;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        return array();
    }
}

// Fungsi untuk menambahkan data
function tambahDataForm($table_name) {
    try {
        $conn = koneksiDatabase();
        
        // Dapatkan struktur tabel
        $sql = "DESCRIBE $table_name";
        $result = $conn->query($sql);
        
        echo "<h2>Tambah Data ke Tabel: $table_name</h2>";
        echo "<form method='POST' action='proses_tambah.php'>";
        echo "<input type='hidden' name='table' value='$table_name'>";
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $field = $row['Field'];
                $type = $row['Type'];
                $null = $row['Null'];
                $key = $row['Key'];
                
                // Skip auto-increment fields
                if (strpos($row['Extra'], 'auto_increment') !== false) {
                    continue;
                }
                
                echo "<div style='margin-bottom: 10px;'>";
                echo "<label for='$field'>$field:</label><br>";
                
                // Tentukan tipe input berdasarkan tipe field
                if (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false) {
                    echo "<input type='number' id='$field' name='$field'";
                } elseif (strpos($type, 'date') !== false) {
                    echo "<input type='date' id='$field' name='$field'";
                } elseif (strpos($type, 'text') !== false || strpos($type, 'varchar') !== false) {
                    echo "<input type='text' id='$field' name='$field'";
                } else {
                    echo "<input type='text' id='$field' name='$field'";
                }
                
                if ($null === 'NO' && $key !== 'PRI') {
                    echo " required";
                }
                
                echo ">";
                echo "</div>";
            }
        }
        
        echo "<button type='submit' style='padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px;'>Tambah Data</button>";
        echo "</form>";
        
        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Main program
echo "<!DOCTYPE html>";
echo "<html lang='id'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Sistem Tampil dan Edit Data</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo "table { margin-bottom: 20px; }";
echo "th, td { padding: 10px; text-align: left; }";
echo "a { text-decoration: none; color: #007bff; }";
echo "a:hover { text-decoration: underline; }";
echo ".container { max-width: 1200px; margin: 0 auto; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1>Sistem Tampil dan Edit Data</h1>";

// Dapatkan daftar tabel
$tables = getTableList();

if (!empty($tables)) {
    echo "<h2>Pilih Tabel:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><a href='?table=$table'>$table</a></li>";
    }
    echo "</ul>";
    
    // Tampilkan data jika tabel dipilih
    if (isset($_GET['table'])) {
        $selected_table = $_GET['table'];
        
        // Tampilkan data
        tampilData($selected_table);
        
        // Form tambah data
        echo "<br>";
        tambahDataForm($selected_table);
    }
} else {
    echo "<p>Tidak ada tabel dalam database. Pastikan database sudah dibuat dan berisi tabel.</p>";
}

echo "</div>";
echo "</body>";
echo "</html>";
?>
