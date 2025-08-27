<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default timezone
date_default_timezone_set('Asia/Jakarta');

// Database configuration
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "cashflow";

// Create database connection with error handling
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Function to format currency
function format_currency($amount) {
    return number_format($amount, 0, ',', '.');
}

// Function to format date
function format_date($date) {
    return date('d/m/Y', strtotime($date));
}

// Function to check if user is logged in
function check_login() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header("Location: login.php");
        exit();
    }
}

// Function to add kas masuk (income)
function add_kas_masuk($tanggal, $event, $keterangan, $nominal) {
    global $conn;
    
    $tanggal = sanitize_input($tanggal);
    $event = sanitize_input($event);
    $keterangan = sanitize_input($keterangan);
    $nominal = (int) $nominal;
    
    if (empty($tanggal) || empty($event) || empty($keterangan) || $nominal <= 0) {
        return false;
    }
    
    $query = "INSERT INTO penerimaan_kas (Tanggal_Input, Event_WLE, Keterangan, Nominal) 
              VALUES (?, ?, ?, ?)";

    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $tanggal, $event, $keterangan, $nominal);
    
    return $stmt->execute();
}

// Function to add kas keluar (expense)
function add_kas_keluar($tanggal, $event, $keterangan, $nama_akun, $nominal) {
    global $conn;
    
    $tanggal = sanitize_input($tanggal);
    $event = sanitize_input($event);
    $keterangan = sanitize_input($keterangan);
    $nama_akun = sanitize_input($nama_akun);
    $nominal = (int) $nominal;
    
    if (empty($tanggal) || empty($keterangan) || empty($nama_akun) || $nominal <= 0) {
        return false;
    }
    
    $query = "INSERT INTO pengeluaran_kas (Tanggal_Input, Event_WLE, Keterangan, Nama_Akun, Nominal) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $tanggal, $event, $keterangan, $nama_akun, $nominal);
    
    return $stmt->execute();
}

// Function to add booking
function add_booking($tanggal, $event, $paket) {
    global $conn;
    
    $tanggal = sanitize_input($tanggal);
    $event = sanitize_input($event);
    $paket = sanitize_input($paket);
    
    if (empty($tanggal) || empty($event) || empty($paket)) {
        return false;
    }
    
    $query = "INSERT INTO jadwal_booking (Tanggal, Event, Paket) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $tanggal, $event, $paket);
    
    return $stmt->execute();
}

// Function to update kas masuk
function update_kas_masuk($id, $tanggal, $event, $keterangan, $nominal) {
    global $conn;
    
    $id = (int) $id;
    $tanggal = sanitize_input($tanggal);
    $event = sanitize_input($event);
    $keterangan = sanitize_input($keterangan);
    $nominal = (int) $nominal;
    
    if ($id <= 0 || empty($tanggal) || empty($event) || empty($keterangan) || $nominal <= 0) {
        return false;
    }
    
    $query = "UPDATE penerimaan_kas 
              SET Tanggal_Input = ?, Event_WLE = ?, Keterangan = ?, Nominal = ? 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssii", $tanggal, $event, $keterangan, $nominal, $id);
    
    return $stmt->execute();
}

// Function to update kas keluar
function update_kas_keluar($id, $tanggal, $event, $keterangan, $nama_akun, $nominal) {
    global $conn;
    
    $id = (int) $id;
    $tanggal = sanitize_input($tanggal);
    $event = sanitize_input($event);
    $keterangan = sanitize_input($keterangan);
    $nama_akun = sanitize_input($nama_akun);
    $nominal = (int) $nominal;
    
    if ($id <= 0 || empty($tanggal) || empty($keterangan) || empty($nama_akun) || $nominal <= 0) {
        return false;
    }
    
    $query = "UPDATE pengeluaran_kas 
              SET Tanggal_Input = ?, Event_WLE = ?, Keterangan = ?, Nama_Akun = ?, Nominal = ? 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssii", $tanggal, $event, $keterangan, $nama_akun, $nominal, $id);
    
    return $stmt->execute();
}

// Function to update booking
function update_booking($id, $tanggal, $event, $paket) {
    global $conn;
    
    $id = (int) $id;
    $tanggal = sanitize_input($tanggal);
    $event = sanitize_input($event);
    $paket = sanitize_input($paket);
    
    if ($id <= 0 || empty($tanggal) || empty($event) || empty($paket)) {
        return false;
    }
    
    $query = "UPDATE jadwal_booking SET Tanggal = ?, Event = ?, Paket = ? WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $tanggal, $event, $paket, $id);
    
    return $stmt->execute();
}

