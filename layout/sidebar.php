<<style>
/* Efek glowing pada link sidebar */
.sb-sidenav .nav-link {
    position: relative;
    color: #fff !important;
    margin: 6px 10px;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
    z-index: 1;
}

/* background glow bergerak */
.sb-sidenav .nav-link::before {
    content: "";
    position: absolute;
    top: -2px; left: -2px; right: -2px; bottom: -2px;
    border-radius: 12px;
    background: linear-gradient(270deg, 
        rgba(174, 174, 182, 1), rgba(75, 78, 78, 1), rgba(59, 62, 59, 1), rgba(15, 15, 15, 1), rgba(64, 40, 40, 1), rgba(160, 154, 160, 1), rgba(66, 66, 92, 1));
    background-size: 400% 400%;
    animation: glowing 8s linear infinite;
    z-index: -1;
    opacity: 0; /* default transparan */
    transition: opacity 0.4s ease;
}

/* Saat hover atau aktif */
.sb-sidenav .nav-link:hover::before,
.sb-sidenav .nav-link.active::before {
    opacity: 1;
}

/* Warna dalam link biar transparan */
.sb-sidenav .nav-link.active, 
.sb-sidenav .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.05);
    font-weight: bold;
    color: #fff;
}

/* Animasi glowing */
@keyframes glowing {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

</style>
<?php
$current_tab = isset($tab) ? $tab : (isset($_GET['tab']) ? $_GET['tab'] : 'dashboard');

function sidebar_link_class($target, $base_class = 'nav-link') {
    global $current_tab;
    return $base_class . ($current_tab === $target ? ' active' : '');
}
?>
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Utama</div>
                <?php if (is_allowed($_SESSION['user_role'], 'dashboard')): ?>
                <a class="<?= sidebar_link_class('dashboard') ?>" href="?tab=dashboard" <?= $current_tab === 'dashboard' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <?php endif; ?>

                <div class="sb-sidenav-menu-heading">Transaksi</div>
                <?php if (is_allowed($_SESSION['user_role'], 'kas_masuk')): ?>
                <a class="<?= sidebar_link_class('kas_masuk') ?>" href="?tab=kas_masuk" <?= $current_tab === 'kas_masuk' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-arrow-down"></i></div>
                    Kas Masuk
                </a>
                <?php endif; ?>
                <?php if (is_allowed($_SESSION['user_role'], 'kas_keluar')): ?>
                <a class="<?= sidebar_link_class('kas_keluar') ?>" href="?tab=kas_keluar" <?= $current_tab === 'kas_keluar' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-arrow-up"></i></div>
                    Kas Keluar
                </a>
                <?php endif; ?>
                <?php if (is_allowed($_SESSION['user_role'], 'booking')): ?>
                <a class="<?= sidebar_link_class('booking') ?>" href="?tab=booking" <?= $current_tab === 'booking' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Jadwal Booking
                </a>
                <?php endif; ?>

                <div class="sb-sidenav-menu-heading">Laporan</div>
                <?php if (is_allowed($_SESSION['user_role'], 'laporan')): ?>
                <a class="<?= sidebar_link_class('laporan') ?>" href="?tab=laporan" <?= $current_tab === 'laporan' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                    Laporan Keuangan
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            Admin Keuangan
        </div>
    </nav>
</div>
