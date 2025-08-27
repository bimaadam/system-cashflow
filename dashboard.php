<?php
// DEBUG: Enable error reporting temporarily (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'function.php';
require 'cek.php';
require 'auth_check.php';

// Enforce role-based access
enforce_role_access();

// Enforce login for this dashboard page
require_login();

// Determine which tab to show
$tab = $_GET['tab'] ?? 'dashboard';
$title = 'Dashboard - Admin Dekorasi';

// Set page title based on tab
switch ($tab) {
    case 'dashboard':
        $title = 'Dashboard - Admin Dekorasi';
        break;
    case 'kas_masuk':
        $title = 'Kas Masuk - Admin Dekorasi';
        break;
    case 'kas_keluar':
        $title = 'Kas Keluar - Admin Dekorasi';
        break;
    case 'booking':
        $title = 'Jadwal Booking - Admin Dekorasi';
        break;
    case 'laporan':
        $title = 'Laporan Keuangan - Admin Dekorasi';
        break;
    default:
        $title = 'Dashboard - Admin Dekorasi';
        $tab = 'dashboard';
}

// Additional scripts for specific pages
$scripts_extra = '';
if (in_array($tab, ['kas_masuk', 'kas_keluar', 'booking'])) {
    $scripts_extra = '<script src="js/modals.js"></script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= htmlspecialchars($title) ?></title>
    <link href="" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <?php require 'layout/navbar.php'; ?>
    <div id="layoutSidenav">
        <?php require 'layout/sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main class="container-fluid px-4 mt-4">
                <?php
                // Include the appropriate view based on tab
                $view_file = "views/{$tab}.php";
                if (file_exists($view_file)) {
                    include $view_file;
                } else {
                    include 'views/galeri.php'; // Default fallback
                }
                ?>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid text-center">
                    <small>&copy; <?= date('Y') ?> Admin Dekorasi</small>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/dist/js/bootstrap.bundle.js"></script>
    <script src="js/scripts.js"></script>
    <?= $scripts_extra ?>
</body>

</html>