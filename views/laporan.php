<?php
?>
<section id="dashboard5" class="mb-5">
    <h4 class="mb-3">Laporan Keuangan Bulanan</h4>
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-chart-line me-1"></i> Laporan Kas Masuk & Keluar
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Event</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_union = "
                            SELECT Tanggal_Input, 'Masuk' AS Jenis, Event_WLE, Keterangan, Nominal
                            FROM penerimaan_kas
                            UNION ALL
                            SELECT Tanggal_Input, 'Keluar' AS Jenis, Event_WLE, Keterangan, Nominal
                            FROM pengeluaran_kas
                            ORDER BY Tanggal_Input DESC
                        ";
                        $result_union = mysqli_query($conn, $query_union);

                        while ($row = mysqli_fetch_assoc($result_union)) {
                            echo "<tr>
                                <td>{$row['Tanggal_Input']}</td>
                                <td>{$row['Jenis']}</td>
                                <td>{$row['Event_WLE']}</td>
                                <td>{$row['Keterangan']}</td>
                                <td>Rp " . number_format($row['Nominal'], 0, ',', '.') . "</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TOTAL BULANAN -->
    <div class="row g-3">
        <?php
                $start_month = date('Y-m-01 00:00:00');         $next_month  = date('Y-m-01 00:00:00', strtotime('first day of next month')); 
                $masuk = mysqli_fetch_assoc(mysqli_query(
            $conn,
            "SELECT COALESCE(SUM(Nominal),0) AS total FROM penerimaan_kas WHERE Tanggal_Input >= '$start_month' AND Tanggal_Input < '$next_month'"
        ));
        $masuk_total = isset($masuk['total']) ? (float)$masuk['total'] : 0.0;

                $keluar = mysqli_fetch_assoc(mysqli_query(
            $conn,
            "SELECT COALESCE(SUM(Nominal),0) AS total FROM pengeluaran_kas WHERE Tanggal_Input >= '$start_month' AND Tanggal_Input < '$next_month'"
        ));
        $keluar_total = isset($keluar['total']) ? (float)$keluar['total'] : 0.0;
        ?>
        <div class="col-md-6">
            <div class="alert alert-success shadow">
                <strong>Total Kas Masuk Bulan Ini:</strong> Rp <?= number_format($masuk_total, 0, ',', '.') ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-danger shadow">
                <strong>Total Kas Keluar Bulan Ini:</strong> Rp <?= number_format($keluar_total, 0, ',', '.') ?>
            </div>
        </div>
    </div>

    <!-- DOWNLOAD LAPORAN (Pilih Bulan) -->
    <div class="d-flex justify-content-end mt-3">
        <form action="cetaklaporan.php" method="get" target="_blank" class="d-flex align-items-center gap-2">
            <label for="bulan" class="me-1">Bulan:</label>
            <select name="bulan" id="bulan" class="form-select form-select-sm" required>
                <?php
                $bulan_now = date('m');
                $nama_bulan = [
                    '01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April',
                    '05' => 'Mei','06' => 'Juni','07' => 'Juli','08' => 'Agustus',
                    '09' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember'
                ];
                foreach ($nama_bulan as $num => $label) {
                    $sel = ($num === $bulan_now) ? 'selected' : '';
                    echo "<option value=\"$num\" $sel>$label</option>";
                }
                ?>
            </select>
            <label for="tahun" class="ms-2 me-1">Tahun:</label>
            <input type="number" name="tahun" id="tahun" class="form-control form-control-sm" min="2020" max="2100" value="<?= date('Y') ?>" required>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-download me-1"></i> Unduh PDF
            </button>
        </form>
    </div>
</section>
