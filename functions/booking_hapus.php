<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        mysqli_query($conn, "DELETE FROM jadwal_booking WHERE id = $id");
    }
    $base_url = dirname($_SERVER['SCRIPT_NAME'], 2);

    $redirect = $_GET['redirect'] ?? '';
    if (!empty($redirect)) {
        header("Location: $base_url/$redirect");
    } else {
        header("Location: $base_url/dashboard.php?tab=booking&deleted=1");
    } 
    exit;

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    if ($id > 0) {
        mysqli_query($conn, "DELETE FROM jadwal_booking WHERE id = $id");
        echo json_encode(['success' => true]);
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}

header('Location: ../../dashboard.php?tab=booking');
exit;


