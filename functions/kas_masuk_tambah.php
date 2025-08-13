<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

// Only handle POST from forms
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['Tanggal_Input'] ?? '';
    $event = $_POST['Event_WLE'] ?? '';
    $keterangan = $_POST['Keterangan'] ?? '';
    $nominal = $_POST['Nominal'] ?? '';

    if (!empty($tanggal) && !empty($event) && !empty($keterangan) && is_numeric($nominal) && (int)$nominal > 0) {
        $tanggal = mysqli_real_escape_string($conn, $tanggal);
        $event = mysqli_real_escape_string($conn, $event);
        $keterangan = mysqli_real_escape_string($conn, $keterangan);
        $nominal = (int)$nominal;

        $query = mysqli_query($conn, "INSERT INTO penerimaan_kas (Tanggal_Input, Event_WLE, Keterangan, Nominal) VALUES ('$tanggal', '$event', '$keterangan', $nominal)");
        if ($query) {
            $redirect = $_POST['redirect'] ?? '';
            if (!empty($redirect)) {
                if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
                    $redirect = '../' . ltrim($redirect, '/');
                }
                header("Location: $redirect");
            } else {
                header("Location: ../dashboard.php?tab=kas_masuk&success=1");
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
        header("Location: ../dashboard.php?tab=kas_masuk&error=1");
    }
    exit;
}

header('Location: ../dashboard.php?tab=kas_masuk');
exit;
?>