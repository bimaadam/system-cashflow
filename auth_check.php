<?php
// Role-based access control
function is_allowed($role, $page) {
    $permissions = [
        'admin' => ['dashboard', 'kas_masuk', 'kas_keluar', 'booking', 'laporan'],
        'owner' => ['dashboard', 'booking', 'laporan']
    ];

    // Grant access if the role has permissions for the page
    if (isset($permissions[$role]) && in_array($page, $permissions[$role])) {
        return true;
    }

    return false;
}

// Check user role and redirect if not allowed
function enforce_role_access() {
    if (!isset($_SESSION['user_role'])) {
        header('Location: Login.php');
        exit();
    }

    $role = $_SESSION['user_role'];
    $current_page = $_GET['tab'] ?? 'dashboard';

    if (!is_allowed($role, $current_page)) {
        // Redirect to a default page or show an error
        header('Location: dashboard.php?tab=booking'); // Or an error page
        exit();
    }
}
?>