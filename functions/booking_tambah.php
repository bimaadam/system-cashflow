<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['Tanggal'] ?? '';
    $event = $_POST['Event'] ?? '';
    $paket = $_POST['Paket'] ?? '';
    $payment_status = $_POST['payment_status'] ?? 'belum_bayar';
    $total_tagihan = isset($_POST['total_tagihan']) ? (int)$_POST['total_tagihan'] : 0;
    $uang_muka = isset($_POST['uang_muka']) ? (int)$_POST['uang_muka'] : 0;

    if (!empty($tanggal) && !empty($event) && !empty($paket)) {
        $tanggal = mysqli_real_escape_string($conn, $tanggal);
        $event = mysqli_real_escape_string($conn, $event);
        $paket = mysqli_real_escape_string($conn, $paket);
        $payment_status = mysqli_real_escape_string($conn, $payment_status);
        $total_tagihan = (int)$total_tagihan;
        $uang_muka = (int)$uang_muka;

        // Pastikan kolom payment_status ada, jika belum ada maka tambahkan
        $colCheck = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jadwal_booking' AND COLUMN_NAME = 'payment_status'");
        if ($colCheck && mysqli_num_rows($colCheck) === 0) {
            mysqli_query($conn, "ALTER TABLE jadwal_booking ADD COLUMN payment_status ENUM('belum_bayar','dibayar_sebagian','sudah_bayar') DEFAULT 'belum_bayar'");
        }

        // Pastikan kolom total_tagihan ada
        $colCheckTagihan = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jadwal_booking' AND COLUMN_NAME = 'total_tagihan'");
        if ($colCheckTagihan && mysqli_num_rows($colCheckTagihan) === 0) {
            mysqli_query($conn, "ALTER TABLE jadwal_booking ADD COLUMN total_tagihan BIGINT DEFAULT 0");
        }

        // Insert into jadwal_booking (dengan payment_status bila kolom tersedia)
        $hasPaymentCol = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jadwal_booking' AND COLUMN_NAME = 'payment_status'");
        $hasPaymentCol = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jadwal_booking' AND COLUMN_NAME = 'payment_status'");
        $hasTagihanCol = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jadwal_booking' AND COLUMN_NAME = 'total_tagihan'");

        $withPay = ($hasPaymentCol && mysqli_num_rows($hasPaymentCol) > 0);
        $withTagihan = ($hasTagihanCol && mysqli_num_rows($hasTagihanCol) > 0);

        if ($withPay && $withTagihan) {
            $query_booking = mysqli_query($conn, "INSERT INTO jadwal_booking (Tanggal, Event, Paket, payment_status, total_tagihan) VALUES ('$tanggal', '$event', '$paket', '$payment_status', $total_tagihan)");
        } elseif ($withPay) {
            $query_booking = mysqli_query($conn, "INSERT INTO jadwal_booking (Tanggal, Event, Paket, payment_status) VALUES ('$tanggal', '$event', '$paket', '$payment_status')");
        } elseif ($withTagihan) {
            $query_booking = mysqli_query($conn, "INSERT INTO jadwal_booking (Tanggal, Event, Paket, total_tagihan) VALUES ('$tanggal', '$event', '$paket', $total_tagihan)");
        } else {
            $query_booking = mysqli_query($conn, "INSERT INTO jadwal_booking (Tanggal, Event, Paket) VALUES ('$tanggal', '$event', '$paket')");
        }

        if ($query_booking) {
            // Jika ada uang muka, catat ke penerimaan_kas dan tautkan ke booking_id
            if ($uang_muka > 0) {
                $bookingId = mysqli_insert_id($conn);
                // pastikan kolom booking_id ada di penerimaan_kas
                $colCheckKas = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penerimaan_kas' AND COLUMN_NAME = 'booking_id'");
                if ($colCheckKas && mysqli_num_rows($colCheckKas) === 0) {
                    mysqli_query($conn, "ALTER TABLE penerimaan_kas ADD COLUMN booking_id BIGINT NULL AFTER Event_WLE");
                }
                $ketRaw = 'DP Booking - ' . $event . (!empty($paket) ? (' (' . $paket . ')') : '');
                $ket = mysqli_real_escape_string($conn, $ketRaw);
                mysqli_query($conn, "INSERT INTO penerimaan_kas (Tanggal_Input, Event_WLE, booking_id, Keterangan, Nominal) VALUES ('$tanggal', '$event', $bookingId, '$ket', $uang_muka)");
            }
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
