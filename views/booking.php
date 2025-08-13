<?php
// Dashboard 4 - Jadwal Booking
?>
<section id="dashboard4" class="mb-5">
    <h4>Jadwal Booking</h4>
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
                            <option value="Platinum">Platinum</option>
                        </select>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data_booking = mysqli_query($conn, "SELECT * FROM jadwal_booking ORDER BY Tanggal DESC");
                        while ($row = mysqli_fetch_assoc($data_booking)) {
                            echo "<tr>
                                    <td>{$row['Tanggal']}</td>
                                    <td>{$row['Event']}</td>
                                    <td>{$row['Paket']}</td>
                                    <td>
                                <a href='functions/booking_hapus.php?id={$row['id']}&redirect=dashboard.php?tab=booking' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
                                <button class='btn btn-sm btn-warning btn-edit-booking' 
                                    data-id='{$row['id']}' 
                                    data-tanggal='{$row['Tanggal']}'
                                    data-event='{$row['Event']}'
                                    data-paket='{$row['Paket']}'>
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
                                <option value="Platinum">Platinum</option>
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
