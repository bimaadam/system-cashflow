<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $tanggal = $_POST['Tanggal_Input'] ?? '';
    $event = $_POST['Event_WLE'] ?? '';
    $keterangan = $_POST['Keterangan'] ?? '';
    $nama_akun = $_POST['Nama_Akun'] ?? '';
    $nominal = $_POST['Nominal'] ?? '';

    if ($id > 0 && !empty($tanggal) && !empty($keterangan) && !empty($nama_akun) && is_numeric($nominal) && (int)$nominal > 0) {
        $tanggal = mysqli_real_escape_string($conn, $tanggal);
        $event = mysqli_real_escape_string($conn, $event);
        $keterangan = mysqli_real_escape_string($conn, $keterangan);
        $nama_akun = mysqli_real_escape_string($conn, $nama_akun);
        $nominal = (int)$nominal;

        $query = mysqli_query($conn, "UPDATE pengeluaran_kas SET Tanggal_Input='$tanggal', Event_WLE='$event', Keterangan='$keterangan', Nama_Akun='$nama_akun', Nominal=$nominal WHERE id=$id");
        if ($query) {
            $redirect = $_POST['redirect'] ?? '';
            if (!empty($redirect)) {
                if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
                    $redirect = '../' . ltrim($redirect, '/');
                }
                header("Location: $redirect");
            } else {
                header("Location: ../dashboard.php?tab=kas_keluar&updated=1");
            }
            exit;
        }
    }

    $redirect = $_POST['redirect'] ?? '';
    if (!empty($redirect)) {
        if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
            $redirect = '../' . ltrim($redirect, '/');
        }
        header("Location: $redirect");
    } else {
        header("Location: ../dashboard.php?tab=kas_keluar&error=1");
    }
    exit;
}

header('Location: ../dashboard.php?tab=kas_keluar');
exit;
?>
