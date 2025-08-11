<?php
require 'function.php';

use Mpdf\Mpdf;

$mpdf = new Mpdf(['format' => 'A4']);
$bulan_ini = date('Y-m');
$bulan_format = date('F Y');

// Ambil data kas masuk
$kas_masuk = mysqli_query($conn, "SELECT * FROM penerimaan_kas ORDER BY Tanggal_Input ASC");

// Ambil data kas keluar
$kas_keluar = mysqli_query($conn, "SELECT * FROM pengeluaran_kas ORDER BY Tanggal_Input ASC");

// Total per bulan
$total_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(Nominal) AS total FROM penerimaan_kas WHERE DATE_FORMAT(Tanggal_Input, '%Y-%m') = '$bulan_ini'"));
$total_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(Nominal) AS total FROM pengeluaran_kas WHERE DATE_FORMAT(Tanggal_Input, '%Y-%m') = '$bulan_ini'"));

ob_start(); // Start output buffering
?>

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

<div class="header">
    <img src="logo_graceful.png" class="logo" alt="Logo Graceful"><br>
    <div class="alamat">Graceful Decoration | Jl. Mawar No. 12, Bandung | Telp: (022) 12345678</div>
    <h2>Laporan Keuangan</h2>
    <div>Bulan: <?= $bulan_format ?></div>
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
        <?php
        mysqli_data_seek($kas_keluar, 0);
        while ($row = mysqli_fetch_assoc($kas_keluar)) { ?>
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
$mpdf->WriteHTML($html);
$mpdf->Output('Laporan_Keuangan_' . date('Y_m') . '.pdf', 'I');
