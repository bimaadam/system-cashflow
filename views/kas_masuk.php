<?php
// Dashboard 2 - Kas Masuk
?>
<section id="dashboard2" class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="mb-0">Kas Masuk</h4>
    </div>

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
                <input type="hidden" name="tab" value="kas_masuk" />
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="form-label">Cari Event</label>
                    <input type="text" name="event" value="<?= isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '' ?>" class="form-control" placeholder="Cari..." />
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="start" value="<?= isset($_GET['start']) ? htmlspecialchars($_GET['start']) : '' ?>" class="form-control" />
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label">Sampai</label>
                    <input type="date" name="end" value="<?= isset($_GET['end']) ? htmlspecialchars($_GET['end']) : '' ?>" class="form-control" />
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i> Filter</button>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <a class="btn btn-outline-secondary" href="dashboard.php?tab=kas_masuk"><i class="fas fa-undo me-1"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- FORM INPUT KAS MASUK -->
    <form method="POST" action="functions/kas_masuk_tambah.php">
        <input type="hidden" name="redirect" value="dashboard.php?tab=kas_masuk">
        <input type="hidden" name="id" value="<?= isset($data_edit) ? $data_edit['id'] : '' ?>">
        <div class="row g-3 mb-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Tanggal</label>
                <input type="date" name="Tanggal_Input" class="form-control" value="<?= isset($data_edit) ? $data_edit['Tanggal_Input'] : '' ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nama Event</label>
                <select name="Event_WLE" id="event-select" class="form-control" required>
                    <option value="">-- Pilih Event --</option>
                    <?php
                    $booking_query = mysqli_query($conn, "
                        SELECT b.id, b.Tanggal, b.Event, b.Paket,
                               COALESCE(b.total_tagihan, 0) AS total_tagihan,
                               COALESCE((SELECT SUM(pk.Nominal) FROM penerimaan_kas pk 
                                         WHERE pk.booking_id = b.id OR (pk.booking_id IS NULL AND pk.Event_WLE = b.Event)), 0) AS total_bayar
                        FROM jadwal_booking b
                        ORDER BY b.Tanggal DESC
                    ");
                    while ($booking = mysqli_fetch_assoc($booking_query)) {
                        $eventVal = htmlspecialchars($booking['Event']);
                        $paketVal = htmlspecialchars($booking['Paket']);
                        $tglVal = htmlspecialchars($booking['Tanggal']);
                        $idVal = (int)$booking['id'];
                        $tagihanVal = (int)$booking['total_tagihan'];
                        $terbayarVal = (int)$booking['total_bayar'];
                        echo "<option value='{$eventVal}' data-id='{$idVal}' data-paket='{$paketVal}' data-tanggal='{$tglVal}' data-total_tagihan='{$tagihanVal}' data-total_bayar='{$terbayarVal}'>{$eventVal}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Keterangan</label>
                <input type="text" name="Keterangan" class="form-control" placeholder="Keterangan pemasukan" value="<?= isset($data_edit) ? $data_edit['Keterangan'] : '' ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nominal</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="Nominal" class="form-control" placeholder="0" min="0" step="1" value="<?= isset($data_edit) ? $data_edit['Nominal'] : '' ?>" required>
                </div>
            </div>
            <input type="hidden" name="booking_id" id="booking-id-input" value="">
             <!-- Info Status Pembayaran untuk event terpilih -->
             <div class="col-12">
                 <div id="payment-info" class="alert alert-light border d-flex justify-content-between align-items-center" role="alert" style="display:none">
                     <div>
                         <strong>Status:</strong> <span id="payment-status-text">-</span>
                     </div>
                     <div>
                         <strong>Sisa belum dibayar:</strong> <span id="payment-remaining-text">Rp 0</span>
                     </div>
                 </div>
                 <div id="paid-notice" class="alert alert-success" role="alert" style="display:none">
                     <i class="fas fa-check-circle me-1"></i> Event ini sudah dibayar lunas.
                 </div>
             </div>
            <div class="col-md-1 d-grid">
                <?php if (isset($data_edit)) { ?>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                <?php } else { ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                <?php } ?>
            </div>
        </div>
    </form>

    <!-- TABEL KAS MASUK -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-table me-1"></i> Data Penerimaan (Kas Masuk)
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Event</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $eventFilter = isset($_GET['event']) ? trim($_GET['event']) : '';
                        $startDate = isset($_GET['start']) ? trim($_GET['start']) : '';
                        $endDate = isset($_GET['end']) ? trim($_GET['end']) : '';

                        $where = [];
                        if ($eventFilter !== '') {
                            $where[] = "Event_WLE LIKE '%" . mysqli_real_escape_string($conn, $eventFilter) . "%'";
                        }
                        if ($startDate !== '') {
                            $where[] = "Tanggal_Input >= '" . mysqli_real_escape_string($conn, $startDate) . "'";
                        }
                        if ($endDate !== '') {
                            $where[] = "Tanggal_Input <= '" . mysqli_real_escape_string($conn, $endDate) . "'";
                        }
                        $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';
                        $result = mysqli_query($conn, "SELECT * FROM penerimaan_kas $whereSql ORDER BY Tanggal_Input DESC");

                        while ($result && ($row = mysqli_fetch_assoc($result))) {
                            echo "<tr>
                                <td>{$row['Tanggal_Input']}</td>
                                <td>{$row['Event_WLE']}</td>
                                <td>{$row['Keterangan']}</td>
                                <td>Rp " . number_format($row['Nominal'], 0, ',', '.') . "</td>
                                <td>
                                <a href='functions/kas_masuk_hapus.php?id={$row['id']}&redirect=dashboard.php?tab=kas_masuk' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
                                    <button class='btn btn-sm btn-warning btn-edit' 
                                        data-id='{$row['id']}' 
                                        data-tanggal='{$row['Tanggal_Input']}'
                                        data-event='{$row['Event_WLE']}'
                                        data-keterangan='{$row['Keterangan']}'
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

    <!-- Modal Edit Kas Masuk -->
    <div class="modal fade" id="editKasMasukModal" tabindex="-1" aria-labelledby="editKasMasukModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editKasMasukModalLabel">Edit Data Kas Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="functions/kas_masuk_edit.php">
                    <input type="hidden" name="redirect" value="dashboard.php?tab=kas_masuk">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label for="edit-tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="Tanggal_Input" id="edit-tanggal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-event" class="form-label">Nama Event</label>
                            <input type="text" name="Event_WLE" id="edit-event" class="form-control" placeholder="Nama Event" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-keterangan" class="form-label">Keterangan</label>
                            <input type="text" name="Keterangan" id="edit-keterangan" class="form-control" placeholder="Keterangan" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-nominal" class="form-label">Nominal (Rp)</label>
                            <input type="number" name="Nominal" id="edit-nominal" class="form-control" placeholder="Nominal" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_event" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Cetak Laporan -->
    <form action="cetak_kasmasuk.php" method="get" target="_blank" style="display: flex; gap: 10px; align-items: center;">
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
        <button type="submit" class="btn btn-primary">Cetak Laporan Kas Masuk</button>
    </form>
</section>
<script>
    // Format angka ke Rupiah sederhana
    function formatRupiah(num) {
        num = Math.max(0, Number(num) || 0);
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    // Saat event dipilih, isi tanggal & keterangan, dan tampilkan status pembayaran + sisa tagihan
    document.getElementById('event-select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const eventName = this.value;
        const paket = selectedOption.getAttribute('data-paket');
        const tanggal = selectedOption.getAttribute('data-tanggal');
        const bookingId = selectedOption.getAttribute('data-id');
        const totalTagihan = parseInt(selectedOption.getAttribute('data-total_tagihan') || '0', 10);
        const totalBayar = parseInt(selectedOption.getAttribute('data-total_bayar') || '0', 10);

        document.querySelector('input[name="Tanggal_Input"]').value = tanggal || '';
        document.querySelector('input[name="Keterangan"]').value = "Pembayaran Booking - " + eventName + (paket ? " (" + paket + ")" : "");
        const bookingIdInput = document.getElementById('booking-id-input');
        if (bookingIdInput) bookingIdInput.value = bookingId || '';

        // Hitung status dan sisa
        let status = 'belum bayar';
        if (totalBayar > 0 && totalBayar < totalTagihan) status = 'dibayar sebagian';
        if (totalTagihan > 0 && totalBayar >= totalTagihan) status = 'sudah bayar';
        const sisa = Math.max(0, totalTagihan - totalBayar);

        // Tampilkan info
        const infoBox = document.getElementById('payment-info');
        const statusText = document.getElementById('payment-status-text');
        const remainingText = document.getElementById('payment-remaining-text');
        statusText.textContent = status;
        remainingText.textContent = formatRupiah(sisa).replace('Rp ', 'Rp ');
        infoBox.style.display = '';

        // Tampilkan notifikasi lunas jika sudah_bayar
        const paidNotice = document.getElementById('paid-notice');
        if (paidNotice) paidNotice.style.display = (status === 'sudah bayar') ? '' : 'none';

        // Overpay protection: set max nominal dan beri hint
        const nominalEl = document.querySelector('input[name="Nominal"]');
        if (nominalEl) {
            nominalEl.max = String(sisa > 0 ? sisa : '');
            nominalEl.placeholder = sisa > 0 ? ("Maks " + sisa.toLocaleString('id-ID')) : nominalEl.placeholder;
        }
    });

    // Cegah overpay saat submit (soft guard: warning, bisa lanjut)
    (function(){
        const form = document.querySelector('form[action="functions/kas_masuk_tambah.php"]');
        if (!form) return;
        form.addEventListener('submit', function(e){
            const remainingText = document.getElementById('payment-remaining-text');
            const nominalEl = form.querySelector('input[name="Nominal"]');
            const statusTextEl = document.getElementById('payment-status-text');
            if (!remainingText || !nominalEl) return;
            const sisaStr = remainingText.textContent.replace(/[^0-9]/g, '');
            const sisa = parseInt(sisaStr || '0', 10);
            const nominal = parseInt(nominalEl.value || '0', 10);
            if (sisa > 0 && nominal > sisa) {
                const ok = confirm('Nominal melebihi sisa. Tetap simpan?');
                if (!ok) {
                    e.preventDefault();
                }
            }
            const statusTxt = statusTextEl ? (statusTextEl.textContent || '').toLowerCase() : '';
            if (statusTxt.includes('sudah')) {
                const okPaid = confirm('Event ini sudah dibayar lunas. Tetap simpan pembayaran?');
                if (!okPaid) {
                    e.preventDefault();
                }
            }
        });
    })();
</script>
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