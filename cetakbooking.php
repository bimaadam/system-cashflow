<?php
require 'function.php';

$bulan = (isset($_GET['bulan']) && preg_match('/^\d{2}$/', $_GET['bulan'])) 
    ? $_GET['bulan'] : date('m');
$tahun = (isset($_GET['tahun']) && preg_match('/^\d{4}$/', $_GET['tahun'])) 
    ? $_GET['tahun'] : date('Y');

$start_month = sprintf('%04d-%02d-01 00:00:00', (int)$tahun, (int)$bulan);
$next_month  = date('Y-m-01 00:00:00', strtotime("$start_month +1 month"));

$nama_bulan_map = [
    '01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April',
    '05' => 'Mei','06' => 'Juni','07' => 'Juli','08' => 'Agustus',
    '09' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember'
];
$nama_bulan = $nama_bulan_map[$bulan] ?? $bulan;

$sql = "SELECT id, Tanggal, Event, Paket 
        FROM jadwal_booking
        WHERE Tanggal >= DATE(?) 
          AND Tanggal < DATE(?)
        ORDER BY Tanggal ASC, id ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $start_month, $next_month);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}
mysqli_stmt_close($stmt);

$total_booking = count($rows);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Laporan Booking <?= $nama_bulan . " " . $tahun ?></title>
    <link rel="stylesheet" type="text/css" href="cetakbooking.css">
</head>
<body>

    <!-- Kop Laporan -->
    <div class="kop-laporan">
        <h1 style="font-family: 'Edwardian Script ITC', cursive; font-size: 64px; margin: 0;">Graceful</h1>
        <p style="font-style: italic; margin: 5px 0;">
            Kampung <u>Tanjungjaya</u>, Desa Dawagung, 
            <u>Kecamatan</u> Rajapolah, <u>Kabupaten Tasikmalaya</u>, 46155<br>
            No HP 087833379235 <u>Email</u>: gracefuldecoration@gmail.com
        </p>
        <hr style="border: 2px solid black; margin: 10px 0 0 0;">
        <h3 style="margin: 5px 0;">
            <u>LAPORAN JADWAL BOOKING BULAN <?= strtoupper($nama_bulan . " " . $tahun) ?></u>
        </h3>
    </div>

    <!-- Ringkasan -->
    <div class="summary">
        Total Booking: <?= $total_booking ?> event | Dicetak: <?= date('d/m/Y H:i') ?>
    </div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Event</th>
                <th>Paket</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="4"><em>Tidak ada data booking untuk periode ini.</em></td>
                </tr>
            <?php else: foreach ($rows as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= date('d/m/Y', strtotime($r['Tanggal'])) ?></td>
                    <td><?= htmlspecialchars($r['Event']) ?></td>
                    <td><?= htmlspecialchars($r['Paket']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="ttd">
        <div>
            <p>Admin Keuangan</p><br><br><br>
            <p><u>Dwiyanty Halimah</u></p>
        </div>
        <div>
            <p>Owner</p><br><br><br>
            <p><u>Dadang Darussalam</u></p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Graceful Decoration - Laporan Booking <?= $nama_bulan . " " . $tahun ?>
    </div>

    <!-- Tombol Cetak -->
    <div class="no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.print()">🖨 Cetak Laporan</button>
    </div>

</body>
</html>
