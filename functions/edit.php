<?php


require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/utils.php';

/**
 * 
 * @param int 
 * @param string 
 * @param string 
 * @param string 
 * @param int 
 * @return array 
 */
function edit_kas_masuk($id, $tanggal, $event, $keterangan, $nominal) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0 || empty($tanggal) || empty($event) || empty($keterangan) || $nominal <= 0) {
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
        
        // Check if record exists
        $check_query = "SELECT id, Keterangan, Nominal FROM penerimaan_kas WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Data kas masuk tidak ditemukan'
            ];
        }
        
        $old_data = $result->fetch_assoc();
        
        // Sanitize input
        $id = (int) $id;
        $tanggal = sanitize_input($tanggal);
        $event = sanitize_input($event);
        $keterangan = sanitize_input($keterangan);
        $nominal = (int) $nominal;
        
        // Prepare query
        $query = "UPDATE penerimaan_kas 
                  SET Tanggal_Input = ?, Event_WLE = ?, Keterangan = ?, Nominal = ?, updated_at = NOW() 
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sssii", $tanggal, $event, $keterangan, $nominal, $id);
        
        if ($stmt->execute()) {
            // Log activity
            log_activity('EDIT_KAS_MASUK', "Mengubah kas masuk ID $id: {$old_data['Keterangan']} -> $keterangan");
            
            return [
                'success' => true,
                'message' => 'Kas masuk berhasil diupdate',
                'id' => $id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error updating kas masuk: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengupdate kas masuk'
        ];
    }
}

/**
 * Update kas keluar (expense) record
 * @param int $id Record ID
 * @param string $tanggal Date of transaction
 * @param string $event Event description
 * @param string $keterangan Transaction details
 * @param string $nama_akun Account name
 * @param int $nominal Amount
 * @return array Result with success status and message
 */
function edit_kas_keluar($id, $tanggal, $event, $keterangan, $nama_akun, $nominal) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0 || empty($tanggal) || empty($keterangan) || empty($nama_akun) || $nominal <= 0) {
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
        
        // Check if record exists
        $check_query = "SELECT id, Keterangan, Nominal FROM pengeluaran_kas WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Data kas keluar tidak ditemukan'
            ];
        }
        
        $old_data = $result->fetch_assoc();
        
        // Sanitize input
        $id = (int) $id;
        $tanggal = sanitize_input($tanggal);
        $event = sanitize_input($event);
        $keterangan = sanitize_input($keterangan);
        $nama_akun = sanitize_input($nama_akun);
        $nominal = (int) $nominal;
        
        // Prepare query
        $query = "UPDATE pengeluaran_kas 
                  SET Tanggal_Input = ?, Event_WLE = ?, Keterangan = ?, Nama_Akun = ?, Nominal = ?, updated_at = NOW() 
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssii", $tanggal, $event, $keterangan, $nama_akun, $nominal, $id);
        
        if ($stmt->execute()) {
            // Log activity
            log_activity('EDIT_KAS_KELUAR', "Mengubah kas keluar ID $id: {$old_data['Keterangan']} -> $keterangan");
            
            return [
                'success' => true,
                'message' => 'Kas keluar berhasil diupdate',
                'id' => $id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error updating kas keluar: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengupdate kas keluar'
        ];
    }
}

/**
 * Update booking record
 * @param int $id Record ID
 * @param string $tanggal Date of booking
 * @param string $event Event name
 * @param string $paket Package type
 * @param string $client_name Client name
 * @param string $contact Contact information
 * @param string $status Booking status
 * @return array Result with success status and message
 */
function edit_booking($id, $tanggal, $event, $paket, $client_name = '', $contact = '', $status = 'pending') {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0 || empty($tanggal) || empty($event) || empty($paket)) {
            return [
                'success' => false,
                'message' => 'ID, tanggal, event, dan paket harus diisi'
            ];
        }
        
        // Validate date format
        if (!validate_date($tanggal)) {
            return [
                'success' => false,
                'message' => 'Format tanggal tidak valid'
            ];
        }
        
        // Check if record exists
        $check_query = "SELECT id, Event, Paket FROM jadwal_booking WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Data booking tidak ditemukan'
            ];
        }
        
        $old_data = $result->fetch_assoc();
        
        // Sanitize input
        $id = (int) $id;
        $tanggal = sanitize_input($tanggal);
        $event = sanitize_input($event);
        $paket = sanitize_input($paket);
        $client_name = sanitize_input($client_name);
        $contact = sanitize_input($contact);
        $status = sanitize_input($status);
        
        // Validate status
        $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            $status = 'pending';
        }
        
        // Prepare query
        $query = "UPDATE jadwal_booking 
                  SET Tanggal = ?, Event = ?, Paket = ?, client_name = ?, contact = ?, status = ?, updated_at = NOW() 
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssssi", $tanggal, $event, $paket, $client_name, $contact, $status, $id);
        
        if ($stmt->execute()) {
            // Log activity
            log_activity('EDIT_BOOKING', "Mengubah booking ID $id: {$old_data['Event']} -> $event");
            
            return [
                'success' => true,
                'message' => 'Booking berhasil diupdate',
                'id' => $id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error updating booking: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengupdate booking'
        ];
    }
}

