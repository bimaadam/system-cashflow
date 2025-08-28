<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

// Simple backfill to standardize penerimaan_kas.Keterangan based on booking payments order.
// Rules:
// - For each booking (by booking_id; fallback by Event match when booking_id is NULL), order payments by date then id.
// - First payment -> "DP Booking - {Event} (Paket)".
// - Middle payments -> "Pembayaran Booking - {Event} (Paket)".
// - A payment that makes cumulative >= total_tagihan -> label as "Pelunasan Booking - {Event} (Paket)" (overrides others for that row).
// - Only touch rows linked to a booking or uniquely matched by Event.
// - Idempotent: will re-write to same strings.

$dry = isset($_GET['dry']) ? (int)$_GET['dry'] : 0; // dry-run prints summary
$redirect = $_GET['redirect'] ?? 'dashboard.php?tab=kas_masuk';

// Load all bookings
$bookings = [];
$resB = mysqli_query($conn, "SELECT id, Event, Paket, COALESCE(total_tagihan,0) AS total_tagihan FROM jadwal_booking");
while ($resB && ($b = mysqli_fetch_assoc($resB))) {
    $bid = (int)$b['id'];
    $bookings[$bid] = $b;
}

// Helper to format label
function fmt_label($type, $event, $paket) {
    $suffix = $paket ? (' (' . $paket . ')') : '';
    if ($type === 'dp') return 'DP Booking - ' . $event . $suffix;
    if ($type === 'pelunasan') return 'Pelunasan Booking - ' . $event . $suffix;
    return 'Pembayaran Booking - ' . $event . $suffix;
}

$totalUpdated = 0; $checked = 0; $skipped = 0;

// Strategy 1: Process by booking_id links first
foreach ($bookings as $bid => $b) {
    $event = $b['Event'];
    $paket = $b['Paket'];
    $tagihan = (int)$b['total_tagihan'];
    // Get payments for this booking (by booking_id or legacy by exact event name when booking_id is NULL)
    $sql = "SELECT id, Tanggal_Input, Keterangan, Nominal, booking_id, Event_WLE FROM penerimaan_kas WHERE (booking_id = $bid) OR (booking_id IS NULL AND Event_WLE = '" . mysqli_real_escape_string($conn, $event) . "') ORDER BY Tanggal_Input ASC, id ASC";
    $resP = mysqli_query($conn, $sql);
    if (!$resP) continue;
    $running = 0; $idx = 0;
    while ($row = mysqli_fetch_assoc($resP)) {
        $checked++;
        $idx++;
        $before = $running;
        $running += (int)$row['Nominal'];

        $type = 'pembayaran';
        if ($idx === 1) $type = 'dp';
        if ($tagihan > 0 && $running >= $tagihan) $type = 'pelunasan';
        $newKet = fmt_label($type, $event, $paket);

        if ($row['Keterangan'] !== $newKet) {
            if ($dry) {
                echo "Will update id={$row['id']} | '{$row['Keterangan']}' -> '{$newKet}'<br>\n";
            } else {
                $safe = mysqli_real_escape_string($conn, $newKet);
                mysqli_query($conn, "UPDATE penerimaan_kas SET Keterangan='$safe' WHERE id=" . (int)$row['id']);
            }
            $totalUpdated++;
        }
    }
}

if ($dry) {
    echo "Checked: $checked, To Update: $totalUpdated, Skipped: $skipped";
    exit;
}

header('Location: ../' . $redirect . '&status=success');
exit;
