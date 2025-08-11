<?php
require 'function.php';

// Set default timezone
date_default_timezone_set('Asia/Jakarta');

// ======================
// PROSES KAS MASUK
// ======================
if (isset($_POST['tambah_event'])) {
    $Tanggal_Input = $_POST['Tanggal_Input'];
    $Event_WLE = $_POST['Event_WLE'];
    $Keterangan = $_POST['Keterangan'];
    $Nominal = $_POST['Nominal'];

    if ($Tanggal_Input && $Event_WLE && $Keterangan && $Nominal) {
        $cek = mysqli_query($conn, "SELECT * FROM penerimaan_kas 
            WHERE Tanggal_Input='$Tanggal_Input' AND Event_WLE='$Event_WLE' 
            AND Keterangan='$Keterangan' AND Nominal='$Nominal'");
        if (mysqli_num_rows($cek) == 0) {
            $query = mysqli_query($conn, "INSERT INTO penerimaan_kas 
                (Tanggal_Input, Event_WLE, Keterangan, Nominal) 
                VALUES ('$Tanggal_Input', '$Event_WLE', '$Keterangan', '$Nominal')");
            if ($query) {
                header("Location: ".$_SERVER['PHP_SELF']."?tab=kas-masuk&success=1");
                exit;
            }
        }
    }
}

// ======================
// PROSES KAS KELUAR
// ======================
if (isset($_POST['tambah_pengeluaran'])) {
    $Tanggal_Input = $_POST['Tanggal_Input'];
    $Event_WLE = $_POST['Event_WLE'];
    $Keterangan = $_POST['Keterangan'];
    $Nama_Akun = $_POST['Nama_Akun'];
    $Nominal = $_POST['Nominal'];

    if ($Tanggal_Input && $Keterangan && $Nominal) {
        $query = mysqli_query($conn, "INSERT INTO pengeluaran_kas 
            (Tanggal_Input, Event_WLE, Keterangan, Nama_Akun, Nominal) 
            VALUES ('$Tanggal_Input', '$Event_WLE', '$Keterangan', '$Nama_Akun', '$Nominal')");
        if ($query) {
            header("Location: ".$_SERVER['PHP_SELF']."?tab=kas-keluar&success=1");
            exit;
        }
    }
}

// ======================
// PROSES HAPUS KAS MASUK
// ======================
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $query_hapus = mysqli_query($conn, "DELETE FROM penerimaan_kas WHERE id = '$id_hapus'");
    if ($query_hapus) {
        header("Location: ".$_SERVER['PHP_SELF']."?tab=kas-masuk&deleted=1");
        exit;
    }
}

// ======================
// PROSES EDIT KAS MASUK
// ======================
if (isset($_POST['update_event'])) {
    $id = $_POST['id'];
    $Tanggal_Input = $_POST['Tanggal_Input'];
    $Event_WLE = $_POST['Event_WLE'];
    $Keterangan = $_POST['Keterangan'];
    $Nominal = $_POST['Nominal'];

    $query_update = mysqli_query($conn, "UPDATE penerimaan_kas 
        SET Tanggal_Input='$Tanggal_Input', Event_WLE='$Event_WLE', 
            Keterangan='$Keterangan', Nominal='$Nominal' 
        WHERE id='$id'");

    if ($query_update) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=kas-masuk&updated=1");
        exit;
    }
}

// ======================
// PROSES HAPUS KAS KELUAR
// ======================
if (isset($_GET['hapus_pengeluaran'])) {
    $id_hapus = $_GET['hapus_pengeluaran'];
    $query_hapus = mysqli_query($conn, "DELETE FROM pengeluaran_kas WHERE id = '$id_hapus'");
    if ($query_hapus) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=kas-keluar&deleted=1");
        exit;
    }
}

// ======================
// PROSES EDIT KAS KELUAR
// ======================
if (isset($_POST['update_pengeluaran'])) {
    $id = $_POST['id'];
    $Tanggal_Input = $_POST['Tanggal_Input'];
    $Event_WLE = $_POST['Event_WLE'];
    $Keterangan = $_POST['Keterangan'];
    $Nama_Akun = $_POST['Nama_Akun'];
    $Nominal = $_POST['Nominal'];

    $query_update = mysqli_query($conn, "UPDATE pengeluaran_kas 
        SET Tanggal_Input='$Tanggal_Input', Event_WLE='$Event_WLE', 
            Keterangan='$Keterangan', Nama_Akun='$Nama_Akun', Nominal='$Nominal'
        WHERE id='$id'");
    
    if ($query_update) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=kas-keluar&updated=1");
        exit;
    }
}

// ======================
// PROSES BOOKING EVENT
// ======================
if (isset($_POST['jadwal_booking'])) {
    $Tanggal = mysqli_real_escape_string($conn, $_POST['Tanggal']);
    $Event   = mysqli_real_escape_string($conn, $_POST['Event']);
    $Paket   = mysqli_real_escape_string($conn, $_POST['Paket']);

    $query = mysqli_query($conn, "INSERT INTO jadwal_booking (Tanggal, Event, Paket) 
                                  VALUES ('$Tanggal', '$Event', '$Paket')");
    if ($query) {
        header("Location: ".$_SERVER['PHP_SELF']."?tab=booking&success=1");
        exit;
    }
}

// ======================
// PROSES HAPUS JADWAL BOOKING
// ======================
if (isset($_GET['hapus_booking'])) {
    $id_hapus = $_GET['hapus_booking'];
    $query_hapus = mysqli_query($conn, "DELETE FROM jadwal_booking WHERE id = '$id_hapus'");
    if ($query_hapus) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=booking&deleted=1");
        exit;
    }
}

// ======================
// PROSES EDIT BOOKING
// ======================
if (isset($_POST['update_booking'])) {
    $id = $_POST['id'];
    $Tanggal = $_POST['Tanggal'];
    $Event = $_POST['Event'];
    $Paket = $_POST['Paket'];
    
    $query_update = mysqli_query($conn, "UPDATE jadwal_booking
        SET Tanggal='$Tanggal', Event='$Event', 
            Paket='$Paket' WHERE id='$id'");
    
    if ($query_update) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=booking&updated=1");
        exit;
    }
}

// Get current tab
$current_tab = $_GET['tab'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Cashflow Management - Dekorasi Graceful</title>
    
    <!-- External CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .sidebar {
            background: var(--primary-color);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s;
        }

        .main-content {
            margin-left: 250px;
            transition: all 0.3s;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .nav-link {
            color: #bdc3c7 !important;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--secondary-color);
            color: white !important;
            transform: translateX(5px);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.3s;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }

        .btn {
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-10px);
        }

        .stats-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .welcome-header {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .gallery-img {
            border-radius: 10px;
            transition: transform 0.3s;
            height: 200px;
            object-fit: cover;
        }

        .gallery-img:hover {
            transform: scale(1.05);
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-4">
            <h4 class="navbar-brand mb-4">
                <i class="fas fa-palette me-2"></i>
                Dekorasi Graceful
            </h4>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= $current_tab === 'dashboard' ? 'active' : '' ?>" href="?tab=dashboard">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_tab === 'kas-masuk' ? 'active' : '' ?>" href="?tab=kas-masuk">
                        <i class="fas fa-arrow-up me-2"></i> Kas Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_tab === 'kas-keluar' ? 'active' : '' ?>" href="?tab=kas-keluar">
                        <i class="fas fa-arrow-down me-2"></i> Kas Keluar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_tab === 'booking' ? 'active' : '' ?>" href="?tab=booking">
                        <i class="fas fa-calendar me-2"></i> Jadwal Booking
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_tab === 'laporan' ? 'active' : '' ?>" href="?tab=laporan">
                        <i class="fas fa-chart-bar me-2"></i> Laporan Keuangan
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="mt-auto p-4">
            <div class="text-center">
                <small class="text-light">
                    <i class="fas fa-user me-1"></i>
                    Admin Keuangan
                </small>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid p-4">
            <!-- Success/Error Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    Data berhasil disimpan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-edit me-2"></i>
                    Data berhasil diperbarui!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-trash me-2"></i>
                    Data berhasil dihapus!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Dashboard Content -->
            <?php if ($current_tab === 'dashboard'): ?>
                <div class="fade-in">
                    <div class="welcome-header">
                        <h1 class="display-4 text-primary mb-3">
                            <i class="fas fa-star me-3"></i>
                            Selamat Datang, Admin!
                        </h1>
                        <p class="lead text-muted">Sistem Manajemen Keuangan Dekorasi Graceful</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <?php
                        $total_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(Nominal) as total FROM penerimaan_kas"))['total'] ?? 0;
                        $total_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(Nominal) as total FROM pengeluaran_kas"))['total'] ?? 0;
                        $total_booking = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM jadwal_booking"));
                        $saldo = $total_masuk - $total_keluar;
                        ?>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="stats-icon text-success">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <h5>Total Kas Masuk</h5>
                                <h3 class="text-success">Rp <?= number_format($total_masuk, 0, ',', '.') ?></h3>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="stats-icon text-danger">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <h5>Total Kas Keluar</h5>
                                <h3 class="text-danger">Rp <?= number_format($total_keluar, 0, ',', '.') ?></h3>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="stats-icon text-primary">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <h5>Saldo</h5>
                                <h3 class="<?= $saldo >= 0 ? 'text-success' : 'text-danger' ?>">
                                    Rp <?= number_format($saldo, 0, ',', '.') ?>
                                </h3>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="stats-icon text-warning">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <h5>Total Booking</h5>
                                <h3 class="text-warning"><?= $total_booking ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Section -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-images me-2"></i>
                                Galeri Dekorasi Graceful
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php 
                                $dekor_images = ['Dekor2.jpg', 'Dekor4.jpg', 'Dekor13.jpg'];
                                foreach ($dekor_images as $index => $image): 
                                    if (file_exists($image)):
                                ?>
                                    <div class="col-md-4 mb-3">
                                        <img src="<?= $image ?>" class="img-fluid gallery-img w-100" alt="Dekorasi <?= $index + 1 ?>">
                                    </div>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            <div class="text-center mt-4">
                                <p class="text-muted">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Kp. Tanjung Jaya, Desa Dawagung, Kec. Rajapolah, Kab. Tasikmalaya, Jawa Barat
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($current_tab === 'kas-masuk'): ?>
                <!-- Kas Masuk Section -->
                <div class="fade-in">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-arrow-up text-success me-2"></i>Manajemen Kas Masuk</h2>
                    </div>

                    <!-- Input Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Tambah Kas Masuk</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" name="Tanggal_Input" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Nama Event</label>
                                        <input type="text" name="Event_WLE" class="form-control" placeholder="Nama Event" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text" name="Keterangan" class="form-control" placeholder="Keterangan detail" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Nominal (Rp)</label>
                                        <input type="number" name="Nominal" class="form-control" placeholder="0" required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" name="tambah_event" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Data Penerimaan Kas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="kasmasukTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Event</th>
                                            <th>Keterangan</th>
                                            <th>Nominal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $data_event = mysqli_query($conn, "SELECT * FROM penerimaan_kas ORDER BY Tanggal_Input DESC");
                                        while ($row = mysqli_fetch_assoc($data_event)):
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['Tanggal_Input'])) ?></td>
                                            <td><?= htmlspecialchars($row['Event_WLE']) ?></td>
                                            <td><?= htmlspecialchars($row['Keterangan']) ?></td>
                                            <td class="text-success fw-bold">Rp <?= number_format($row['Nominal'], 0, ',', '.') ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1" 
                                                    onclick="editKasMasuk(<?= $row['id'] ?>, '<?= $row['Tanggal_Input'] ?>', '<?= addslashes($row['Event_WLE']) ?>', '<?= addslashes($row['Keterangan']) ?>', <?= $row['Nominal'] ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="?tab=kas-masuk&hapus=<?= $row['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Print Report -->
                    <div class="mt-4">
                        <form action="cetak_kasmasuk.php" method="get" target="_blank" class="d-flex gap-3 align-items-end">
                            <div>
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-select" required>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= sprintf('%02d', $i) ?>" <?= date('m') == $i ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun" class="form-control" min="2020" max="2100" value="<?= date('Y') ?>" required>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-print me-2"></i>Cetak Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php elseif ($current_tab === 'kas-keluar'): ?>
                <!-- Kas Keluar Section -->
                <div class="fade-in">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-arrow-down text-danger me-2"></i>Manajemen Kas Keluar</h2>
                    </div>

                    <!-- Input Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Tambah Kas Keluar</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" name="Tanggal_Input" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Event</label>
                                        <input type="text" name="Event_WLE" class="form-control" placeholder="Event">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text" name="Keterangan" class="form-control" placeholder="Keterangan" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Kategori</label>
                                        <select name="Nama_Akun" class="form-select" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="Rental">Rental</option>
                                            <option value="Bensin">Bensin</option>
                                            <option value="Peralatan">Peralatan</option>
                                            <option value="Konsumsi">Konsumsi</option>
                                            <option value="Modal">Modal</option>
                                            <option value="Dll">Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Nominal (Rp)</label>
                                        <input type="number" name="Nominal" class="form-control" placeholder="0" required>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" name="tambah_pengeluaran" class="btn btn-danger w-100">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Data Pengeluaran Kas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="kaskeluarTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Event</th>
                                            <th>Keterangan</th>
                                            <th>Kategori</th>
                                            <th>Nominal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $data_pengeluaran = mysqli_query($conn, "SELECT * FROM pengeluaran_kas ORDER BY Tanggal_Input DESC");
                                        while ($row = mysqli_fetch_assoc($data_pengeluaran)):
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['Tanggal_Input'])) ?></td>
                                            <td><?= htmlspecialchars($row['Event_WLE'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['Keterangan']) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['Nama_Akun']) ?></span></td>
                                            <td class="text-danger fw-bold">Rp <?= number_format($row['Nominal'], 0, ',', '.') ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1" 
                                                    onclick="editKasKeluar(<?= $row['id'] ?>, '<?= $row['Tanggal_Input'] ?>', '<?= addslashes($row['Event_WLE']) ?>', '<?= addslashes($row['Keterangan']) ?>', '<?= addslashes($row['Nama_Akun']) ?>', <?= $row['Nominal'] ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="?tab=kas-keluar&hapus_pengeluaran=<?= $row['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($current_tab === 'booking'): ?>
                <!-- Booking Section -->
                <div class="fade-in">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-calendar text-warning me-2"></i>Manajemen Jadwal Booking</h2>
                    </div>

                    <!-- Input Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Tambah Booking Event</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Tanggal Booking</label>
                                        <input type="date" name="Tanggal" class="form-control" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Nama Event</label>
                                        <input type="text" name="Event" class="form-control" placeholder="Contoh: Wedding Andi & Rina" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Paket Dekorasi</label>
                                        <select name="Paket" class="form-select" required>
                                            <option value="">Pilih Paket</option>
                                            <option value="Silver">Silver Package</option>
                                            <option value="Gold">Gold Package</option>
                                            <option value="Platinum">Platinum Package</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" name="jadwal_booking" class="btn btn-success w-100">
                                            <i class="fas fa-calendar-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Data Booking Events</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="bookingTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nama Event</th>
                                            <th>Paket</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $data_booking = mysqli_query($conn, "SELECT * FROM jadwal_booking ORDER BY Tanggal ASC");
                                        while ($row = mysqli_fetch_assoc($data_booking)):
                                            $tanggal_booking = strtotime($row['Tanggal']);
                                            $today = strtotime(date('Y-m-d'));
                                            $status = '';
                                            $status_class = '';
                                            
                                            if ($tanggal_booking < $today) {
                                                $status = 'Selesai';
                                                $status_class = 'bg-success';
                                            } elseif ($tanggal_booking == $today) {
                                                $status = 'Hari Ini';
                                                $status_class = 'bg-warning';
                                            } else {
                                                $status = 'Mendatang';
                                                $status_class = 'bg-info';
                                            }
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['Tanggal'])) ?></td>
                                            <td><?= htmlspecialchars($row['Event']) ?></td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($row['Paket']) ?></span></td>
                                            <td><span class="badge <?= $status_class ?>"><?= $status ?></span></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1" 
                                                    onclick="editBooking(<?= $row['id'] ?>, '<?= $row['Tanggal'] ?>', '<?= addslashes($row['Event']) ?>', '<?= addslashes($row['Paket']) ?>')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="?tab=booking&hapus_booking=<?= $row['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Yakin ingin menghapus booking ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($current_tab === 'laporan'): ?>
                <!-- Laporan Section -->
                <div class="fade-in">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-chart-bar text-info me-2"></i>Laporan Keuangan</h2>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <?php
                        $bulan_ini = date('Y-m');
                        $masuk_bulan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(Nominal) AS total FROM penerimaan_kas WHERE DATE_FORMAT(Tanggal_Input, '%Y-%m') = '$bulan_ini'"))['total'] ?? 0;
                        $keluar_bulan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(Nominal) AS total FROM pengeluaran_kas WHERE DATE_FORMAT(Tanggal_Input, '%Y-%m') = '$bulan_ini'"))['total'] ?? 0;
                        $saldo_bulan = $masuk_bulan - $keluar_bulan;
                        ?>
                        
                        <div class="col-md-4">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5>Kas Masuk Bulan Ini</h5>
                                    <h3>Rp <?= number_format($masuk_bulan, 0, ',', '.') ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card text-white bg-danger">
                                <div class="card-body">
                                    <h5>Kas Keluar Bulan Ini</h5>
                                    <h3>Rp <?= number_format($keluar_bulan, 0, ',', '.') ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card text-white <?= $saldo_bulan >= 0 ? 'bg-primary' : 'bg-warning' ?>">
                                <div class="card-body">
                                    <h5>Saldo Bulan Ini</h5>
                                    <h3>Rp <?= number_format($saldo_bulan, 0, ',', '.') ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Report -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Laporan Gabungan Kas Masuk & Keluar</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="laporanTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Jenis</th>
                                            <th>Event</th>
                                            <th>Keterangan</th>
                                            <th>Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query_union = "
                                            SELECT Tanggal_Input, 'Kas Masuk' AS Jenis, Event_WLE, Keterangan, Nominal, 'success' as class
                                            FROM penerimaan_kas
                                            UNION ALL
                                            SELECT Tanggal_Input, 'Kas Keluar' AS Jenis, Event_WLE, Keterangan, Nominal, 'danger' as class
                                            FROM pengeluaran_kas
                                            ORDER BY Tanggal_Input DESC
                                        ";
                                        $result_union = mysqli_query($conn, $query_union);
                                        
                                        while ($row = mysqli_fetch_assoc($result_union)):
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['Tanggal_Input'])) ?></td>
                                            <td><span class="badge bg-<?= $row['class'] ?>"><?= $row['Jenis'] ?></span></td>
                                            <td><?= htmlspecialchars($row['Event_WLE'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['Keterangan']) ?></td>
                                            <td class="text-<?= $row['class'] ?> fw-bold">
                                                <?= $row['Jenis'] == 'Kas Masuk' ? '+' : '-' ?>Rp <?= number_format($row['Nominal'], 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Download Report Button -->
                    <div class="text-end mt-3">
                        <a href="cetaklaporan.php" class="btn btn-primary" target="_blank">
                            <i class="fas fa-download me-2"></i>Download Laporan PDF
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for Edit Kas Masuk -->
    <div class="modal fade" id="editKasMasukModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kas Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="Tanggal_Input" id="edit-tanggal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Event</label>
                            <input type="text" name="Event_WLE" id="edit-event" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="Keterangan" id="edit-keterangan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal</label>
                            <input type="number" name="Nominal" id="edit-nominal" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_event" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Edit Kas Keluar -->
    <div class="modal fade" id="editKasKeluarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kas Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id-keluar">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="Tanggal_Input" id="edit-tanggal-keluar" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Event</label>
                            <input type="text" name="Event_WLE" id="edit-event-keluar" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="Keterangan" id="edit-keterangan-keluar" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="Nama_Akun" id="edit-kategori-keluar" class="form-select" required>
                                <option value="Rental">Rental</option>
                                <option value="Bensin">Bensin</option>
                                <option value="Peralatan">Peralatan</option>
                                <option value="Konsumsi">Konsumsi</option>
                                <option value="Modal">Modal</option>
                                <option value="Dll">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal</label>
                            <input type="number" name="Nominal" id="edit-nominal-keluar" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_pengeluaran" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Edit Booking -->
    <div class="modal fade" id="editBookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id-booking">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="Tanggal" id="edit-tanggal-booking" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Event</label>
                            <input type="text" name="Event" id="edit-event-booking" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Paket</label>
                            <select name="Paket" id="edit-paket-booking" class="form-select" required>
                                <option value="Silver">Silver Package</option>
                                <option value="Gold">Gold Package</option>
                                <option value="Platinum">Platinum Package</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_booking" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/umd/simple-datatables.min.js"></script>
    
    <script>
        // Initialize DataTables
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('kasmasukTable')) {
                new simpleDatatables.DataTable("#kasmasukTable", {
                    searchable: true,
                    sortable: true,
                    perPage: 10
                });
            }
            
            if (document.getElementById('kaskeluarTable')) {
                new simpleDatatables.DataTable("#kaskeluarTable", {
                    searchable: true,
                    sortable: true,
                    perPage: 10
                });
            }
            
            if (document.getElementById('bookingTable')) {
                new simpleDatatables.DataTable("#bookingTable", {
                    searchable: true,
                    sortable: true,
                    perPage: 10
                });
            }
            
            if (document.getElementById('laporanTable')) {
                new simpleDatatables.DataTable("#laporanTable", {
                    searchable: true,
                    sortable: true,
                    perPage: 15
                });
            }
        });

        // Edit Functions
        function editKasMasuk(id, tanggal, event, keterangan, nominal) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-tanggal').value = tanggal;
            document.getElementById('edit-event').value = event;
            document.getElementById('edit-keterangan').value = keterangan;
            document.getElementById('edit-nominal').value = nominal;
            
            const modal = new bootstrap.Modal(document.getElementById('editKasMasukModal'));
            modal.show();
        }

        function editKasKeluar(id, tanggal, event, keterangan, kategori, nominal) {
            document.getElementById('edit-id-keluar').value = id;
            document.getElementById('edit-tanggal-keluar').value = tanggal;
            document.getElementById('edit-event-keluar').value = event;
            document.getElementById('edit-keterangan-keluar').value = keterangan;
            document.getElementById('edit-kategori-keluar').value = kategori;
            document.getElementById('edit-nominal-keluar').value = nominal;
            
            const modal = new bootstrap.Modal(document.getElementById('editKasKeluarModal'));
            modal.show();
        }

        function editBooking(id, tanggal, event, paket) {
            document.getElementById('edit-id-booking').value = id;
            document.getElementById('edit-tanggal-booking').value = tanggal;
            document.getElementById('edit-event-booking').value = event;
            document.getElementById('edit-paket-booking').value = paket;
            
            const modal = new bootstrap.Modal(document.getElementById('editBookingModal'));
            modal.show();
        }

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('show')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }

        // Add mobile menu button if needed
        if (window.innerWidth <= 768) {
            const navbar = document.querySelector('.main-content');
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'btn btn-primary d-md-none position-fixed';
            toggleBtn.style.cssText = 'top: 20px; left: 20px; z-index: 1001;';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.onclick = toggleSidebar;
            navbar.prepend(toggleBtn);
        }
    </script>
</body>
</html>