<?php
session_start();

// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_showroom_mobil";

$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Buat database & tabel otomatis jika belum ada
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db`");
mysqli_select_db($conn, $db);

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `log_inventaris` (
  `id_log` INT(11) NOT NULL AUTO_INCREMENT,
  `waktu` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `model_kendaraan` VARCHAR(100) NOT NULL,
  `tipe_pergerakan` VARCHAR(50) NOT NULL,
  `jumlah_unit` INT(11) NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `petugas` VARCHAR(50) DEFAULT 'admin',
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `transaksi_pelanggan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `waktu` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pembeli_kontak` VARCHAR(100) NOT NULL,
  `kendaraan` VARCHAR(100) NOT NULL,
  `metode_bayar` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `petugas` VARCHAR(50) DEFAULT 'admin',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Sinkronisasi otomatis kolom 'petugas' jika tabel sudah ada sebelumnya
mysqli_query($conn, "ALTER TABLE `transaksi_pelanggan` ADD COLUMN IF NOT EXISTS `petugas` VARCHAR(50) DEFAULT 'admin'");
mysqli_query($conn, "ALTER TABLE `log_inventaris` ADD COLUMN IF NOT EXISTS `petugas` VARCHAR(50) DEFAULT 'admin'");

// Proses Login Otomatis sebagai "admin"
if (isset($_POST['login_action'])) {
    $_SESSION['user_login'] = 'admin';
    header("Location: index.php?page=input");
    exit();
}

// Proses Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Handle Tambah Data Transaksi
if (isset($_POST['tambah_transaksi']) && isset($_SESSION['user_login'])) {
    $pembeli   = mysqli_real_escape_string($conn, $_POST['pembeli_kontak']);
    $kendaraan = mysqli_real_escape_string($conn, $_POST['kendaraan']);
    $metode    = mysqli_real_escape_string($conn, $_POST['metode_bayar']);
    $status    = mysqli_real_escape_string($conn, $_POST['status']);
    $petugas   = mysqli_real_escape_string($conn, $_SESSION['user_login']);

    mysqli_query($conn, "INSERT INTO transaksi_pelanggan (pembeli_kontak, kendaraan, metode_bayar, status, petugas) VALUES ('$pembeli', '$kendaraan', '$metode', '$status', '$petugas')");
    header("Location: index.php?page=output&status=success_trx");
    exit();
}

// Handle Edit Data Transaksi
if (isset($_POST['edit_transaksi']) && isset($_SESSION['user_login'])) {
    $id_trx    = (int)$_POST['id_trx'];
    $pembeli   = mysqli_real_escape_string($conn, $_POST['pembeli_kontak']);
    $kendaraan = mysqli_real_escape_string($conn, $_POST['kendaraan']);
    $metode    = mysqli_real_escape_string($conn, $_POST['metode_bayar']);
    $status    = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "UPDATE transaksi_pelanggan SET pembeli_kontak='$pembeli', kendaraan='$kendaraan', metode_bayar='$metode', status='$status' WHERE id=$id_trx");
    header("Location: index.php?page=output&status=updated_trx");
    exit();
}

// Handle Tambah Data Log Inventaris
if (isset($_POST['tambah_log']) && isset($_SESSION['user_login'])) {
    $model   = mysqli_real_escape_string($conn, $_POST['model_kendaraan']);
    $tipe    = mysqli_real_escape_string($conn, $_POST['tipe_pergerakan']);
    $jumlah  = (int)$_POST['jumlah_unit'];
    $ket     = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $petugas = mysqli_real_escape_string($conn, $_SESSION['user_login']);

    mysqli_query($conn, "INSERT INTO log_inventaris (model_kendaraan, tipe_pergerakan, jumlah_unit, keterangan, petugas) VALUES ('$model', '$tipe', $jumlah, '$ket', '$petugas')");
    header("Location: index.php?page=output&status=success_log");
    exit();
}

// Handle Edit Data Log Inventaris
if (isset($_POST['edit_log']) && isset($_SESSION['user_login'])) {
    $id_log  = (int)$_POST['id_log'];
    $model   = mysqli_real_escape_string($conn, $_POST['model_kendaraan']);
    $tipe    = mysqli_real_escape_string($conn, $_POST['tipe_pergerakan']);
    $jumlah  = (int)$_POST['jumlah_unit'];
    $ket     = mysqli_real_escape_string($conn, $_POST['keterangan']);

    mysqli_query($conn, "UPDATE log_inventaris SET model_kendaraan='$model', tipe_pergerakan='$tipe', jumlah_unit=$jumlah, keterangan='$ket' WHERE id_log=$id_log");
    header("Location: index.php?page=output&status=updated_log");
    exit();
}

// Handle Fitur Hapus Data Transaksi
if (isset($_GET['hapus_trx']) && isset($_SESSION['user_login'])) {
    $id_trx = (int)$_GET['hapus_trx'];
    mysqli_query($conn, "DELETE FROM transaksi_pelanggan WHERE id = $id_trx");
    header("Location: index.php?page=output&status=deleted_trx");
    exit();
}

// Handle Fitur Hapus Data Log Inventaris
if (isset($_GET['hapus_log']) && isset($_SESSION['user_login'])) {
    $id_log = (int)$_GET['hapus_log'];
    mysqli_query($conn, "DELETE FROM log_inventaris WHERE id_log = $id_log");
    header("Location: index.php?page=output&status=deleted_log");
    exit();
}

// Navigasi Halaman
$page = isset($_GET['page']) ? $_GET['page'] : 'input';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ApexMotors Enterprise Suite</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --accent: #2563eb;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #ca8a04;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --text-main: #334155;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 20px;
        }
        .container { max-width: 1350px; margin: 0 auto; }

        .login-wrapper { display: flex; justify-content: center; align-items: center; height: 85vh; }
        .login-card { background: var(--card-bg); padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }

        .enterprise-header {
            background: var(--card-bg);
            padding: 20px 30px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .nav-tabs {
            display: flex;
            gap: 10px;
            background: #f1f5f9;
            padding: 5px;
            border-radius: 8px;
        }
        .nav-tabs a {
            padding: 8px 16px;
            text-decoration: none;
            color: #64748b;
            font-size: 0.88rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .nav-tabs a.active {
            background: var(--card-bg);
            color: var(--accent);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            font-weight: 600;
        }
        .logout-btn { background: #fee2e2; color: var(--danger); padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; }

        .section-card { background: var(--card-bg); border-radius: 12px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .section-title { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .section-title h2 { font-size: 1.15rem; color: var(--primary); margin: 0; border-left: 4px solid var(--accent); padding-left: 10px; }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        
        .metric-box { background: var(--card-bg); padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); border: 1px solid var(--border); }
        .metric-box h4 { margin: 0 0 8px 0; font-size: 0.85rem; color: #64748b; }
        .metric-box .value { font-size: 1.5rem; font-weight: 700; color: var(--primary); }

        .form-group { margin-bottom: 16px; text-align: left; }
        .form-group label { display: block; font-weight: 500; font-size: 0.85rem; margin-bottom: 6px; color: #475569; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.9rem; background: #fff; font-family: inherit; }
        
        .btn { background: var(--accent); color: white; border: none; padding: 11px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; width: 100%; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.9; }
        .btn-success { background: var(--success); }
        .btn-warning { background: var(--warning); color: white; }
        .btn-print { background: #475569; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500; text-decoration:none; display:inline-block;}
        
        .btn-action-group { display: flex; gap: 5px; justify-content: center; }
        .btn-edit { background: #e0f2fe; color: var(--accent); padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.78rem; font-weight: 600; border: 1px solid #bae6fd; display: inline-block; transition: background 0.2s; }
        .btn-edit:hover { background: var(--accent); color: white; }
        
        .btn-delete { background: #fee2e2; color: var(--danger); padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.78rem; font-weight: 600; border: 1px solid #fca5a5; display: inline-block; transition: background 0.2s; }
        .btn-delete:hover { background: var(--danger); color: white; }

        /* Search & Filter Bar */
        .filter-bar { display: flex; gap: 10px; margin-bottom: 15px; }
        .filter-bar input { padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 0.85rem; flex: 1; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.88rem; }
        th { background-color: #f8fafc; color: var(--primary); font-weight: 600; }
        tr:hover { background-color: #f8fafc; }
        .empty-row { text-align: center; color: #94a3b8; font-style: italic; padding: 30px; }

        /* Modal Popup Edit */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-card { background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative; }
        .modal-close { position: absolute; top: 15px; right: 20px; font-size: 1.2rem; cursor: pointer; color: #64748b; font-weight: bold; }

        .car-preview-container { margin-top: 15px; border: 1px dashed var(--border); border-radius: 8px; padding: 10px; text-align: center; background: #fafaf9; }
        .car-preview-container img { width: 100%; height: 150px; object-fit: cover; border-radius: 6px; display: none; }
        .car-preview-placeholder { font-size: 0.8rem; color: #94a3b8; padding: 30px 0; }

        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print { display: none !important; }
            .section-card { box-shadow: none; border: 1px solid #ddd; }
            .enterprise-header { border: none; box-shadow: none; padding: 0; margin-bottom: 20px; }
        }
    </style>
</head>
<body>

<div class="container">

<?php if (!isset($_SESSION['user_login'])): ?>
    <div class="login-wrapper">
        <div class="login-card">
            <h2>ApexMotors Corp.</h2>
            <p style="color:#64748b; font-size:0.9rem; margin-bottom:25px;">Sistem ERP Showroom (Login Instan)</p>
            <form method="POST" action="">
                <div class="form-group" style="text-align: center;">
                    <span style="display:inline-block; padding:10px 20px; background:#f1f5f9; border-radius:8px; font-weight:600; color:var(--primary); margin-bottom:15px; width:100%;">
                        👤 Masuk sebagai: <strong>admin</strong>
                    </span>
                </div>
                <button type="submit" name="login_action" class="btn" style="margin-top:5px;">Masuk ke Sistem</button>
            </form>
        </div>
    </div>
<?php else: ?>

    <div class="enterprise-header no-print">
        <div>
            <h1 style="margin:0; font-size: 1.3rem; color: var(--primary);">ApexMotors ERP Suite</h1>
            <span style="font-size: 0.8rem; color: #64748b;">Active Operator: <strong><?= htmlspecialchars($_SESSION['user_login']); ?></strong></span>
        </div>
        
        <div class="nav-tabs">
            <a href="?page=input" class="<?= ($page == 'input') ? 'active' : ''; ?>">📥 Halaman Input</a>
            <a href="?page=output" class="<?= ($page == 'output') ? 'active' : ''; ?>">📊 Halaman Output & Database</a>
            <a href="?page=hasil_output" class="<?= ($page == 'hasil_output') ? 'active' : ''; ?>">🖨️ Laporan & Cetak PDF</a>
        </div>

        <div>
            <a href="?logout=true" class="logout-btn">Keluar Sesi</a>
        </div>
    </div>

    <?php 
    // ==========================================
    // 1. HALAMAN INPUT DATA
    // ==========================================
    if ($page == 'input'): 
    ?>
        <div class="grid-2">
            <div class="section-card">
                <div class="section-title"><h2>Input Transaksi Penjualan</h2></div>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Nama & Kontak Pembeli</label>
                        <input type="text" name="pembeli_kontak" required placeholder="Contoh: Budi Santoso (08123456789)">
                    </div>
                    <div class="form-group">
                        <label>Model Kendaraan</label>
                        <input type="text" id="input_kendaraan_trx" name="kendaraan" required placeholder="Contoh: Toyota Fortuner GR Sport">
                    </div>
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <select name="metode_bayar">
                            <option value="Cash / Tunai">Cash / Tunai</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Kredit / Leasing">Kredit / Leasing Korporat</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Konfirmasi</label>
                        <select name="status">
                            <option value="Pending Verification">Pending Verification</option>
                            <option value="Approved / Lunas">Approved / Lunas</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="car-preview-container">
                        <div id="placeholder_trx" class="car-preview-placeholder">Preview Visual Kendaraan Real-Time...</div>
                        <img id="img_preview_trx" src="" alt="Real-time Car Preview">
                    </div>

                    <button type="submit" name="tambah_transaksi" class="btn btn-success" style="margin-top:20px;">Submit Transaksi Baru</button>
                </form>
            </div>

            <div class="section-card">
                <div class="section-title"><h2>Input Log Logistik Gudang</h2></div>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Model Kendaraan Unit</label>
                        <input type="text" id="input_kendaraan_log" name="model_kendaraan" required placeholder="Contoh: Mitsubishi Pajero Sport">
                    </div>
                    <div class="form-group">
                        <label>Tipe Pergerakan Logistik</label>
                        <select name="tipe_pergerakan">
                            <option value="Masuk">Masuk (Restock Pabrik)</option>
                            <option value="Keluar">Keluar (Distribusi Showroom)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kuantitas Unit</label>
                        <input type="number" name="jumlah_unit" value="1" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor Referensi / DO Pabrik</label>
                        <input type="text" name="keterangan" placeholder="Contoh: DO-LOG-2026-9989">
                    </div>

                    <div class="car-preview-container">
                        <div id="placeholder_log" class="car-preview-placeholder">Preview Visual Kendaraan Real-Time...</div>
                        <img id="img_preview_log" src="" alt="Real-time Car Preview">
                    </div>

                    <button type="submit" name="tambah_log" class="btn" style="margin-top:20px;">Submit Log Inventaris</button>
                </form>
            </div>
        </div>

    <?php 
    // ==========================================
    // 2. HALAMAN OUTPUT & KELOLA DATA (DENGAN FITUR EDIT & FILTER)
    // ==========================================
    elseif ($page == 'output'): 
        $res_trx = mysqli_query($conn, "SELECT * FROM transaksi_pelanggan ORDER BY id DESC");
        $res_log = mysqli_query($conn, "SELECT * FROM log_inventaris ORDER BY id_log DESC");
        
        $count_trx = mysqli_num_rows($res_trx);
        $count_log = mysqli_num_rows($res_log);
    ?>
        <div class="grid-3">
            <div class="metric-box">
                <h4>Total Transaksi Tercatat</h4>
                <div class="value"><?= $count_trx; ?> Transaksi</div>
            </div>
            <div class="metric-box">
                <h4>Total Log Pergerakan Gudang</h4>
                <div class="value"><?= $count_log; ?> Log Aktivitas</div>
            </div>
            <div class="metric-box">
                <h4>Fitur Baru Tersedia</h4>
                <div class="value" style="color:var(--accent); font-size:1.1rem; margin-top:5px;">✨ Edit & Filter Aktif</div>
            </div>
        </div>

        <!-- Tabel Output Transaksi -->
        <div class="section-card">
            <div class="section-title">
                <h2>Kelola Database: Transaksi Pelanggan</h2>
                <div style="display:flex; gap:10px;">
                    <a href="?page=hasil_output" class="btn-print">Buka Cetak PDF →</a>
                </div>
            </div>
            
            <div class="filter-bar">
                <input type="text" id="searchTrx" placeholder="🔍 Ketik untuk menyaring tabel transaksi secara instan..." onkeyup="filterTable('tableTrx', 'searchTrx')">
            </div>

            <table id="tableTrx">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Waktu Entri</th>
                        <th>Kontak Pembeli</th>
                        <th>Kendaraan</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th>Operator</th>
                        <th style="text-align: center;">Aksi (Edit / Hapus)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($count_trx > 0): while($r = mysqli_fetch_assoc($res_trx)): 
                        array_walk($r, function(&$val) { $val = htmlspecialchars($val, ENT_QUOTES); });
                    ?>
                    <tr>
                        <td>#<?= $r['id']; ?></td>
                        <td><?= $r['waktu']; ?></td>
                        <td><?= $r['pembeli_kontak']; ?></td>
                        <td><strong><?= $r['kendaraan']; ?></strong></td>
                        <td><?= $r['metode_bayar']; ?></td>
                        <td><?= $r['status']; ?></td>
                        <td><?= isset($r['petugas']) ? $r['petugas'] : 'admin'; ?></td>
                        <td style="text-align: center;">
                            <div class="btn-action-group">
                                <button onclick="openEditTrx('<?= $r['id']; ?>', '<?= $r['pembeli_kontak']; ?>', '<?= $r['kendaraan']; ?>', '<?= $r['metode_bayar']; ?>', '<?= $r['status']; ?>')" class="btn-edit">✏️ Edit</button>
                                <a href="?page=output&hapus_trx=<?= $r['id']; ?>" class="btn-delete" onclick="return confirm('Hapus data transaksi #<?= $r['id']; ?> ini?');">🗑️ Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="8" class="empty-row">Belum ada data transaksi yang dimasukkan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabel Output Log Inventaris -->
        <div class="section-card">
            <div class="section-title">
                <h2>Kelola Database: Log Inventaris Logistik</h2>
            </div>
            
            <div class="filter-bar">
                <input type="text" id="searchLog" placeholder="🔍 Ketik untuk menyaring log inventaris secara instan..." onkeyup="filterTable('tableLog', 'searchLog')">
            </div>

            <table id="tableLog">
                <thead>
                    <tr>
                        <th>ID Log</th>
                        <th>Waktu Entri</th>
                        <th>Model Kendaraan</th>
                        <th>Pergerakan</th>
                        <th>Jumlah</th>
                        <th>Keterangan / DO</th>
                        <th>Operator</th>
                        <th style="text-align: center;">Aksi (Edit / Hapus)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($res_log, 0);
                    if ($count_log > 0): while($r = mysqli_fetch_assoc($res_log)): 
                        array_walk($r, function(&$val) { $val = htmlspecialchars($val, ENT_QUOTES); });
                    ?>
                    <tr>
                        <td>#<?= $r['id_log']; ?></td>
                        <td><?= $r['waktu']; ?></td>
                        <td><strong><?= $r['model_kendaraan']; ?></strong></td>
                        <td>
                            <span style="color: <?= ($r['tipe_pergerakan'] == 'Masuk') ? 'var(--success)' : 'var(--danger)'; ?>; font-weight:600;">
                                <?= $r['tipe_pergerakan']; ?>
                            </span>
                        </td>
                        <td><?= $r['jumlah_unit']; ?> Unit</td>
                        <td><?= $r['keterangan']; ?></td>
                        <td><?= isset($r['petugas']) ? $r['petugas'] : 'admin'; ?></td>
                        <td style="text-align: center;">
                            <div class="btn-action-group">
                                <button onclick="openEditLog('<?= $r['id_log']; ?>', '<?= $r['model_kendaraan']; ?>', '<?= $r['tipe_pergerakan']; ?>', '<?= $r['jumlah_unit']; ?>', '<?= $r['keterangan']; ?>')" class="btn-edit">✏️ Edit</button>
                                <a href="?page=output&hapus_log=<?= $r['id_log']; ?>" class="btn-delete" onclick="return confirm('Hapus data log #<?= $r['id_log']; ?> ini?');">🗑️ Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="8" class="empty-row">Belum ada log inventaris yang dimasukkan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- MODAL POPUP EDIT TRANSAKSI -->
        <div id="modalEditTrx" class="modal-overlay">
            <div class="modal-card">
                <span class="modal-close" onclick="closeModal('modalEditTrx')">&times;</span>
                <h3 style="margin-top:0; color:var(--primary);">Edit Data Transaksi Pelanggan</h3>
                <form method="POST" action="">
                    <input type="hidden" name="id_trx" id="edit_id_trx">
                    <div class="form-group">
                        <label>Nama & Kontak Pembeli</label>
                        <input type="text" name="pembeli_kontak" id="edit_pembeli_kontak" required>
                    </div>
                    <div class="form-group">
                        <label>Model Kendaraan</label>
                        <input type="text" name="kendaraan" id="edit_kendaraan_trx" required>
                    </div>
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <select name="metode_bayar" id="edit_metode_bayar">
                            <option value="Cash / Tunai">Cash / Tunai</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Kredit / Leasing">Kredit / Leasing Korporat</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Konfirmasi</label>
                        <select name="status" id="edit_status">
                            <option value="Pending Verification">Pending Verification</option>
                            <option value="Approved / Lunas">Approved / Lunas</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" name="edit_transaksi" class="btn btn-warning" style="margin-top:15px;">Simpan Perubahan Transaksi</button>
                </form>
            </div>
        </div>

        <!-- MODAL POPUP EDIT LOG INVENTARIS -->
        <div id="modalEditLog" class="modal-overlay">
            <div class="modal-card">
                <span class="modal-close" onclick="closeModal('modalEditLog')">&times;</span>
                <h3 style="margin-top:0; color:var(--primary);">Edit Logistik & Inventaris Gudang</h3>
                <form method="POST" action="">
                    <input type="hidden" name="id_log" id="edit_id_log">
                    <div class="form-group">
                        <label>Model Kendaraan Unit</label>
                        <input type="text" name="model_kendaraan" id="edit_model_kendaraan" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Pergerakan Logistik</label>
                        <select name="tipe_pergerakan" id="edit_tipe_pergerakan">
                            <option value="Masuk">Masuk (Restock Pabrik)</option>
                            <option value="Keluar">Keluar (Distribusi Showroom)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kuantitas Unit</label>
                        <input type="number" name="jumlah_unit" id="edit_jumlah_unit" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor Referensi / DO Pabrik</label>
                        <input type="text" name="keterangan" id="edit_keterangan">
                    </div>
                    <button type="submit" name="edit_log" class="btn btn-warning" style="margin-top:15px;">Simpan Perubahan Log</button>
                </form>
            </div>
        </div>

    <?php 
    // ==========================================
    // 3. HALAMAN HASIL OUTPUT (CETAK / LAPORAN)
    // ==========================================
    elseif ($page == 'hasil_output'): 
        $res_trx = mysqli_query($conn, "SELECT * FROM transaksi_pelanggan ORDER BY id DESC");
        $res_log = mysqli_query($conn, "SELECT * FROM log_inventaris ORDER BY id_log DESC");
    ?>
        <div class="section-card">
            <div class="section-title">
                <div>
                    <h2 style="margin:0; font-size:1.3rem;">LAPORAN RESMI KORPORAT - APEXMOTORS CORP.</h2>
                    <p style="margin:4px 0 0 0; font-size:0.85rem; color:#64748b;">Dokumen Resmi Hasil Output Sistem ERP Showroom (Dicetak pada: <?= date('d-m-Y H:i:s'); ?>)</p>
                </div>
                <div style="display:flex; gap:10px;" class="no-print">
                    <button onclick="exportToCSV()" class="btn" style="background:var(--success); font-weight:600; padding:10px 15px; width:auto;">📥 Ekspor ke Excel (.CSV)</button>
                    <button onclick="window.print();" class="btn-print" style="background:var(--accent); font-weight:600; padding:10px 20px;">🖨️ Cetak / Download PDF</button>
                </div>
            </div>

            <h3 style="margin-top:25px; color:var(--primary); font-size:1rem; border-bottom:1px solid #cbd5e1; padding-bottom:8px;">A. Rekapitulasi Transaksi Pelanggan</h3>
            <table id="exportTrxTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Waktu</th>
                        <th>Pembeli</th>
                        <th>Kendaraan</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($res_trx) > 0): while($r = mysqli_fetch_assoc($res_trx)): ?>
                    <tr>
                        <td>#<?= $r['id']; ?></td>
                        <td><?= $r['waktu']; ?></td>
                        <td><?= htmlspecialchars($r['pembeli_kontak']); ?></td>
                        <td><?= htmlspecialchars($r['kendaraan']); ?></td>
                        <td><?= htmlspecialchars($r['metode_bayar']); ?></td>
                        <td><strong><?= htmlspecialchars($r['status']); ?></strong></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" class="empty-row">Tidak ada data transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h3 style="margin-top:35px; color:var(--primary); font-size:1rem; border-bottom:1px solid #cbd5e1; padding-bottom:8px;">B. Rekapitulasi Logistik & Inventaris Gudang</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Log</th>
                        <th>Waktu</th>
                        <th>Model Kendaraan</th>
                        <th>Pergerakan</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($res_log) > 0): while($r = mysqli_fetch_assoc($res_log)): ?>
                    <tr>
                        <td>#<?= $r['id_log']; ?></td>
                        <td><?= $r['waktu']; ?></td>
                        <td><?= htmlspecialchars($r['model_kendaraan']); ?></td>
                        <td><?= htmlspecialchars($r['tipe_pergerakan']); ?></td>
                        <td><?= $r['jumlah_unit']; ?> Unit</td>
                        <td><?= htmlspecialchars($r['keterangan']); ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" class="empty-row">Tidak ada data log inventaris.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div style="margin-top: 50px; display: flex; justify-content: flex-end; text-align: center;">
                <div style="width: 250px;">
                    <p style="margin:0; font-size:0.9rem;">Padang, <?= date('d F Y'); ?></p>
                    <p style="margin:5px 0 50px 0; font-size:0.9rem; font-weight:600;">Direktur Operasional</p>
                    <p style="margin:0; font-size:0.9rem; font-weight:bold; text-decoration: underline;"><?= htmlspecialchars($_SESSION['user_login']); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

</div>

<script>
// Real-time Car Image Preview
function setupCarPreview(inputId, imgId, placeholderId) {
    const input = document.getElementById(inputId);
    const img = document.getElementById(imgId);
    const placeholder = document.getElementById(placeholderId);
    if(!input) return;

    let timeout = null;
    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value.trim();
        if (query.length > 2) {
            timeout = setTimeout(() => {
                img.src = `https://source.unsplash.com/featured/600x400/?${encodeURIComponent(query + ' car')}`;
                img.style.display = 'block';
                placeholder.style.display = 'none';
            }, 500);
        } else {
            img.style.display = 'none';
            placeholder.style.display = 'block';
        }
    });
}
setupCarPreview('input_kendaraan_trx', 'img_preview_trx', 'placeholder_trx');
setupCarPreview('input_kendaraan_log', 'img_preview_log', 'placeholder_log');

