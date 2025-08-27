<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['Tanggal'] ?? '';
    $event = $_POST['Event'] ?? '';
    $paket = $_POST['Paket'] ?? '';

    if (!empty($tanggal) && !empty($event) && !empty($paket)) {
        $tanggal = mysqli_real_escape_string($conn, $tanggal);
        $event = mysqli_real_escape_string($conn, $event);
        $paket = mysqli_real_escape_string($conn, $paket);

        // Insert into jadwal_booking
        $query_booking = mysqli_query($conn, "INSERT INTO jadwal_booking (Tanggal, Event, Paket) VALUES ('$tanggal', '$event', '$paket')");

        if ($query_booking) {
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
        } else {
            // If query_booking fails
            $redirect = $_POST['redirect'] ?? '';
            if (!empty($redirect)) {
                if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
                    $redirect = '../' . ltrim($redirect, '/');
                }
                header("Location: $redirect&status=error");
            } else {
                header("Location: ../dashboard.php?tab=booking&status=error");
            }
            exit;
        }
    } else {
        // If initial validation fails
        $redirect = $_POST['redirect'] ?? '';
        if (!empty($redirect)) {
            if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
                $redirect = '../' . ltrim($redirect, '/');
            }
            header("Location: $redirect&status=error");
        } else {
            header("Location: ../dashboard.php?tab=booking&status=error");
        }
        exit;
    }
}

header('Location: ../dashboard.php?tab=booking&status=error');
exit;
