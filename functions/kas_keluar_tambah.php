<?php
require_once __DIR__ . '/../function.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['Tanggal_Input'] ?? '';
    $event = $_POST['Event_WLE'] ?? '';
    $keterangan = $_POST['Keterangan'] ?? '';
    $nama_akun = $_POST['Nama_Akun'] ?? '';
    $nominal = $_POST['Nominal'] ?? '';

    if (!empty($tanggal) && !empty($keterangan) && !empty($nama_akun) && is_numeric($nominal) && (int)$nominal > 0) {
        $tanggal = mysqli_real_escape_string($conn, $tanggal);
        $event = mysqli_real_escape_string($conn, $event);
        $keterangan = mysqli_real_escape_string($conn, $keterangan);
        $nama_akun = mysqli_real_escape_string($conn, $nama_akun);
        $nominal = (int)$nominal;

        $query = mysqli_query($conn, "INSERT INTO pengeluaran_kas (Tanggal_Input, Event_WLE, Keterangan, Nama_Akun, Nominal) VALUES ('$tanggal', '$event', '$keterangan', '$nama_akun', $nominal)");
        if ($query) {
            $redirect = $_POST['redirect'] ?? '';
            if (!empty($redirect)) {
                if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
                    $redirect = '../' . ltrim($redirect, '/');
                }
                header("Location: $redirect&status=success");
            } else {
                header("Location: ../dashboard.php?tab=kas_keluar&status=success");
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
        header("Location: ../dashboard.php?tab=kas_keluar&status=error");
    }
    exit;
}

header('Location: ../dashboard.php?tab=kas_keluar&status=error');
    exit;
?>