// Modal Edit Control Functions
function openEditTrx(id, pembeli, kendaraan, metode, status) {
    document.getElementById('edit_id_trx').value = id;
    document.getElementById('edit_pembeli_kontak').value = pembeli;
    document.getElementById('edit_kendaraan_trx').value = kendaraan;
    document.getElementById('edit_metode_bayar').value = metode;
    document.getElementById('edit_status').value = status;
    document.getElementById('modalEditTrx').style.display = 'flex';
}

function openEditLog(id, model, tipe, jumlah, ket) {
    document.getElementById('edit_id_log').value = id;
    document.getElementById('edit_model_kendaraan').value = model;
    document.getElementById('edit_tipe_pergerakan').value = tipe;
    document.getElementById('edit_jumlah_unit').value = jumlah;
    document.getElementById('edit_keterangan').value = ket;
    document.getElementById('modalEditLog').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Live Filter Table Function
function filterTable(tableId, inputId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const trs = table.getElementsByTagName('tr');

    for (let i = 1; i < trs.length; i++) {
        let visible = false;
        const tds = trs[i].getElementsByTagName('td');
        for (let j = 0; j < tds.length - 1; j++) {
            if (tds[j] && tds[j].innerText.toLowerCase().includes(filter)) {
                visible = true;
                break;
            }
        }
        trs[i].style.display = visible ? '' : 'none';
    }
}

// Export to CSV Function
function exportToCSV() {
    let csv = [];
    let rows = document.querySelectorAll("#exportTrxTable tr");
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length; j++) 
            row.push('"' + cols[j].innerText + '"');
        csv.push(row.join(","));
    }
    let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    let downloadLink = document.createElement("a");
    downloadLink.download = "Laporan_Transaksi_ApexMotors.csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

</body>
</html>