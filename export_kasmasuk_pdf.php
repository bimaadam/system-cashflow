<?php
require 'function.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kas Masuk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<h2>Laporan Kas Masuk</h2>

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
        <?php
        $data_event = mysqli_query($conn, "SELECT * FROM penerimaan_kas ORDER BY Tanggal_Input DESC");
        $total = 0;
        while ($row = mysqli_fetch_assoc($data_event)) {
            $total += $row['Nominal'];
            echo "<tr>
                    <td>{$row['Tanggal_Input']}</td>
                    <td>{$row['Event_WLE']}</td>
                    <td>{$row['Keterangan']}</td>
                    <td>Rp " . number_format($row['Nominal'], 0, ',', '.') . "</td>
                </tr>";
        }
        ?>
    </tbody>
</table>

<div class="total">
    Total Kas Masuk: Rp <?= number_format($total, 0, ',', '.') ?>
</div>

<div class="no-print" style="margin-top: 20px; text-align: center;">
    <button onclick="window.print()">Print / Cetak</button>
</div>

</body>
</html>
