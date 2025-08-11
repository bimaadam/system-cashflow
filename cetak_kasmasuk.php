<?php
require 'function.php';

// Ambil bulan dan tahun dari GET atau default ke bulan sekarang
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query data per bulan dan tahun
$query = mysqli_query($conn, "SELECT * FROM penerimaan_kas 
    WHERE MONTH(Tanggal_Input) = '$bulan' AND YEAR(Tanggal_Input) = '$tahun' 
    ORDER BY Tanggal_Input ASC");

$total = 0;
$nama_bulan = date("F", mktime(0, 0, 0, $bulan, 10));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kas Masuk Bulan <?= $nama_bulan . " " . $tahun ?></title>
    <style>
        @media print {
  .no-print {
    display: none !important;
  }

  body {
    margin: 0;
    padding: 0;
    height: 100vh;
    position: relative;
  }

  .ttd {
    position: absolute;
    bottom: 60px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    padding: 0 30px;
  }
}

        body { font-family: Arial; margin: 30px; }
        .logo { width: 150px; }
        .center { text-align: center; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        .ttd { margin-top: 80px; display: flex; justify-content: space-between; }
        .ttd div { text-align: center; width: 40%; }
    </style>
</head>
<body>

<div class="center">
<div class="kop-laporan" style="text-align: center;">
  <h1 style="font-family: 'Edwardian Script ITC', cursive; font-size: 64px; margin: 0;">Graceful</h1>
  <p style="font-style: italic; margin: 5px 0;">
    Kampung <u>Tanjungjaya</u>, Desa Dawagung, <u>Kecamatan</u> Rajapolah, <u>Kabupaten Tasikmalaya</u>, 46155<br>
    No HP 087833379235 <u>Email</u>: gracefuldecoration@gmail.com
  </p>
  <hr style="border: 2px solid black; margin: 10px 0 0 0;">
  <div style="font-weight: bold; font-size: 18px; margin-top: -12px;">
   <h3><u>LAPORAN KAS MASUK BULAN <?= strtoupper($nama_bulan . " " . $tahun) ?></u></h3>
  </div>
</div>
    </p>
</div>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Event</th>
            <th>Keterangan</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while($data = mysqli_fetch_array($query)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d-m-Y', strtotime($data['Tanggal_Input'])) ?></td>
            <td><?= $data['Event_WLE'] ?></td>
            <td><?= $data['Keterangan'] ?></td>
            <td>Rp <?= number_format($data['Nominal'], 0, ',', '.') ?></td>
        </tr>
        <?php $total += $data['Nominal']; endwhile; ?>
    </tbody>
</table>

<p><strong>Total Kas Masuk Bulan Ini: Rp <?= number_format($total, 0, ',', '.') ?></strong></p>

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

<br>
<div class="center no-print">
    <button onclick="window.print()">🖨 Cetak Laporan</button>
</div>

</body>
</html>

