<?php
require 'function.php';

@include __DIR__ . '/vendor/autoload.php';
if (!class_exists('Mpdf\\Mpdf')) {
    die('mPDF belum terpasang. Silakan install dengan: composer require mpdf/mpdf');
}

use Mpdf\Mpdf;
$mpdfTmp = __DIR__ . '/tmp';
if (!is_dir($mpdfTmp)) {
    @mkdir($mpdfTmp, 0777, true);
}
if (!is_writable($mpdfTmp)) {
    @chmod($mpdfTmp, 0777);
}

// Embed Edwardian Script ITC font if available
$fontDir = __DIR__ . '/assets/fonts';
$brandTtf = $fontDir . '/edwardianscriptitc.ttf';

$mpdfOptions = [
    'format' => 'A4',
    'tempDir' => $mpdfTmp,
];

if (is_file($brandTtf)) {
    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];
    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];
    $mpdfOptions['fontDir'] = array_merge($fontDirs, [$fontDir]);
    $mpdfOptions['fontdata'] = $fontData + [ 'edwardian' => ['R' => basename($brandTtf)] ];
}

try {
    $mpdf = new Mpdf($mpdfOptions);
} catch (\Throwable $e) {
    http_response_code(500);
    die('mPDF init error: ' . htmlspecialchars($e->getMessage()));
}
$bulan = isset($_GET['bulan']) && preg_match('/^\d{2}$/', $_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) && preg_match('/^\d{4}$/', $_GET['tahun']) ? $_GET['tahun'] : date('Y');

$start_month = sprintf('%04d-%02d-01 00:00:00', (int)$tahun, (int)$bulan);
$next_month  = date('Y-m-01 00:00:00', strtotime("$start_month +1 month"));

$nama_bulan_map = [
    '01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April',
    '05' => 'Mei','06' => 'Juni','07' => 'Juli','08' => 'Agustus',
    '09' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember'
];
$eventFilter = isset($_GET['event']) ? trim($_GET['event']) : '';
$bulan_format = ($nama_bulan_map[$bulan] ?? date('F', strtotime($start_month))) . ' ' . $tahun;

// Build queries with optional event filter using prepared statements
if ($eventFilter !== '') {
    $like = "%" . $eventFilter . "%";
    // Kas Masuk list
    $stmtMasuk = mysqli_prepare($conn, "SELECT * FROM penerimaan_kas WHERE Tanggal_Input >= ? AND Tanggal_Input < ? AND Event_WLE LIKE ? ORDER BY Tanggal_Input ASC");
    mysqli_stmt_bind_param($stmtMasuk, 'sss', $start_month, $next_month, $like);
    mysqli_stmt_execute($stmtMasuk);
    $kas_masuk = mysqli_stmt_get_result($stmtMasuk);
    // Kas Keluar list
    $stmtKeluar = mysqli_prepare($conn, "SELECT * FROM pengeluaran_kas WHERE Tanggal_Input >= ? AND Tanggal_Input < ? AND Event_WLE LIKE ? ORDER BY Tanggal_Input ASC");
    mysqli_stmt_bind_param($stmtKeluar, 'sss', $start_month, $next_month, $like);
    mysqli_stmt_execute($stmtKeluar);
    $kas_keluar = mysqli_stmt_get_result($stmtKeluar);
    // Totals
    $stmtTotMasuk = mysqli_prepare($conn, "SELECT COALESCE(SUM(Nominal),0) AS total FROM penerimaan_kas WHERE Tanggal_Input >= ? AND Tanggal_Input < ? AND Event_WLE LIKE ?");
    mysqli_stmt_bind_param($stmtTotMasuk, 'sss', $start_month, $next_month, $like);
    mysqli_stmt_execute($stmtTotMasuk);
    $total_masuk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTotMasuk));
    $stmtTotKeluar = mysqli_prepare($conn, "SELECT COALESCE(SUM(Nominal),0) AS total FROM pengeluaran_kas WHERE Tanggal_Input >= ? AND Tanggal_Input < ? AND Event_WLE LIKE ?");
    mysqli_stmt_bind_param($stmtTotKeluar, 'sss', $start_month, $next_month, $like);
    mysqli_stmt_execute($stmtTotKeluar);
    $total_keluar = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTotKeluar));
} else {
    $kas_masuk = mysqli_query($conn, "SELECT * FROM penerimaan_kas 
        WHERE Tanggal_Input >= '$start_month' AND Tanggal_Input < '$next_month'
        ORDER BY Tanggal_Input ASC");
    $kas_keluar = mysqli_query($conn, "SELECT * FROM pengeluaran_kas 
        WHERE Tanggal_Input >= '$start_month' AND Tanggal_Input < '$next_month'
        ORDER BY Tanggal_Input ASC");
    $total_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(Nominal),0) AS total FROM penerimaan_kas WHERE Tanggal_Input >= '$start_month' AND Tanggal_Input < '$next_month'"));
    $total_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(Nominal),0) AS total FROM pengeluaran_kas WHERE Tanggal_Input >= '$start_month' AND Tanggal_Input < '$next_month'"));
}

