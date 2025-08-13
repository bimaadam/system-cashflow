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

$fontDir = __DIR__ . '/assets/fonts';
$brandTtf = $fontDir . '/edwardianscriptitc.ttf';

$mpdfOptions = [
    'format' => 'A4',
    'orientation' => 'P',
    'tempDir' => $mpdfTmp,
    'margin_left' => 20,
    'margin_right' => 20,
    'margin_top' => 15,
    'margin_bottom' => 15,
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
$nama_bulan = isset($nama_bulan_map[$bulan]) ? $nama_bulan_map[$bulan] : $bulan;


if (!isset($conn) || !$conn) {
    http_response_code(500);
    die('Koneksi database tidak tersedia.');
}

$sql = "SELECT id, Tanggal, Event, Paket FROM jadwal_booking
        WHERE Tanggal >= DATE(?) AND Tanggal < DATE(?)
        ORDER BY Tanggal ASC, id ASC";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    http_response_code(500);
    die('Query error: ' . htmlspecialchars(mysqli_error($conn)));
}
mysqli_stmt_bind_param($stmt, 'ss', $start_month, $next_month);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$rows = [];
while ($row = $result ? mysqli_fetch_assoc($result) : null) {
    $rows[] = $row;
}
mysqli_stmt_close($stmt);

$total_booking = count($rows);

$logo_src = '';
$logo_path = __DIR__ . '/assets/img/logo.png';
if (file_exists($logo_path)) {
    $logo_src = 'assets/img/logo.png';
}

// HTML content
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <style>
        @page {
            footer: html_footer;
        }
        
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 11pt; 
            color: #000;
            line-height: 1.5;
        }
        
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .company-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .company-info h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .company-address {
            font-size: 10pt;
            margin: 5px 0 0 0;
            line-height: 1.4;
        }
        
        .report-title {
            text-align: center;
            margin: 15px 0 5px 0;
        }
        
        .report-title h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .report-period {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 5px;
        }
        
        .summary {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 20px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td { 
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        
        th { 
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .no-column {
            width: 8%;
            text-align: center;
        }
        
        .date-column {
            width: 20%;
        }
        
        .event-column {
            width: 52%;
        }
        
        .package-column {
            width: 20%;
            text-align: center;
        }
        
        .text-center { 
            text-align: center;
            font-style: italic;
            padding: 20px;
        }
        
        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="kop-laporan" style="text-align: center;">
      <h1 style="font-family: 'edwardian','Edwardian Script ITC', cursive; font-size: 64px; margin: 0;">Graceful</h1>
      <p style="font-style: italic; margin: 5px 0;">
        Kampung <u>Tanjungjaya</u>, Desa Dawagung, <u>Kecamatan</u> Rajapolah, <u>Kabupaten Tasikmalaya</u>, 46155<br>
        No HP 087833379235 <u>Email</u>: gracefuldecoration@gmail.com
      </p>
      <hr style="border: 2px solid black; margin: 10px 0 0 0;">
      <div style="font-weight: bold; font-size: 18px; margin-top: -12px;">
        <h3><u>LAPORAN JADWAL BOOKING BULAN <?= strtoupper(htmlspecialchars($nama_bulan . ' ' . $tahun)) ?></u></h3>
      </div>
    </div>

    <div class="summary">
        Total Booking: <?= $total_booking ?> event | Dicetak: <?= date('d/m/Y H:i') ?>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th class="no-column">No</th>
                <th class="date-column">Tanggal</th>
                <th class="event-column">Nama Event</th>
                <th class="package-column">Paket</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
            <tr><td class="text-center" colspan="4">Tidak ada data booking untuk periode ini.</td></tr>
        <?php else: ?>
            <?php foreach ($rows as $index => $r): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td><?php 
                        $date = new DateTime($r['Tanggal']);
                        echo $date->format('d/m/Y');
                    ?></td>
                    <td><?= htmlspecialchars($r['Event']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($r['Paket']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <htmlpagefooter name="footer">
        <div class="footer">
            Graceful Decoration - Laporan Booking <?= htmlspecialchars($nama_bulan) . ' ' . htmlspecialchars($tahun) ?> | Halaman {PAGENO} dari {nbpg}
        </div>
    </htmlpagefooter>

</body>
</html>
<?php
$html = ob_get_clean();

try {
    $mpdf->WriteHTML($html);
    $filename = 'Laporan_Booking_' . $tahun . '_' . $bulan . '.pdf';
    $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
} catch (\Throwable $e) {
    http_response_code(500);
    die('Render PDF error: ' . htmlspecialchars($e->getMessage()));
}
?>