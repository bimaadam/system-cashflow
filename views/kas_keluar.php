<?php
// Dashboard 3 - Kas Keluar
?>
<section id="dashboard3" class="mb-5">
    <h4 class="mb-3">Kas Keluar</h4>

   <!-- ALERT (Pesan Sukses / Gagal) -->
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'success'): ?>
        <div class="heart-alert success" id="heartAlert">
            <span>💖 SIPP MIN, DATANYA MASUK 😘 💖</span>
            <button class="close-btn" onclick="closeHeart()">×</button>
        </div>
    <?php elseif ($_GET['status'] == 'error'): ?>
        <div class="heart-alert error" id="heartAlert">
            <span>💔 Data Gagal Disimpan! Silakan coba lagi 💔</span>
            <button class="close-btn" onclick="closeHeart()">×</button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<style>
.heart-alert {
    position: relative;
    width: 250px;
    height: 220px;
    margin: 20px auto;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 20px;
    font-weight: bold;
    color: white;
    animation: popIn 0.6s ease forwards;
    font-size: 14px;
    line-height: 1.4em;

    clip-path: path("M125 220 L10 100 A60 60 0 0 1 125 40 A60 60 0 0 1 240 100 Z");
    opacity: 0;
}

/* Warna */
.heart-alert.success {
    background: linear-gradient(135deg, #ff69b4, #ff1493);
    box-shadow: 0 0 20px rgba(255, 20, 147, 0.7);
}
.heart-alert.error {
    background: linear-gradient(135deg, #ff4e50, #8b0000);
    box-shadow: 0 0 20px rgba(139, 0, 0, 0.7);
}

/* Tombol close */
.heart-alert .close-btn {
    position: absolute;
    top: 5px;
    right: 10px;
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}

/* Animasi masuk & keluar */
@keyframes popIn {
    0% { transform: scale(0.5); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.8); }
}
.fade-out {
    animation: fadeOut 0.1s forwards;
}
</style>

    <!-- FILTER EVENT -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="tab" value="kas_keluar" />
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="form-label">Cari Event</label>
                    <input type="text" name="event" value="<?= isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '' ?>" class="form-control" placeholder="Cari..." />
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i> Filter</button>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <a class="btn btn-outline-secondary" href="dashboard.php?tab=kas_keluar"><i class="fas fa-undo me-1"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Input -->
    <form method="POST" action="functions/kas_keluar_tambah.php">
        <input type="hidden" name="redirect" value="dashboard.php?tab=kas_keluar">
        <div class="row g-3 mb-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Tanggal</label>
                <input type="date" name="Tanggal_Input" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nama Event</label>
                <input type="text" name="Event_WLE" class="form-control" placeholder="Nama Event" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Keterangan</label>
                <input type="text" name="Keterangan" class="form-control" placeholder="Keterangan pengeluaran" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Nama Akun</label>
                <select name="Nama_Akun" class="form-select" required>
                    <option value="">Pilih Akun</option>
                    <option>Rental</option>
                    <option>Bensin</option>
                    <option>Peralatan</option>
                    <option>Konsumsi</option>
                    <option>Modal</option>
                    <option>Gaji</option>
                    <option>Dll</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Nominal</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="Nominal" class="form-control" placeholder="0" min="0" step="1" required>
                </div>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah
                </button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-table me-1"></i> Data Pengeluaran (Kas Keluar)
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Event</th>
                            <th>Keterangan</th>
                            <th>Nama Akun</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $eventFilter = isset($_GET['event']) ? trim($_GET['event']) : '';
                        if ($eventFilter !== '') {
                            $sql = "SELECT * FROM pengeluaran_kas WHERE Event_WLE LIKE CONCAT('%', ?, '%') ORDER BY Tanggal_Input DESC";
                            if ($stmt = mysqli_prepare($conn, $sql)) {
                                mysqli_stmt_bind_param($stmt, 's', $eventFilter);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                            } else {
                                $result = false;
                            }
                        } else {
                            $result = mysqli_query($conn, "SELECT * FROM pengeluaran_kas ORDER BY Tanggal_Input DESC");
                        }
                        while ($result && ($row = mysqli_fetch_assoc($result))) {
                            echo "<tr>
                                    <td>{$row['Tanggal_Input']}</td>
                                    <td>{$row['Event_WLE']}</td>
                                    <td>{$row['Keterangan']}</td>
                                    <td>{$row['Nama_Akun']}</td>
                                    <td>Rp " . number_format($row['Nominal'], 0, ',', '.') . "</td>
                                    <td>
                                        <a href='functions/kas_keluar_hapus.php?id={$row['id']}&redirect=dashboard.php?tab=kas_keluar' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
                                        <button class='btn btn-sm btn-warning btn-edit-pengeluaran' 
                                            data-id='{$row['id']}' 
                                            data-tanggal='{$row['Tanggal_Input']}'
                                            data-event='{$row['Event_WLE']}'
                                            data-keterangan='{$row['Keterangan']}'
                                            data-nama_akun='{$row['Nama_Akun']}'
                                            data-nominal='{$row['Nominal']}'>
                                            <i class='fas fa-edit'></i> Edit
                                        </button>
                                    </td>
                                </tr>";
                        }
                        if (isset($stmt) && $stmt) { mysqli_stmt_close($stmt); }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kas Keluar -->
    <div class="modal fade" id="editKasKeluarModal" tabindex="-1" aria-labelledby="editKasKeluarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editKasKeluarModalLabel">Edit Data Kas Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="functions/kas_keluar_edit.php">
                    <input type="hidden" name="redirect" value="dashboard.php?tab=kas_keluar">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id-pengeluaran">
                        <div class="mb-3">
                            <label for="edit-tanggal-pengeluaran" class="form-label">Tanggal</label>
                            <input type="date" name="Tanggal_Input" id="edit-tanggal-pengeluaran" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-event-pengeluaran" class="form-label">Nama Event</label>
                            <input type="text" name="Event_WLE" id="edit-event-pengeluaran" class="form-control" placeholder="Nama Event" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-keterangan-pengeluaran" class="form-label">Keterangan</label>
                            <input type="text" name="Keterangan" id="edit-keterangan-pengeluaran" class="form-control" placeholder="Keterangan" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-nama-akun-pengeluaran" class="form-label">Nama Akun</label>
                            <select name="Nama_Akun" id="edit-nama-akun-pengeluaran" class="form-select" required>
                                <option value="Rental">Rental</option>
                                <option value="Bensin">Bensin</option>
                                <option value="Peralatan">Peralatan</option>
                                <option value="Konsumsi">Konsumsi</option>
                                <option value="Modal">Modal</option>
                                <option value="Dll">Dll</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-nominal-pengeluaran" class="form-label">Nominal (Rp)</label>
                            <input type="number" name="Nominal" id="edit-nominal-pengeluaran" class="form-control" placeholder="Nominal" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_pengeluaran" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Cetak Laporan -->
    <form action="cetak_kaskeluar.php" method="get" target="_blank" style="display: flex; gap: 10px; align-items: center;">
        <label for="bulan">Bulan:</label>
        <select name="bulan" id="bulan" required>
            <option value="01">Januari</option>
            <option value="02">Februari</option>
            <option value="03">Maret</option>
            <option value="04">April</option>
            <option value="05">Mei</option>
            <option value="06">Juni</option>
            <option value="07">Juli</option>
            <option value="08">Agustus</option>
            <option value="09">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
        </select>
        <label for="tahun">Tahun:</label>
        <input type="number" name="tahun" id="tahun" min="2020" max="2100" value="<?= date('Y') ?>" required>
        <button type="submit" class="btn btn-primary">Cetak Laporan Kas Keluar</button>
    </form>
</section>
<script>
function closeHeart() {
    const alertBox = document.getElementById("heartAlert");
    if (alertBox) {
        alertBox.classList.add("fade-out");
        setTimeout(() => alertBox.remove(), 600); // hapus setelah animasi selesai
    }
}

// Otomatis close setelah 2 detik
document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        closeHeart();
    }, 2000);
});
</script>
