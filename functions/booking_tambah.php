<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['Tanggal'] ?? '';
    $event = $_POST['Event'] ?? '';
    $paket = $_POST['Paket'] ?? '';

    // Hardcoded package prices
    $paket_prices = [
        'Silver' => 1000000,
        'Gold' => 2000000,
        'Diamond' => 3000000,
        'Platinum' => 4000000
    ];

    if (!empty($tanggal) && !empty($event) && !empty($paket) && isset($paket_prices[$paket])) {
        $tanggal = mysqli_real_escape_string($conn, $tanggal);
        $event = mysqli_real_escape_string($conn, $event);
        $paket = mysqli_real_escape_string($conn, $paket);
        $harga = $paket_prices[$paket];

        // Insert into jadwal_booking
        $query_booking = mysqli_query($conn, "INSERT INTO jadwal_booking (Tanggal, Event, Paket) VALUES ('$tanggal', '$event', '$paket')");

        if ($query_booking) {
            // Insert into penerimaan_kas
            $keterangan = "Booking - $event ($paket)";
            $query_kas_masuk = mysqli_query($conn, "INSERT INTO penerimaan_kas (Tanggal_Input, Event_WLE, Keterangan, Nominal) VALUES ('$tanggal', '$event', '$keterangan', '$harga')");

            if ($query_kas_masuk) {
                $redirect = $_POST['redirect'] ?? '';
                if (!empty($redirect)) {
                    if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
                        $redirect = '../' . ltrim($redirect, '/');
                    }
                    header("Location: $redirect&status=success");
                } else {
                    header("Location: ../dashboard.php?tab=booking&status=success");
                }
                exit;
            }
        }
    }

    $redirect = $_POST['redirect'] ?? '';
    if (!empty($redirect)) {
        if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
            $redirect = '../' . ltrim($redirect, '/');
        }
        header("Location: $redirect");
    } else {
        header("Location: ../dashboard.php?tab=booking&status=error");
    }
    exit;
}

header('Location: ../dashboard.php?tab=booking&status=error');
exit;