ob_start(); ?>

<!-- HTML untuk PDF -->
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; }
        .header { text-align: center; margin-bottom: 10px; }
        .logo { width: 100px; margin-bottom: 10px; }
        .alamat { font-size: 11pt; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section-title { font-weight: bold; font-size: 14pt; margin: 15px 0 5px; }
        .total { font-weight: bold; }
    </style>
</head>
<body>

<div class="kop-laporan" style="text-align: center;">
  <h1 style="font-family: 'edwardian','Edwardian Script ITC', cursive; font-size: 64px; margin: 0;">Graceful</h1>
  <p style="font-style: italic; margin: 5px 0;">
    Kampung <u>Tanjungjaya</u>, Desa Dawagung, <u>Kecamatan</u> Rajapolah, <u>Kabupaten Tasikmalaya</u>, 46155<br>
    No HP 087833379235 <u>Email</u>: gracefuldecoration@gmail.com
  </p>
  <hr style="border: 2px solid black; margin: 10px 0 0 0;">
  <div style="font-weight: bold; font-size: 18px; margin-top: -12px;">
    <h3><u>LAPORAN KEUANGAN BULAN <?= strtoupper($bulan_format) ?></u></h3>
  </div>
  <?php if ($eventFilter !== ''): ?>
    <div style="margin-top:4px; font-size: 11pt;">Filter Event: <strong><?= htmlspecialchars($eventFilter) ?></strong></div>
  <?php endif; ?>
</div>

<!-- Kas Masuk -->
<div class="section-title">Kas Masuk</div>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Event</th>
            <th>Keterangan</th>
            <th>Nominal (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($kas_masuk)) { ?>
            <tr>
                <td><?= $row['Tanggal_Input'] ?></td>
                <td><?= $row['Event_WLE'] ?></td>
                <td><?= $row['Keterangan'] ?></td>
                <td style="text-align: right;"><?= number_format($row['Nominal'], 0, ',', '.') ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Kas Keluar -->
<div class="section-title">Kas Keluar</div>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Event</th>
            <th>Keterangan</th>
            <th>Nama Akun</th>
            <th>Nominal (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($kas_keluar)) { ?>
            <tr>
                <td><?= $row['Tanggal_Input'] ?></td>
                <td><?= $row['Event_WLE'] ?></td>
                <td><?= $row['Keterangan'] ?></td>
                <td><?= $row['Nama_Akun'] ?></td>
                <td style="text-align: right;"><?= number_format($row['Nominal'], 0, ',', '.') ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Total Bulanan -->
<table>
    <tr>
        <td class="total" style="background-color: #d4edda;">Total Kas Masuk Bulan Ini</td>
        <td class="total" style="text-align: right; background-color: #d4edda;">Rp <?= number_format($total_masuk['total'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td class="total" style="background-color: #f8d7da;">Total Kas Keluar Bulan Ini</td>
        <td class="total" style="text-align: right; background-color: #f8d7da;">Rp <?= number_format($total_keluar['total'], 0, ',', '.') ?></td>
    </tr>
</table>

</body>
</html>

<?php
$html = ob_get_clean();
try {
    $mpdf->WriteHTML($html);
    $mpdf->Output('Laporan_Keuangan_' . date('Y_m') . '.pdf', 'I');
} catch (\Throwable $e) {
    http_response_code(500);
    die('mPDF rendering error: ' . htmlspecialchars($e->getMessage()));
}
