<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

// Only handle POST from forms
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['Tanggal_Input'] ?? '';
    $event = $_POST['Event_WLE'] ?? '';
    $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
    $keterangan = $_POST['Keterangan'] ?? '';
    $nominal = $_POST['Nominal'] ?? '';

    if (!empty($tanggal) && !empty($event) && !empty($keterangan) && is_numeric($nominal) && (int)$nominal > 0) {
        $tanggal = mysqli_real_escape_string($conn, $tanggal);
        $event = mysqli_real_escape_string($conn, $event);
        $keterangan = mysqli_real_escape_string($conn, $keterangan);
        $nominal = (int)$nominal;

        // Pastikan kolom booking_id ada di penerimaan_kas
        $colCheck = mysqli_query($conn, "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penerimaan_kas' AND COLUMN_NAME = 'booking_id'");
        if ($colCheck && mysqli_num_rows($colCheck) === 0) {
            mysqli_query($conn, "ALTER TABLE penerimaan_kas ADD COLUMN booking_id BIGINT NULL AFTER Event_WLE");
        }

        // Pastikan index untuk performa: booking_id, Event_WLE, Tanggal_Input
        $idxCheck1 = mysqli_query($conn, "SELECT 1 FROM information_schema.statistics WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penerimaan_kas' AND INDEX_NAME = 'idx_booking_id' LIMIT 1");
        if ($idxCheck1 && mysqli_num_rows($idxCheck1) === 0) {
            mysqli_query($conn, "ALTER TABLE penerimaan_kas ADD INDEX idx_booking_id (booking_id)");
        }
        $idxCheck2 = mysqli_query($conn, "SELECT 1 FROM information_schema.statistics WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penerimaan_kas' AND INDEX_NAME = 'idx_event_wle' LIMIT 1");
        if ($idxCheck2 && mysqli_num_rows($idxCheck2) === 0) {
            mysqli_query($conn, "ALTER TABLE penerimaan_kas ADD INDEX idx_event_wle (Event_WLE)");
        }
        $idxCheck3 = mysqli_query($conn, "SELECT 1 FROM information_schema.statistics WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penerimaan_kas' AND INDEX_NAME = 'idx_tanggal_input' LIMIT 1");
        if ($idxCheck3 && mysqli_num_rows($idxCheck3) === 0) {
            mysqli_query($conn, "ALTER TABLE penerimaan_kas ADD INDEX idx_tanggal_input (Tanggal_Input)");
        }

        // Insert dengan booking_id jika tersedia (>0), jika tidak NULL
        $bookingIdValue = $booking_id > 0 ? $booking_id : 'NULL';
        $query = mysqli_query($conn, "INSERT INTO penerimaan_kas (Tanggal_Input, Event_WLE, booking_id, Keterangan, Nominal) VALUES ('$tanggal', '$event', $bookingIdValue, '$keterangan', $nominal)");
        if ($query) {
            $redirect = $_POST['redirect'] ?? '';
            if (!empty($redirect)) {
                if (strpos($redirect, 'http://') !== 0 && strpos($redirect, 'https://') !== 0 && strpos($redirect, '/') !== 0) {
                    $redirect = '../' . ltrim($redirect, '/');
                }
                header("Location: $redirect&status=success");
            } else {
                header("Location: ../dashboard.php?tab=kas_masuk&status=success");
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
        header("Location: ../dashboard.php?tab=kas_masuk&error");
    }
    exit;
}

header('Location: ../dashboard.php?tab=kas_masuk&status=error');
exit;
?>