// Function to delete kas masuk
function delete_kas_masuk($id) {
    global $conn;
    
    $id = (int) $id;
    if ($id <= 0) return false;
    
    $query = "DELETE FROM penerimaan_kas WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

// Function to delete kas keluar
function delete_kas_keluar($id) {
    global $conn;
    
    $id = (int) $id;
    if ($id <= 0) return false;
    
    $query = "DELETE FROM pengeluaran_kas WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

// Function to delete booking
function delete_booking($id) {
    global $conn;
    
    $id = (int) $id;
    if ($id <= 0) return false;
    
    $query = "DELETE FROM jadwal_booking WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

// Function to get total kas masuk
function get_total_kas_masuk($month = null, $year = null) {
    global $conn;
    
    $query = "SELECT SUM(Nominal) as total FROM penerimaan_kas";
    $params = [];
    $types = "";
    
    if ($month && $year) {
        $query .= " WHERE MONTH(Tanggal_Input) = ? AND YEAR(Tanggal_Input) = ?";
        $params = [$month, $year];
        $types = "ii";
    } elseif ($year) {
        $query .= " WHERE YEAR(Tanggal_Input) = ?";
        $params = [$year];
        $types = "i";
    }
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'] ?? 0;
}

// Function to get total kas keluar
function get_total_kas_keluar($month = null, $year = null) {
    global $conn;
    
    $query = "SELECT SUM(Nominal) as total FROM pengeluaran_kas";
    $params = [];
    $types = "";
    
    if ($month && $year) {
        $query .= " WHERE MONTH(Tanggal_Input) = ? AND YEAR(Tanggal_Input) = ?";
        $params = [$month, $year];
        $types = "ii";
    } elseif ($year) {
        $query .= " WHERE YEAR(Tanggal_Input) = ?";
        $params = [$year];
        $types = "i";
    }
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'] ?? 0;
}

// Function to get total bookings
function get_total_bookings() {
    global $conn;
    
    $query = "SELECT COUNT(*) as total FROM jadwal_booking";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    
    return $row['total'] ?? 0;
}

// Function to get kas masuk data
function get_kas_masuk_data($limit = null, $offset = null) {
    global $conn;
    
    $query = "SELECT * FROM penerimaan_kas ORDER BY Tanggal_Input DESC";
    
    if ($limit) {
        $query .= " LIMIT " . (int)$limit;
        if ($offset) {
            $query .= " OFFSET " . (int)$offset;
        }
    }
    
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get kas keluar data
function get_kas_keluar_data($limit = null, $offset = null) {
    global $conn;
    
    $query = "SELECT * FROM pengeluaran_kas ORDER BY Tanggal_Input DESC";
    
    if ($limit) {
        $query .= " LIMIT " . (int)$limit;
        if ($offset) {
            $query .= " OFFSET " . (int)$offset;
        }
    }
    
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get booking data
function get_booking_data($limit = null, $offset = null) {
    global $conn;
    
    $query = "SELECT * FROM jadwal_booking ORDER BY Tanggal ASC";
    
    if ($limit) {
        $query .= " LIMIT " . (int)$limit;
        if ($offset) {
            $query .= " OFFSET " . (int)$offset;
        }
    }
    
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get combined financial report
function get_financial_report($month = null, $year = null) {
    global $conn;
    
    $where_clause = "";
    $params = [];
    $types = "";
    
    if ($month && $year) {
        $where_clause = "WHERE MONTH(Tanggal_Input) = ? AND YEAR(Tanggal_Input) = ?";
        $params = [$month, $year, $month, $year];
        $types = "iiii";
    } elseif ($year) {
        $where_clause = "WHERE YEAR(Tanggal_Input) = ?";
        $params = [$year, $year];
        $types = "ii";
    }
    
    $query = "
        SELECT Tanggal_Input, 'Kas Masuk' AS Jenis, Event_WLE, Keterangan, Nominal, 'success' as class
        FROM penerimaan_kas 
        $where_clause
        UNION ALL
        SELECT Tanggal_Input, 'Kas Keluar' AS Jenis, Event_WLE, Keterangan, Nominal, 'danger' as class
        FROM pengeluaran_kas 
        $where_clause
        ORDER BY Tanggal_Input DESC
    ";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to log activity (optional feature)
function log_activity($action, $description) {
    global $conn;
    
    $action = sanitize_input($action);
    $description = sanitize_input($description);
    $user_id = $_SESSION['user_id'] ?? 1;
    $timestamp = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO activity_log (user_id, action, description, timestamp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user_id, $action, $description, $timestamp);
    
    return $stmt->execute();
}

// Function to validate date
function validate_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

// Function to get month name in Indonesian
function get_month_name($month_number) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    return $months[(int)$month_number] ?? '';
}

// Function to generate unique transaction ID
function generate_transaction_id() {
    return 'TXN' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

// Error handling and logging
function handle_error($error_message) {
    error_log($error_message);
    if (defined('DEBUG') && DEBUG) {
        echo "Error: " . $error_message;
    }
}

// Close connection when script ends
register_shutdown_function(function() {
    global $conn;
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
});
?>