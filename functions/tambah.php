<?php
// Functions for adding new records
// This file contains all functions related to creating/adding new data

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/utils.php';

/**
 * Add new kas masuk (income) record
 * @param string $tanggal Date of transaction
 * @param string $event Event description
 * @param string $keterangan Transaction details
 * @param int $nominal Amount
 * @return array Result with success status and message
 */
function tambah_kas_masuk($tanggal, $event, $keterangan, $nominal) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if (empty($tanggal) || empty($event) || empty($keterangan) || $nominal <= 0) {
            return [
                'success' => false,
                'message' => 'Semua field harus diisi dan nominal harus lebih dari 0'
            ];
        }
        
        // Validate date format
        if (!validate_date($tanggal)) {
            return [
                'success' => false,
                'message' => 'Format tanggal tidak valid'
            ];
        }
        
        // Sanitize input
        $tanggal = sanitize_input($tanggal);
        $event = sanitize_input($event);
        $keterangan = sanitize_input($keterangan);
        $nominal = (int) $nominal;
        
        // Generate transaction ID
        $transaction_id = generate_transaction_id();
        
        // Prepare query
        $query = "INSERT INTO penerimaan_kas (transaction_id, Tanggal_Input, Event_WLE, Keterangan, Nominal, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssi", $transaction_id, $tanggal, $event, $keterangan, $nominal);
        
        if ($stmt->execute()) {
            $insert_id = $conn->insert_id;
            
            // Log activity
            log_activity('ADD_KAS_MASUK', "Menambah kas masuk: $keterangan - " . format_currency($nominal));
            
            return [
                'success' => true,
                'message' => 'Kas masuk berhasil ditambahkan',
                'id' => $insert_id,
                'transaction_id' => $transaction_id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error adding kas masuk: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menambah kas masuk'
        ];
    }
}

/**
 * Add new kas keluar (expense) record
 * @param string $tanggal Date of transaction
 * @param string $event Event description
 * @param string $keterangan Transaction details
 * @param string $nama_akun Account name
 * @param int $nominal Amount
 * @return array Result with success status and message
 */
function tambah_kas_keluar($tanggal, $event, $keterangan, $nama_akun, $nominal) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if (empty($tanggal) || empty($keterangan) || empty($nama_akun) || $nominal <= 0) {
            return [
                'success' => false,
                'message' => 'Semua field harus diisi dan nominal harus lebih dari 0'
            ];
        }
        
        // Validate date format
        if (!validate_date($tanggal)) {
            return [
                'success' => false,
                'message' => 'Format tanggal tidak valid'
            ];
        }
        
        // Sanitize input
        $tanggal = sanitize_input($tanggal);
        $event = sanitize_input($event);
        $keterangan = sanitize_input($keterangan);
        $nama_akun = sanitize_input($nama_akun);
        $nominal = (int) $nominal;
        
        // Generate transaction ID
        $transaction_id = generate_transaction_id();
        
        // Prepare query
        $query = "INSERT INTO pengeluaran_kas (transaction_id, Tanggal_Input, Event_WLE, Keterangan, Nama_Akun, Nominal, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sssssi", $transaction_id, $tanggal, $event, $keterangan, $nama_akun, $nominal);
        
        if ($stmt->execute()) {
            $insert_id = $conn->insert_id;
            
            // Log activity
            log_activity('ADD_KAS_KELUAR', "Menambah kas keluar: $keterangan - " . format_currency($nominal));
            
            return [
                'success' => true,
                'message' => 'Kas keluar berhasil ditambahkan',
                'id' => $insert_id,
                'transaction_id' => $transaction_id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error adding kas keluar: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menambah kas keluar'
        ];
    }
}

/**
 * Add new booking record
 * @param string $tanggal Date of booking
 * @param string $event Event name
 * @param string $paket Package type
 * @param string $client_name Client name (optional)
 * @param string $contact Contact information (optional)
 * @return array Result with success status and message
 */
