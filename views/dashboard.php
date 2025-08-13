<?php

$summary = [
    'booking_count' => null,
    'kas_masuk_count' => null,
    'kas_keluar_count' => null,
    'kas_masuk_sum' => null,
    'kas_keluar_sum' => null,
];

if (isset($conn) && $conn) {
    try {
        if ($rs = $conn->query("SELECT COUNT(*) AS c FROM jadwal_booking")) {
            $row = $rs->fetch_assoc();
            $summary['booking_count'] = (int)($row['c'] ?? 0);
        }
        if ($rs = $conn->query("SELECT COUNT(*) AS c, COALESCE(SUM(Nominal),0) AS s FROM penerimaan_kas")) {
            $row = $rs->fetch_assoc();
            $summary['kas_masuk_count'] = (int)($row['c'] ?? 0);
            $summary['kas_masuk_sum'] = (float)($row['s'] ?? 0);
        }
        if ($rs = $conn->query("SELECT COUNT(*) AS c, COALESCE(SUM(Nominal),0) AS s FROM pengeluaran_kas")) {
            $row = $rs->fetch_assoc();
            $summary['kas_keluar_count'] = (int)($row['c'] ?? 0);
            $summary['kas_keluar_sum'] = (float)($row['s'] ?? 0);
        }
    } catch (Throwable $e) {
            }
}

function rupiah($v) {
    if ($v === null) return '-';
    return 'Rp ' . number_format((float)$v, 0, ',', '.');
}

$adminName = 'Admin';
if (!empty($_SESSION['user_id']) && isset($conn) && $conn) {
    try {
        $uid = (int)$_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT COALESCE(NULLIF(username,''), email) AS display_name FROM users WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $uid);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();
                if (!empty($row['display_name'])) {
                    $adminName = $row['display_name'];
                }
            }
            $stmt->close();
        }
    } catch (Throwable $e) {
                if (!empty($_SESSION['username'])) {
            $adminName = $_SESSION['username'];
        }
    }
} elseif (!empty($_SESSION['username'])) {
    $adminName = $_SESSION['username'];
}
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-start border-3 border-warning shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 display-6 text-warning"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <div class="small text-muted">Total Booking</div>
                    <div class="h4 mb-0"><?= $summary['booking_count'] !== null ? (int)$summary['booking_count'] : '-' ?></div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a class="small text-decoration-none" href="?tab=booking">Lihat Booking <i class="fas fa-angle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-3 border-success shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 display-6 text-success"><i class="fas fa-arrow-down"></i></div>
                <div>
                    <div class="small text-muted">Kas Masuk</div>
                    <div class="h5 mb-0">Jumlah: <?= $summary['kas_masuk_count'] !== null ? (int)$summary['kas_masuk_count'] : '-' ?></div>
                    <div class="small text-muted">Total: <?= rupiah($summary['kas_masuk_sum']) ?></div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a class="small text-decoration-none" href="?tab=kas_masuk">Lihat Kas Masuk <i class="fas fa-angle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-3 border-danger shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 display-6 text-danger"><i class="fas fa-arrow-up"></i></div>
                <div>
                    <div class="small text-muted">Kas Keluar</div>
                    <div class="h5 mb-0">Jumlah: <?= $summary['kas_keluar_count'] !== null ? (int)$summary['kas_keluar_count'] : '-' ?></div>
                    <div class="small text-muted">Total: <?= rupiah($summary['kas_keluar_sum']) ?></div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a class="small text-decoration-none" href="?tab=kas_keluar">Lihat Kas Keluar <i class="fas fa-angle-right"></i></a>
            </div>
        </div>
    </div>
</div>

<?php
$gallery = [];
if (function_exists('glob')) {
    $paths = glob('assets/img/Dekor*.{jpg,jpeg,png,JPG,JPEG,PNG}', GLOB_BRACE) ?: [];
    foreach ($paths as $p) {
        if (is_file($p)) { $gallery[] = $p; }
    }
}
?>

<?php if (!empty($gallery)): ?>
<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Galeri Dekorasi</h5>
        <small class="text-muted">Menampilkan <?= count($gallery) ?> foto</small>
    </div>
    <div class="row g-3">
        <?php foreach ($gallery as $idx => $img): if ($idx >= 12) break; ?>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="Galeri Dekorasi" loading="lazy" style="object-fit:cover;height:140px;">
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3 d-flex align-items-center">
        <div class="me-3 text-secondary display-6"><i class="fas fa-user-circle"></i></div>
        <div>
            <div class="h5 mb-1">Selamat datang, <strong><?= htmlspecialchars($adminName) ?></strong></div>
            <div class="text-muted small">Ringkasan aktivitas dan transaksi terbaru ditampilkan di atas.</div>
        </div>
    </div>
    <div class="card-footer bg-transparent border-0 pt-0">
        <a class="small text-decoration-none" href="?tab=booking">Lihat Jadwal Booking</a>
        · <a class="small text-decoration-none" href="?tab=kas_masuk">Lihat Kas Masuk</a>
        · <a class="small text-decoration-none" href="?tab=kas_keluar">Lihat Kas Keluar</a>
    </div>
    </div>
