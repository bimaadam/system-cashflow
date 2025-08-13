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
                <a class="<?= sidebar_link_class('dashboard') ?>" href="?tab=dashboard" <?= $current_tab === 'dashboard' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <div class="sb-sidenav-menu-heading">Transaksi</div>
                <a class="<?= sidebar_link_class('kas_masuk') ?>" href="?tab=kas_masuk" <?= $current_tab === 'kas_masuk' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-arrow-down"></i></div>
                    Kas Masuk
                </a>
                <a class="<?= sidebar_link_class('kas_keluar') ?>" href="?tab=kas_keluar" <?= $current_tab === 'kas_keluar' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-arrow-up"></i></div>
                    Kas Keluar
                </a>
                <a class="<?= sidebar_link_class('booking') ?>" href="?tab=booking" <?= $current_tab === 'booking' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Jadwal Booking
                </a>

                <div class="sb-sidenav-menu-heading">Laporan</div>
                <a class="<?= sidebar_link_class('laporan') ?>" href="?tab=laporan" <?= $current_tab === 'laporan' ? 'aria-current="page"' : '' ?>>
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                    Laporan Keuangan
                </a>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            Admin Keuangan
        </div>
    </nav>
</div>
