<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $tanggal = $_POST['Tanggal'] ?? '';
    $event = $_POST['Event'] ?? '';
    $paket = $_POST['Paket'] ?? '';

    if ($id > 0 && !empty($tanggal) && !empty($event) && !empty($paket)) {
        $tanggal = mysqli_real_escape_string($conn, $tanggal);
        $event = mysqli_real_escape_string($conn, $event);
        $paket = mysqli_real_escape_string($conn, $paket);

        $query = mysqli_query($conn, "UPDATE jadwal_booking SET Tanggal='$tanggal', Event='$event', Paket='$paket' WHERE id=$id");
        if ($query) {
            $redirect = $_POST['redirect'] ?? '';
            if (!empty($redirect)) {
                if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
                    $redirect = '../' . ltrim($redirect, '/');
                }
                header("Location: $redirect");
            } else {
                header("Location: ../dashboard.php?tab=booking&updated=1");
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
        header("Location: ../dashboard.php?tab=booking&error=1");
    }
    exit;
}

header('Location: ../dashboard.php?tab=booking');
exit;