/**
 * Update user record
 * @param int $id User ID
 * @param string $username Username
 * @param string $email Email address
 * @param string $full_name Full name
 * @param string $role User role
 * @param string $status User status
 * @param string $password New password (optional)
 * @return array Result with success status and message
 */
function edit_user($id, $username, $email, $full_name, $role = 'user', $status = 'active', $password = '') {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0 || empty($username) || empty($email) || empty($full_name)) {
            return [
                'success' => false,
                'message' => 'ID, username, email, dan nama lengkap harus diisi'
            ];
        }
        
        // Validate email
        if (!validate_email($email)) {
            return [
                'success' => false,
                'message' => 'Format email tidak valid'
            ];
        }
        
        // Check if record exists
        $check_query = "SELECT id, username, email FROM users WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'User tidak ditemukan'
            ];
        }
        
        $old_data = $result->fetch_assoc();
        
        // Check if username or email already exists (excluding current user)
        $duplicate_query = "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?";
        $duplicate_stmt = $conn->prepare($duplicate_query);
        $duplicate_stmt->bind_param("ssi", $username, $email, $id);
        $duplicate_stmt->execute();
        $duplicate_result = $duplicate_stmt->get_result();
        
        if ($duplicate_result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Username atau email sudah digunakan oleh user lain'
            ];
        }
        
        // Sanitize input
        $id = (int) $id;
        $username = sanitize_input($username);
        $email = sanitize_input($email);
        $full_name = sanitize_input($full_name);
        $role = sanitize_input($role);
        $status = sanitize_input($status);
        
        // Prepare query based on whether password is being updated
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE users 
                      SET username = ?, email = ?, full_name = ?, role = ?, status = ?, password_hash = ?, updated_at = NOW() 
                      WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssi", $username, $email, $full_name, $role, $status, $password_hash, $id);
        } else {
            $query = "UPDATE users 
                      SET username = ?, email = ?, full_name = ?, role = ?, status = ?, updated_at = NOW() 
                      WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssi", $username, $email, $full_name, $role, $status, $id);
        }
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if ($stmt->execute()) {
            // Log activity
            $password_changed = !empty($password) ? ' (password diubah)' : '';
            log_activity('EDIT_USER', "Mengubah user ID $id: {$old_data['username']} -> $username" . $password_changed);
            
            return [
                'success' => true,
                'message' => 'User berhasil diupdate',
                'id' => $id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error updating user: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengupdate user'
        ];
    }
}

/**
 * Update category record
 * @param int $id Category ID
 * @param string $name Category name
 * @param string $description Category description
 * @param string $type Category type
 * @param string $status Category status
 * @return array Result with success status and message
 */
function edit_kategori($id, $name, $description = '', $type = 'expense', $status = 'active') {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0 || empty($name)) {
            return [
                'success' => false,
                'message' => 'ID dan nama kategori harus diisi'
            ];
        }
        
        // Check if record exists
        $check_query = "SELECT id, name FROM categories WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ];
        }
        
        $old_data = $result->fetch_assoc();
        
        // Check if category name already exists (excluding current category)
        $duplicate_query = "SELECT id FROM categories WHERE name = ? AND type = ? AND id != ?";
        $duplicate_stmt = $conn->prepare($duplicate_query);
        $duplicate_stmt->bind_param("ssi", $name, $type, $id);
        $duplicate_stmt->execute();
        $duplicate_result = $duplicate_stmt->get_result();
        
        if ($duplicate_result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Kategori dengan nama tersebut sudah ada'
            ];
        }
        
        // Sanitize input
        $id = (int) $id;
        $name = sanitize_input($name);
        $description = sanitize_input($description);
        $type = sanitize_input($type);
        $status = sanitize_input($status);
        
        // Prepare query
        $query = "UPDATE categories 
                  SET name = ?, description = ?, type = ?, status = ?, updated_at = NOW() 
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssi", $name, $description, $type, $status, $id);
        
        if ($stmt->execute()) {
            // Log activity
            log_activity('EDIT_CATEGORY', "Mengubah kategori ID $id: {$old_data['name']} -> $name");
            
            return [
                'success' => true,
                'message' => 'Kategori berhasil diupdate',
                'id' => $id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error updating category: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengupdate kategori'
        ];
    }
}

/**
 * Update booking status
 * @param int $id Booking ID
 * @param string $status New status
 * @return array Result with success status and message
 */
function edit_booking_status($id, $status) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0 || empty($status)) {
            return [
                'success' => false,
                'message' => 'ID dan status harus diisi'
            ];
        }
        
        // Validate status
        $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            return [
                'success' => false,
                'message' => 'Status tidak valid'
            ];
        }
        
        // Check if record exists
        $check_query = "SELECT id, Event, status FROM jadwal_booking WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ];
        }
        
        $old_data = $result->fetch_assoc();
        
        // Sanitize input
        $id = (int) $id;
        $status = sanitize_input($status);
        
        // Prepare query
        $query = "UPDATE jadwal_booking SET status = ?, updated_at = NOW() WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            // Log activity
            log_activity('EDIT_BOOKING_STATUS', "Mengubah status booking ID $id: {$old_data['status']} -> $status");
            
            return [
                'success' => true,
                'message' => 'Status booking berhasil diupdate',
                'id' => $id
            ];
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error updating booking status: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengupdate status booking'
        ];
    }
}
?>