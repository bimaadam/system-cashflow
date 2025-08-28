<?php
// Dashboard 4 - Jadwal Booking
?>
<section id="dashboard4" class="mb-5">
    <h4>Jadwal Booking</h4>

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

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Input Booking Event</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="functions/booking_tambah.php">
                <input type="hidden" name="redirect" value="dashboard.php?tab=booking">
                <div class="row g-3 align-items-center">
                    <!-- Input Tanggal -->
                    <div class="col-md-3">
                        <label for="tanggal" class="form-label"><i class="fas fa-calendar-day me-1"></i>Tanggal Booking</label>
                        <input type="date" name="Tanggal" class="form-control border-primary shadow-sm" required>
                    </div>

                    <!-- Input Nama Event -->
                    <div class="col-md-4">
                        <label for="Event" class="form-label"><i class="fas fa-star me-1"></i>Nama Event</label>
                        <input type="text" name="Event" class="form-control border-primary shadow-sm" placeholder="Contoh: Wedding Andi & Rina" required>
                    </div>

                    <!-- Dropdown Paket -->
                    <div class="col-md-3">
                        <label for="Paket" class="form-label"><i class="fas fa-box-open me-1"></i>Pilih Paket</label>
                        <select name="Paket" class="form-select border-primary shadow-sm" required>
                            <option value="">-- Pilih Paket --</option>
                            <option value="Silver">Silver</option>
                            <option value="Gold">Gold</option>
                            <option value="Diamond">Diamond</option>
                            <option value="Platinum">Platinum</option>
                        </select>
                    </div>

                    <!-- Status Pembayaran -->
                    <div class="col-md-3">
                        <label for="payment_status" class="form-label"><i class="fas fa-money-bill-wave me-1"></i>Status Pembayaran</label>
                        <select name="payment_status" id="payment_status" class="form-select border-primary shadow-sm">
                            <option value="belum_bayar" selected>Belum Bayar</option>
                            <option value="dibayar_sebagian">Dibayar Sebagian</option>
                            <option value="sudah_bayar">Sudah Bayar</option>
                        </select>
                    </div>

                    <!-- Total Tagihan (opsional) -->
                    <div class="col-md-3">
                        <label for="total_tagihan" class="form-label"><i class="fas fa-receipt me-1"></i>Total Tagihan (Rp)</label>
                        <input type="number" name="total_tagihan" id="total_tagihan" class="form-control border-primary shadow-sm" placeholder="0" min="0" step="1">
                    </div>
                    <!-- Uang Muka (opsional) -->
                    <div class="col-md-3">
                        <label for="uang_muka" class="form-label"><i class="fas fa-hand-holding-usd me-1"></i>Uang Muka (Rp) - Opsional</label>
                        <input type="number" name="uang_muka" id="uang_muka" class="form-control border-primary shadow-sm" placeholder="0" min="0" step="1">
                    </div>

                    <!-- Tombol -->
                    <div class="col-md-2 d-grid">
                        <label class="form-label invisible">.</label>
                        <button type="submit" name="jadwal_booking" class="btn btn-success shadow">
                            <i class="fas fa-plus-circle me-1"></i> Booking
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET" action="cetakbooking.php" target="_blank">
                <?php $nowMonth = date('m'); $nowYear = date('Y'); ?>
                <div class="col-sm-3 col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <?php for ($m=1; $m<=12; $m++): $mm = sprintf('%02d', $m); ?>
                            <option value="<?= $mm ?>" <?= $mm==$nowMonth?'selected':'' ?>><?= $mm ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php for ($y=$nowYear-3; $y<=$nowYear+1; $y++): ?>
                            <option value="<?= $y ?>" <?= $y==$nowYear?'selected':'' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-sm-5 col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i> Export Booking (PDF)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-table me-1"></i> Data Booking Graceful Dekorasi
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal Booking</th>
                            <th>Nama Event</th>
                            <th>Paket</th>
                            <th>Total Tagihan</th>
                            <th>Status Pembayaran</th>
                            <th>Sisa Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data_booking = mysqli_query($conn, "
                            SELECT b.*, 
                                   COALESCE(b.total_tagihan, 0) AS total_tagihan,
                                   COALESCE((SELECT SUM(pk.Nominal) FROM penerimaan_kas pk 
                                            WHERE pk.booking_id = b.id OR (pk.booking_id IS NULL AND pk.Event_WLE = b.Event)
                                   ), 0) AS total_bayar
                            FROM jadwal_booking b
                            ORDER BY b.Tanggal DESC
                        ");
                        while ($row = mysqli_fetch_assoc($data_booking)) {
                            $tagihan = isset($row['total_tagihan']) ? (int)$row['total_tagihan'] : 0;
                            $terbayar = isset($row['total_bayar']) ? (int)$row['total_bayar'] : 0;
                            // Hitung status dinamis berdasarkan total_tagihan vs total_bayar
                            $computedStatus = 'belum_bayar';
                            if ($tagihan > 0 && $terbayar >= $tagihan) {
                                $computedStatus = 'sudah_bayar';
                            } elseif ($terbayar > 0 && $terbayar < $tagihan) {
                                $computedStatus = 'dibayar_sebagian';
                            } else {
                                $computedStatus = 'belum_bayar';
                            }
                            // Fallback ke payment_status tersimpan jika tagihan = 0
                            $pay = ($tagihan > 0) ? $computedStatus : (isset($row['payment_status']) ? $row['payment_status'] : 'belum_bayar');
                            $badgeClass = 'bg-secondary';
                            if ($pay === 'belum_bayar') $badgeClass = 'bg-danger';
                            if ($pay === 'dibayar_sebagian') $badgeClass = 'bg-warning text-dark';
                            if ($pay === 'sudah_bayar') $badgeClass = 'bg-success';
                            $sisa = max(0, $tagihan - $terbayar);
                            echo "<tr>
                                    <td>{$row['Tanggal']}</td>
                                    <td>{$row['Event']}</td>
                                    <td>{$row['Paket']}</td>
                                    <td>Rp " . number_format($tagihan, 0, ',', '.') . "</td>
                                    <td><span class='badge {$badgeClass}'>" . htmlspecialchars(str_replace('_',' ',$pay)) . "</span></td>
                                    <td><strong class='text-danger'>Rp " . number_format($sisa, 0, ',', '.') . "</strong></td>
                                    <td>
                                <a href='functions/booking_hapus.php?id={$row['id']}&redirect=dashboard.php?tab=booking' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
                                <button class='btn btn-sm btn-warning btn-edit-booking' 
                                    data-id='{$row['id']}' 
                                    data-tanggal='{$row['Tanggal']}'
                                    data-event='{$row['Event']}'
                                    data-paket='{$row['Paket']}'
                                    data-payment_status='{$pay}'
                                    data-total_tagihan='{$tagihan}'
                                    >
                                            <i class='fas fa-edit'></i> Edit
                                        </button>
                                    </td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Booking -->
    <div class="modal fade" id="editBookingModal" tabindex="-1" aria-labelledby="editBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookingModalLabel">Edit Data Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="functions/booking_edit.php">
                    <input type="hidden" name="redirect" value="dashboard.php?tab=booking">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id-booking">
                        <div class="mb-3">
                            <label for="edit-tanggal-booking" class="form-label">Tanggal</label>
                            <input type="date" name="Tanggal" id="edit-tanggal-booking" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-event-booking" class="form-label">Nama Event</label>
                            <input type="text" name="Event" id="edit-event-booking" class="form-control" placeholder="Nama Event" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-paket-booking" class="form-label">Paket</label>
                            <select name="Paket" id="edit-paket-booking" class="form-select" required>
                                <option value="Silver">Silver</option>
                                <option value="Gold">Gold</option>
                                <option value="Diamond">Diamond</option>
                                <option value="Platinum">Platinum</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-total-tagihan" class="form-label">Total Tagihan (Rp)</label>
                            <input type="number" name="total_tagihan" id="edit-total-tagihan" class="form-control" placeholder="0" min="0" step="1">
                        </div>
                        <div class="alert alert-info">
                            Pembayaran tambahan/termin silakan input dari menu <strong>Kas Masuk</strong>. Field Uang Muka tidak tersedia pada edit untuk menghindari duplikasi pembayaran.
                        </div>
                        <div class="mb-3">
                            <label for="edit-payment-status" class="form-label">Status Pembayaran</label>
                            <select name="payment_status" id="edit-payment-status" class="form-select">
                                <option value="belum_bayar">Belum Bayar</option>
                                <option value="dibayar_sebagian">Dibayar Sebagian</option>
                                <option value="sudah_bayar">Sudah Bayar</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_booking" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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