<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $tanggal = $_POST['Tanggal'] ?? '';
    $event = $_POST['Event'] ?? '';
    $paket = $_POST['Paket'] ?? '';
    $payment_status = $_POST['payment_status'] ?? 'belum_bayar';
    $total_tagihan = isset($_POST['total_tagihan']) ? (int)$_POST['total_tagihan'] : 0;
    $uang_muka = isset($_POST['uang_muka']) ? (int)$_POST['uang_muka'] : 0;

    if ($id > 0 && !empty($tanggal) && !empty($event) && !empty($paket)) {
        $tanggal = mysqli_real_escape_string($conn, $tanggal);
        $event = mysqli_real_escape_string($conn, $event);
        $paket = mysqli_real_escape_string($conn, $paket);
        $payment_status = mysqli_real_escape_string($conn, $payment_status);
        $total_tagihan = (int)$total_tagihan;
        $uang_muka = (int)$uang_muka;
        // Pastikan kolom payment_status ada
        $colCheck = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jadwal_booking' AND COLUMN_NAME = 'payment_status'");
        if ($colCheck && mysqli_num_rows($colCheck) === 0) {
            mysqli_query($conn, "ALTER TABLE jadwal_booking ADD COLUMN payment_status ENUM('belum_bayar','dibayar_sebagian','sudah_bayar') DEFAULT 'belum_bayar'");
        }
        // Pastikan kolom total_tagihan ada
        $colCheckTagihan = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jadwal_booking' AND COLUMN_NAME = 'total_tagihan'");
        if ($colCheckTagihan && mysqli_num_rows($colCheckTagihan) === 0) {
            mysqli_query($conn, "ALTER TABLE jadwal_booking ADD COLUMN total_tagihan BIGINT DEFAULT 0");
        }

        // Ambil status lama untuk deteksi transisi ke sudah_bayar
        $oldStatus = 'belum_bayar';
        $oldRes = mysqli_query($conn, "SELECT payment_status FROM jadwal_booking WHERE id=$id LIMIT 1");
        if ($oldRes && mysqli_num_rows($oldRes) > 0) {
            $oldRow = mysqli_fetch_assoc($oldRes);
            if ($oldRow && isset($oldRow['payment_status'])) {
                $oldStatus = $oldRow['payment_status'];
            }
        }

        $query = mysqli_query($conn, "UPDATE jadwal_booking SET Tanggal='$tanggal', Event='$event', Paket='$paket', payment_status='$payment_status', total_tagihan=$total_tagihan WHERE id=$id");
        if ($query) {
            // Jika ada uang muka pada edit, tambahkan penerimaan_kas hanya jika BELUM ADA pembayaran sebelumnya
            if ($uang_muka > 0) {
                // pastikan kolom booking_id ada
                $colCheckKas = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penerimaan_kas' AND COLUMN_NAME = 'booking_id'");
                if ($colCheckKas && mysqli_num_rows($colCheckKas) === 0) {
                    mysqli_query($conn, "ALTER TABLE penerimaan_kas ADD COLUMN booking_id BIGINT NULL AFTER Event_WLE");
                }
                // cek total bayar existing
                $sumSql0 = "SELECT COALESCE(SUM(Nominal),0) AS total_bayar FROM penerimaan_kas WHERE (booking_id = $id) OR (booking_id IS NULL AND Event_WLE = '" . mysqli_real_escape_string($conn, $event) . "')";
                $sumRes0 = mysqli_query($conn, $sumSql0);
                $rowSum0 = $sumRes0 ? mysqli_fetch_assoc($sumRes0) : ['total_bayar' => 0];
                $totalBayar0 = (int)($rowSum0['total_bayar'] ?? 0);
                if ($totalBayar0 === 0) {
                    $ketRaw = 'DP Booking - ' . $event . (!empty($paket) ? (' (' . $paket . ')') : '');
                    $ket = mysqli_real_escape_string($conn, $ketRaw);
                    mysqli_query($conn, "INSERT INTO penerimaan_kas (Tanggal_Input, Event_WLE, booking_id, Keterangan, Nominal) VALUES ('$tanggal', '$event', $id, '$ket', $uang_muka)");
                }
            }

            // Auto-pelunasan: hanya saat transisi ke sudah_bayar (dari status selain sudah_bayar)
            if ($payment_status === 'sudah_bayar' && $oldStatus !== 'sudah_bayar' && $total_tagihan > 0) {
                // pastikan kolom booking_id ada
                $colCheckKas = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penerimaan_kas' AND COLUMN_NAME = 'booking_id'");
                if ($colCheckKas && mysqli_num_rows($colCheckKas) === 0) {
                    mysqli_query($conn, "ALTER TABLE penerimaan_kas ADD COLUMN booking_id BIGINT NULL AFTER Event_WLE");
                }

                // hitung total bayar (prioritas by booking_id, fallback by Event_WLE untuk data lama)
                $sumSql = "SELECT COALESCE(SUM(Nominal),0) AS total_bayar FROM penerimaan_kas WHERE (booking_id = $id) OR (booking_id IS NULL AND Event_WLE = '" . mysqli_real_escape_string($conn, $event) . "')";
                $sumRes = mysqli_query($conn, $sumSql);
                $rowSum = $sumRes ? mysqli_fetch_assoc($sumRes) : ['total_bayar' => 0];
                $totalBayar = (int)($rowSum['total_bayar'] ?? 0);
                $sisa = max(0, $total_tagihan - $totalBayar);

                if ($sisa > 0) {
                    $today = date('Y-m-d');
                    $ketRaw = 'Pelunasan Booking - ' . $event . (!empty($paket) ? (' (' . $paket . ')') : '');
                    $ketPelunasan = mysqli_real_escape_string($conn, $ketRaw);
                    mysqli_query($conn, "INSERT INTO penerimaan_kas (Tanggal_Input, Event_WLE, booking_id, Keterangan, Nominal) VALUES ('$today', '$event', $id, '$ketPelunasan', $sisa)");
                }
            }
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