function tambah_booking($tanggal, $event, $paket, $client_name = '', $contact = '') {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if (empty($tanggal) || empty($event) || empty($paket)) {
            return [
                'success' => false,
                'message' => 'Tanggal, event, dan paket harus diisi'
            ];
        }
        
        // Validate date format
        if (!validate_date($tanggal)) {
            return [
                'success' => false,
                'message' => 'Format tanggal tidak valid'
            ];
        }
        
        // Check if date is in the past
        if (strtotime($tanggal) < strtotime(date('Y-m-d'))) {
            return [
                'success' => false,
                'message' => 'Tanggal booking tidak boleh di masa lalu'
            ];
        }
        
        // Sanitize input
        $tanggal = sanitize_input($tanggal);
        $event = sanitize_input($event);
        $paket = sanitize_input($paket);
        $client_name = sanitize_input($client_name);
        $contact = sanitize_input($contact);
        
        // Generate booking ID
        $booking_id = 'BK' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        // Prepare query
        $query = "INSERT INTO jadwal_booking (booking_id, Tanggal, Event, Paket, client_name, contact, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssss", $booking_id, $tanggal, $event, $paket, $client_name, $contact);
        
        if ($stmt->execute()) {
            $insert_id = $conn->insert_id;
            
            // Log activity
            log_activity('ADD_BOOKING', "Menambah booking: $event - $paket pada " . format_date($tanggal));
            
            return [
                'success' => true,
                'message' => 'Booking berhasil ditambahkan',
                'id' => $insert_id,
                'booking_id' => $booking_id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error adding booking: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menambah booking'
        ];
    }
}

/**
 * Add new user account
 * @param string $username Username
 * @param string $email Email address
 * @param string $password Password
 * @param string $full_name Full name
 * @param string $role User role (default: 'user')
 * @return array Result with success status and message
 */
function tambah_user($username, $email, $password, $full_name, $role = 'user') {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            return [
                'success' => false,
                'message' => 'Semua field harus diisi'
            ];
        }
        
        // Validate email
        if (!validate_email($email)) {
            return [
                'success' => false,
                'message' => 'Format email tidak valid'
            ];
        }
        
        // Check if username or email already exists
        $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Username atau email sudah digunakan'
            ];
        }
        
        // Sanitize input
        $username = sanitize_input($username);
        $email = sanitize_input($email);
        $full_name = sanitize_input($full_name);
        $role = sanitize_input($role);
        
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare query
        $query = "INSERT INTO users (username, email, password_hash, full_name, role, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, 'active', NOW())";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sssss", $username, $email, $password_hash, $full_name, $role);
        
        if ($stmt->execute()) {
            $insert_id = $conn->insert_id;
            
            // Log activity
            log_activity('ADD_USER', "Menambah user baru: $username ($full_name)");
            
            return [
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'id' => $insert_id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error adding user: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menambah user'
        ];
    }
}

/**
 * Add new category
 * @param string $name Category name
 * @param string $description Category description
 * @param string $type Category type (income/expense)
 * @return array Result with success status and message
 */
function tambah_kategori($name, $description = '', $type = 'expense') {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if (empty($name)) {
            return [
                'success' => false,
                'message' => 'Nama kategori harus diisi'
            ];
        }
        
        // Check if category already exists
        $check_query = "SELECT id FROM categories WHERE name = ? AND type = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $name, $type);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Kategori dengan nama tersebut sudah ada'
            ];
        }
        
        // Sanitize input
        $name = sanitize_input($name);
        $description = sanitize_input($description);
        $type = sanitize_input($type);
        
        // Prepare query
        $query = "INSERT INTO categories (name, description, type, status, created_at) 
                  VALUES (?, ?, ?, 'active', NOW())";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sss", $name, $description, $type);
        
        if ($stmt->execute()) {
            $insert_id = $conn->insert_id;
            
            // Log activity
            log_activity('ADD_CATEGORY', "Menambah kategori baru: $name ($type)");
            
            return [
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'id' => $insert_id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error adding category: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menambah kategori'
        ];
    }
}
?>