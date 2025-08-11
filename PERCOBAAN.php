<?php
require 'function.php';
require 'cek.php';

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
                header("Location: ".$_SERVER['PHP_SELF']."?tab=dashboard2");
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
            header("Location: ".$_SERVER['PHP_SELF']."?tab=dashboard3");
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
        header("Location: ".$_SERVER['PHP_SELF']."?tab=dashboard2");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
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
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=dashboard2");
        exit;
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($conn);
    }
}
// ======================
// PROSES HAPUS KAS KELUAR
// ======================
if (isset($_GET['hapus_pengeluaran'])) {
    $id_hapus = $_GET['hapus_pengeluaran'];
    $query_hapus = mysqli_query($conn, "DELETE FROM pengeluaran_kas WHERE id = '$id_hapus'");
    if ($query_hapus) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=dashboard3");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
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
    $id = $_POST['id'];

    $query_update = mysqli_query($conn, "UPDATE pengeluaran_kas 
        SET Tanggal_Input='$Tanggal_Input', Event_WLE='$Event_WLE', 
            Keterangan='$Keterangan', Nama_Akun='$Nama_Akun', Nominal='$Nominal'
        WHERE id='$id'");
    
    if ($query_update) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=dashboard3");
        exit;
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($conn);
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
        header("Location: ".$_SERVER['PHP_SELF']."?tab=dashboard4");
        exit;
    } else {
        echo "Gagal menambah booking: " . mysqli_error($conn);
    }
}
// ======================
// PROSES HAPUS JADWAL BOOKING
// ======================
if (isset($_GET['hapus_booking'])) {
    $id_hapus = $_GET['hapus_booking'];
    $query_hapus = mysqli_query($conn, "DELETE FROM jadwal_booking WHERE id = '$id_hapus'");
    if ($query_hapus) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=dashboard4");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
}
// ======================
// PROSES EDIT KAS KELUAR
// ======================
if (isset($_POST['update_booking'])) {
    $id = $_POST['id'];
    $Tanggal = $_POST['Tanggal'];
    $Event = $_POST['Event'];
    $Paket = $POST_['Paket'];
    $query_update = mysqli_query($conn, "UPDATE jadwal_booking
        SET Tanggal='$Tanggal', Event='$Event', 
            Paket='$Paket' WHERE id='$id'");
    
    if ($query_update) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=dashboard4");
        exit;
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <style>
.hologram-text {
  font-weight: 900;
  font-size: 1.8rem;
  color: #383c3cff;
  text-align: center;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  text-shadow:
    0 0 5px #888888,
    0 0 10px #aaaaaa,
    0 0 20px #bbbbbb,
    0 0 30px #cccccc;
  animation: flicker 3s infinite alternate;
}

/* Animasi flicker tanpa blur */
@keyframes flicker {
  0%, 19%, 21%, 23%, 25%, 54%, 56%, 100% {
    opacity: 1;
  }
  20%, 22%, 24%, 55% {
    opacity: 0.5;
  }
}

.row {
    border: 3px solid #ccc;      /* warna abu muda */
    border-radius: 10px;         /* sudut membulat */
    padding: 4px;                 /* jarak antara gambar dan border */
    background-color: #fff;      /* latar belakang putih di dalam border */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2); /* efek bayangan */
}

</style>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard - Admin Dekorasi</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

<!-- Navbar -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="Index.php">Admin Dekorasi</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" data-bs-toggle="dropdown"><i class="fas fa-user fa-fw"></i></a>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="#">Settings</a>
                <a class="dropdown-item" href="#">Activity Log</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="Logout.php">Logout</a>
            </div>
        </li>
    </ul>
</nav>

<div id="layoutSidenav">
    <!-- Sidebar -->
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>

<a class="nav-link" href="#" onclick="showDashboard('dashboard1')">
    <div class="sb-nav-link-icon"><i class="fas fa-image"></i></div>
    Menu Utama
</a>

<a class="nav-link" href="#" onclick="showDashboard('dashboard2')">
    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
    Kas Masuk
</a>

<a class="nav-link" href="#" onclick="showDashboard('dashboard3')">
    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
    Kas Keluar
</a>

<a class="nav-link" href="#" onclick="showDashboard('dashboard4')">
    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
    Jadwal Booking
</a>
<a class="nav-link" href="#" onclick="showDashboard('dashboard5')">
    <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
    Laporan Keuangan
</a>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                Admin Keuangan
            </div>
        </nav>
    </div>

    <!-- Content -->
    <div id="layoutSidenav_content">
        <main class="container-fluid px-4 mt-4">
           

            <!-- Dashboard 1 -->
            <section id="dashboard1" class="mb-5" style="display: block;">
                 <h1 class="hologram-text">HII!! ADMIN</h1>
    <h3 class="hologram-text mb-4">SELAMAT DATANG DAN SELAMAT RECAP</h3>
                <h4>Galeri Dekorasi Graceful</h4>
                <div class="row">
                    <div class="col-md-2"><img src="Dekor1.jpg" class="img-fluid mb-2" alt="Dekor 1"></div>
                    <div class="col-md-2"><img src="Dekor4.jpg" class="img-fluid mb-2" alt="Dekor 4"></div>
                    <div class="col-md-2"><img src="Dekor5.jpg" class="img-fluid mb-2" alt="Dekor 5"></div>
                    <div class="col-md-2"><img src="Dekor6.jpg" class="img-fluid mb-2" alt="Dekor 6"></div>
                    <div class="col-md-2"><img src="Dekor2.jpg" class="img-fluid mb-2" alt="Dekor 2"></div>
                    <div class="col-md-2"><img src="Dekor3.jpg" class="img-fluid mb-2" alt="Dekor 3"></div>
                    <div class="col-md-2"><img src="Dekor7.jpg" class="img-fluid mb-2" alt="Dekor 7"></div>
                    <div class="col-md-2"><img src="Dekor8.jpg" class="img-fluid mb-2" alt="Dekor 8"></div>
                    <div class="col-md-2"><img src="Dekor10.jpg" class="img-fluid mb-2" alt="Dekor 10"></div>
                    <div class="col-md-2"><img src="Dekor9.jpg" class="img-fluid mb-2" alt="Dekor 9"></div>
                    <div class="col-md-2"><img src="Dekor11.jpg" class="img-fluid mb-2" alt="Dekor 11"></div>
                    <div class="col-md-2"><img src="Dekor12.jpg" class="img-fluid mb-2" alt="Dekor 12"></div>
                </div>
                <!-- Teks Alamat -->
<p style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem; font-style: italic; text-align: center; margin-top: 10px;">
    Alamat : Kp.Tanjung Jaya, Desa Dawagung, Kec. Rajapolah, Kab. Tasikmalaya Jawa Barat
</p>
            </section>

           <!-- Dashboard 2 -->
    <section id="dashboard2" class="mb-5"style="display: none;">
    <div class="d-flex justify-content-between align-items-center mb-2">
    <h4 class="mb-0">Kas Masuk</h4>
</div>

    <!-- FORM INPUT KAS MASUK -->
    <form method="POST" action="">
    <input type="hidden" name="id" value="<?= isset($data_edit) ? $data_edit['id'] : '' ?>">
    <div class="row g-2 mb-3">
        <div class="col-md-2">
            <input type="date" name="Tanggal_Input" class="form-control" value="<?= isset($data_edit) ? $data_edit['Tanggal_Input'] : '' ?>" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="Event_WLE" class="form-control" placeholder="Nama Event" value="<?= isset($data_edit) ? $data_edit['Event_WLE'] : '' ?>" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="Keterangan" class="form-control" placeholder="Keterangan" value="<?= isset($data_edit) ? $data_edit['Keterangan'] : '' ?>" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="Nominal" class="form-control" placeholder="Nominal (Rp)" value="<?= isset($data_edit) ? $data_edit['Nominal'] : '' ?>" required>
        </div>
        <div class="col-md-1">
            <?php if (isset($data_edit)) { ?>
                <button type="submit" name="update_event" class="btn btn-success w-100">✓</button>
            <?php } else { ?>
                <button type="submit" name="tambah_event" class="btn btn-primary w-100">+</button>
            <?php } ?>
        </div>
    </div>
</form>
    <!-- TABEL KAS MASUK -->
   <!-- TABEL KAS MASUK -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-table me-1"></i> Data Penerimaan (Kas Masuk)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Event</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data_event = mysqli_query($conn, "SELECT * FROM penerimaan_kas ORDER BY Tanggal_Input DESC");
                    while ($row = mysqli_fetch_assoc($data_event)) {
                        echo "<tr>
                            <td>{$row['Tanggal_Input']}</td>
                            <td>{$row['Event_WLE']}</td>
                            <td>{$row['Keterangan']}</td>
                            <td>Rp " . number_format($row['Nominal'], 0, ',', '.') . "</td>
                            <td>
                            <a href='?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
                                <button class='btn btn-sm btn-warning btn-edit' 
                                    data-id='{$row['id']}' 
                                    data-tanggal='{$row['Tanggal_Input']}'
                                    data-event='{$row['Event_WLE']}'
                                    data-keterangan='{$row['Keterangan']}'
                                    data-nominal='{$row['Nominal']}'>
                                    <i class='fas fa-edit'></i> Edit
                                </button>
                            </td>
                        </tr>";
                    }
                    ?>
                    <div class="modal fade" id="editKasMasukModal" tabindex="-1" aria-labelledby="editKasMasukModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKasMasukModalLabel">Edit Data Kas Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="Tanggal_Input" id="edit-tanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-event" class="form-label">Nama Event</label>
                        <input type="text" name="Event_WLE" id="edit-event" class="form-control" placeholder="Nama Event" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-keterangan" class="form-label">Keterangan</label>
                        <input type="text" name="Keterangan" id="edit-keterangan" class="form-control" placeholder="Keterangan" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-nominal" class="form-label">Nominal (Rp)</label>
                        <input type="number" name="Nominal" id="edit-nominal" class="form-control" placeholder="Nominal" required>
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
                </tbody>
            </table>
        </div>
    </div>
</div>
<form action="cetak_kasmasuk.php" method="get" target="_blank" style="display: flex; gap: 10px; align-items: center;">
    <label for="bulan">Bulan:</label>
    <select name="bulan" id="bulan" required>
        <option value="01">Januari</option>
        <option value="02">Februari</option>
        <option value="03">Maret</option>
        <option value="04">April</option>
        <option value="05">Mei</option>
        <option value="06">Juni</option>
        <option value="07">Juli</option>
        <option value="08">Agustus</option>
        <option value="09">September</option>
        <option value="10">Oktober</option>
        <option value="11">November</option>
        <option value="12">Desember</option>
    </select>
    <label for="tahun">Tahun:</label>
    <input type="number" name="tahun" id="tahun" min="2020" max="2100" value="<?= date('Y') ?>" required>

    <button type="submit" class="btn btn-primary">Cetak Laporan Kas Masuk</button>
</form>

</section>
           <!-- Dashboard 3 -->
<section id="dashboard3" style="display: none;">
    <h4 class="mb-3">Kas Keluar</h4>

    <!-- Form Input -->
    <form method="POST">
        <div class="row g-2 mb-3">
            <div class="col-md-2">
                <input type="date" name="Tanggal_Input" class="form-control" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="Event_WLE" class="form-control" placeholder="Event" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="Keterangan" class="form-control" placeholder="Keterangan" required>
            </div>
            <div class="col-md-2">
                <select name="Nama_Akun" class="form-select" required>
                    <option value="">Pilih Akun</option>
                    <option>Rental</option>
                    <option>Bensin</option>
                    <option>Peralatan</option>
                    <option>Konsumsi</option>
                    <option>Modal</option>
                    <option>Dll</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="Nominal" class="form-control" placeholder="Nominal (Rp)" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="tambah_pengeluaran" class="btn btn-primary w-100">+</button>
            </div>
        </div>

    </form>

    <div class="card">
    <div class="card-header">
        <i class="fas fa-table me-1"></i> Data Pengeluaran (Kas Keluar)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Event</th>
                        <th>Keterangan</th>
                        <th>Nama Akun</th>
                        <th>Nominal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data_pengeluaran = mysqli_query($conn, "SELECT * FROM pengeluaran_kas ORDER BY Tanggal_Input DESC");
                    while ($row = mysqli_fetch_assoc($data_pengeluaran)) {
                        echo "<tr>
                                <td>{$row['Tanggal_Input']}</td>
                                <td>{$row['Event_WLE']}</td>
                                <td>{$row['Keterangan']}</td>
                                <td>{$row['Nama_Akun']}</td>
                                <td>Rp " . number_format($row['Nominal'], 0, ',', '.') . "</td>
                                <td>
                                    <a href='?hapus_pengeluaran={$row['id']}&tab=dashboard3' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
                                    <button class='btn btn-sm btn-warning btn-edit-pengeluaran' 
                                        data-id='{$row['id']}' 
                                        data-tanggal='{$row['Tanggal_Input']}'
                                        data-event='{$row['Event_WLE']}'
                                        data-keterangan='{$row['Keterangan']}'
                                        data-nama_akun='{$row['Nama_Akun']}'
                                        data-nominal='{$row['Nominal']}'>
                                        <i class='fas fa-edit'></i> Edit
                                    </button>
                                </td>
                            </tr>";
                    }
                    ?>
                    <div class="modal fade" id="editKasKeluarModal" tabindex="-1" aria-labelledby="editKasKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKasKeluarModalLabel">Edit Data Kas Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id-pengeluaran">
                    <div class="mb-3">
                        <label for="edit-tanggal-pengeluaran" class="form-label">Tanggal</label>
                        <input type="date" name="Tanggal_Input" id="edit-tanggal-pengeluaran" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-event-pengeluaran" class="form-label">Nama Event</label>
                        <input type="text" name="Event_WLE" id="edit-event-pengeluaran" class="form-control" placeholder="Nama Event" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-keterangan-pengeluaran" class="form-label">Keterangan</label>
                        <input type="text" name="Keterangan" id="edit-keterangan-pengeluaran" class="form-control" placeholder="Keterangan" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-nama-akun-pengeluaran" class="form-label">Nama Akun</label>
                        <select name="Nama_Akun" id="edit-nama-akun-pengeluaran" class="form-select" required>
                            <option value="Rental">Rental</option>
                            <option value="Bensin">Bensin</option>
                            <option value="Peralatan">Peralatan</option>
                            <option value="Konsumsi">Konsumsi</option>
                            <option value="Modal">Modal</option>
                            <option value="Dll">Dll</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-nominal-pengeluaran" class="form-label">Nominal (Rp)</label>
                        <input type="number" name="Nominal" id="edit-nominal-pengeluaran" class="form-control" placeholder="Nominal" required>
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
                </tbody>
            </table>
        </div>
    </div>
</div>
    <form action="cetak_kaskeluar.php" method="get" target="_blank" style="display: flex; gap: 10px; align-items: center;">
    <label for="bulan">Bulan:</label>
    <select name="bulan" id="bulan" required>
        <option value="01">Januari</option>
        <option value="02">Februari</option>
        <option value="03">Maret</option>
        <option value="04">April</option>
        <option value="05">Mei</option>
        <option value="06">Juni</option>
        <option value="07">Juli</option>
        <option value="08">Agustus</option>
        <option value="09">September</option>
        <option value="10">Oktober</option>
        <option value="11">November</option>
        <option value="12">Desember</option>
    </select>
    <label for="tahun">Tahun:</label>
    <input type="number" name="tahun" id="tahun" min="2020" max="2100" value="<?= date('Y') ?>" required>

    <button type="submit" class="btn btn-primary">Cetak Laporan Kas Keluar</button>
</form>
</section>

<!-- Dashboard 4 Jadwal Booking -->
 <section id="dashboard4" class="mb-5" style="display: none;">
     <h4>Jadwal Booking</h4>
  <div class="card shadow mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Input Booking Event</h5>
  </div>
  <div class="card-body">
    <form method="POST" action="">
      <div class="row g-3 align-items-center">
        
        <!-- Input Tanggal -->
        <div class="col-md-3">
          <label for="tanggal" class="form-label"><i class="fas fa-calendar-day me-1"></i>Tanggal Booking</label>
          <input type="date" name="Tanggal" class="form-control border-primary shadow-sm" required>
        </div>

        <!-- Input Nama Event -->
        <div class="col-md-4">
          <label for="Event" class="form-label"><i class="fas fa-star me-1"></i>Nama Event</label>
          <input type="text" name="Event" class="form-control border-primary shadow-sm" placeholder="Contoh: Wedding Andi & Rina" required>
        </div>

        <!-- Dropdown Paket -->
        <div class="col-md-3">
          <label for="Paket" class="form-label"><i class="fas fa-box-open me-1"></i>Pilih Paket</label>
          <select name="Paket" class="form-select border-primary shadow-sm" required>
            <option value="">-- Pilih Paket --</option>
            <option value="Silver">Silver </option>
            <option value="Gold">Gold</option>
            <option value="Platinum">Platinum</option>
          </select>
        </div>

        <!-- Tombol -->
        <div class="col-md-2 d-grid">
          <label class="form-label invisible">.</label>
          <button type="submit" name="jadwal_booking" class="btn btn-success shadow">
            <i class="fas fa-plus-circle me-1"></i> Booking
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="card">
    <div class="card-header">
        <i class="fas fa-table me-1"></i> Data Booking Graceful Dekorasi
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal Booking</th>
                        <th>Nama Event</th>
                        <th>Paket</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data_booking = mysqli_query($conn, "SELECT * FROM jadwal_booking ORDER BY Tanggal DESC");
                    while ($row = mysqli_fetch_assoc($data_booking)) {
                        echo "<tr>
                                <td>{$row['Tanggal']}</td>
                                <td>{$row['Event']}</td>
                                <td>{$row['Paket']}</td>
                                <td>
                                    <a href='?hapus_booking={$row['id']}&tab=dashboard4' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
                                    <button class='btn btn-sm btn-warning btn-edit-booking' 
                                        data-id='{$row['id']}' 
                                        data-tanggal='{$row['Tanggal']}'
                                        data-event='{$row['Event']}'
                                        data-paket='{$row['Paket']}'>
                                        <i class='fas fa-edit'></i> Edit
                                    </button>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>

<!-- Dashboard 5 Laporan Keuangan -->
<section id="dashboard5" class="mb-5" style="display: none;">
    <h4 class="mb-3">Laporan Keuangan Bulanan</h4>

    <!-- TABEL GABUNGAN -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-chart-line me-1"></i> Laporan Kas Masuk & Keluar
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Event</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_union = "
                            SELECT Tanggal_Input, 'Masuk' AS Jenis, Event_WLE, Keterangan, Nominal
                            FROM penerimaan_kas
                            UNION ALL
                            SELECT Tanggal_Input, 'Keluar' AS Jenis, Event_WLE, Keterangan, Nominal
                            FROM pengeluaran_kas
                            ORDER BY Tanggal_Input DESC
                        ";
                        $result_union = mysqli_query($conn, $query_union);

                        while ($row = mysqli_fetch_assoc($result_union)) {
                            echo "<tr>
                                <td>{$row['Tanggal_Input']}</td>
                                <td>{$row['Jenis']}</td>
                                <td>{$row['Event_WLE']}</td>
                                <td>{$row['Keterangan']}</td>
                                <td>Rp " . number_format($row['Nominal'], 0, ',', '.') . "</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TOTAL BULANAN -->
    <div class="row g-3">
        <?php
        $bulan_ini = date('Y-m');

        // Total kas masuk
        $masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(Nominal) AS total FROM penerimaan_kas WHERE DATE_FORMAT(Tanggal_Input, '%Y-%m') = '$bulan_ini'"));

        // Total kas keluar
        $keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(Nominal) AS total FROM pengeluaran_kas WHERE DATE_FORMAT(Tanggal_Input, '%Y-%m') = '$bulan_ini'"));
        ?>
        <div class="col-md-6">
            <div class="alert alert-success shadow">
                <strong>Total Kas Masuk Bulan Ini:</strong> Rp <?= number_format($masuk['total'], 0, ',', '.') ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-danger shadow">
                <strong>Total Kas Keluar Bulan Ini:</strong> Rp <?= number_format($keluar['total'], 0, ',', '.') ?>
            </div>
        </div>
    </div>

    <!-- DOWNLOAD LAPORAN -->
    <div class="text-end mt-3">
        <a href="cetaklaporan.php" class="btn btn-outline-primary">
            <i class="fas fa-download me-1"></i> Unduh Laporan PDF
        </a>
    </div>
</section>

        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid text-center">
                <small>&copy; 2025 Admin Dekorasi</small>
            </div>
        </footer>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script>
    // Aktifkan datatable
    document.querySelectorAll('.datatable').forEach(table => {
        new simpleDatatables.DataTable(table);
    });
</script>
<script>
function showDashboard(id) {
    const sections = ['dashboard1', 'dashboard2', 'dashboard3','dashboard4','dashboard5'];
    sections.forEach(section => {
        const el = document.getElementById(section);
        if (el) el.style.display = (section === id) ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const activeTab = params.get('tab') || 'dashboard1';
    showDashboard(activeTab);
});
</script>
<script>
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const tanggal = button.dataset.tanggal;
            const event = button.dataset.event;
            const keterangan = button.dataset.keterangan;
            const nominal = button.dataset.nominal;

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-tanggal').value = tanggal;
            document.getElementById('edit-event').value = event;
            document.getElementById('edit-keterangan').value = keterangan;
            document.getElementById('edit-nominal').value = nominal;

            const editModal = new bootstrap.Modal(document.getElementById('editKasMasukModal'));
            editModal.show();
        });
    });
</script>
<script>
  document.querySelector("form").addEventListener("submit", function () {
    const btn = this.querySelector("button[type='submit']");
    btn.disabled = true;
    btn.innerText = "";
  });
</script>
<script>
// Skrip untuk tombol edit kas keluar
document.querySelectorAll('.btn-edit-pengeluaran').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.dataset.id;
        const tanggal = button.dataset.tanggal;
        const event = button.dataset.event;
        const keterangan = button.dataset.keterangan;
        const nama_akun = button.dataset.nama_akun;
        const nominal = button.dataset.nominal;

        document.getElementById('edit-id-pengeluaran').value = id;
        document.getElementById('edit-tanggal-pengeluaran').value = tanggal;
        document.getElementById('edit-event-pengeluaran').value = event;
        document.getElementById('edit-keterangan-pengeluaran').value = keterangan;
        document.getElementById('edit-nama-akun-pengeluaran').value = nama_akun;
        document.getElementById('edit-nominal-pengeluaran').value = nominal;

        const editModal = new bootstrap.Modal(document.getElementById('editKasKeluarModal'));
        editModal.show();
    });
});
</script>

</body>
</html>
