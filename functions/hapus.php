<?php
// Functions for deleting records
// This file contains all functions related to deleting data

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/utils.php';

/**
 * Delete kas masuk (income) record
 * @param int $id Record ID to delete
 * @return array Result with success status and message
 */
function hapus_kas_masuk($id) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0) {
            return [
                'success' => false,
                'message' => 'ID tidak valid'
            ];
        }
        
        // Check if record exists and get details for logging
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
        
        $data = $result->fetch_assoc();
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Soft delete - mark as deleted instead of actually deleting
            $delete_query = "UPDATE penerimaan_kas SET deleted_at = NOW() WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            
            if (!$delete_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $delete_stmt->bind_param("i", $id);
            
            if (!$delete_stmt->execute()) {
                throw new Exception("Execute failed: " . $delete_stmt->error);
            }
            
            // Log activity
            log_activity('DELETE_KAS_MASUK', "Menghapus kas masuk ID $id: {$data['Keterangan']} - " . format_currency($data['Nominal']));
            
            // Commit transaction
            $conn->commit();
            
            return [
                'success' => true,
                'message' => 'Kas masuk berhasil dihapus',
                'id' => $id
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        handle_error("Error deleting kas masuk: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghapus kas masuk'
        ];
    }
}

/**
 * Delete kas keluar (expense) record
 * @param int $id Record ID to delete
 * @return array Result with success status and message
 */
function hapus_kas_keluar($id) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0) {
            return [
                'success' => false,
                'message' => 'ID tidak valid'
            ];
        }
        
        // Check if record exists and get details for logging
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
        
        $data = $result->fetch_assoc();
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Soft delete - mark as deleted instead of actually deleting
            $delete_query = "UPDATE pengeluaran_kas SET deleted_at = NOW() WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            
            if (!$delete_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $delete_stmt->bind_param("i", $id);
            
            if (!$delete_stmt->execute()) {
                throw new Exception("Execute failed: " . $delete_stmt->error);
            }
            
            // Log activity
            log_activity('DELETE_KAS_KELUAR', "Menghapus kas keluar ID $id: {$data['Keterangan']} - " . format_currency($data['Nominal']));
            
            // Commit transaction
            $conn->commit();
            
            return [
                'success' => true,
                'message' => 'Kas keluar berhasil dihapus',
                'id' => $id
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        handle_error("Error deleting kas keluar: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghapus kas keluar'
        ];
    }
}

/**
 * Delete booking record
 * @param int $id Record ID to delete
 * @return array Result with success status and message
 */
function hapus_booking($id) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0) {
            return [
                'success' => false,
                'message' => 'ID tidak valid'
            ];
        }
        
        // Check if record exists and get details for logging
        $check_query = "SELECT id, Event, Tanggal, status FROM jadwal_booking WHERE id = ?";
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
        
        $data = $result->fetch_assoc();
        
        // Check if booking is completed (might want to prevent deletion)
        if ($data['status'] === 'completed') {
            return [
                'success' => false,
                'message' => 'Booking yang sudah selesai tidak dapat dihapus'
            ];
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Soft delete - mark as deleted instead of actually deleting
            $delete_query = "UPDATE jadwal_booking SET deleted_at = NOW() WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            
            if (!$delete_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $delete_stmt->bind_param("i", $id);
            
            if (!$delete_stmt->execute()) {
                throw new Exception("Execute failed: " . $delete_stmt->error);
            }
            
            // Log activity
            log_activity('DELETE_BOOKING', "Menghapus booking ID $id: {$data['Event']} pada " . format_date($data['Tanggal']));
            
            // Commit transaction
            $conn->commit();
            
            return [
                'success' => true,
                'message' => 'Booking berhasil dihapus',
                'id' => $id
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        handle_error("Error deleting booking: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghapus booking'
        ];
    }
}

/**
 * Delete user record
 * @param int $id User ID to delete
 * @return array Result with success status and message
 */
function hapus_user($id) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0) {
            return [
                'success' => false,
                'message' => 'ID tidak valid'
            ];
        }
        
        // Prevent self-deletion
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
            return [
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun sendiri'
            ];
        }
        
        // Check if record exists and get details for logging
        $check_query = "SELECT id, username, full_name, role FROM users WHERE id = ?";
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
        
        $data = $result->fetch_assoc();
        
        // Prevent deletion of admin if it's the last admin
        if ($data['role'] === 'admin') {
            $admin_count_query = "SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND deleted_at IS NULL";
            $admin_count_result = $conn->query($admin_count_query);
            $admin_count = $admin_count_result->fetch_assoc()['count'];
            
            if ($admin_count <= 1) {
                return [
                    'success' => false,
                    'message' => 'Tidak dapat menghapus admin terakhir'
                ];
            }
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Soft delete - mark as deleted instead of actually deleting
            $delete_query = "UPDATE users SET deleted_at = NOW() WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            
            if (!$delete_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $delete_stmt->bind_param("i", $id);
            
            if (!$delete_stmt->execute()) {
                throw new Exception("Execute failed: " . $delete_stmt->error);
            }
            
            // Log activity
            log_activity('DELETE_USER', "Menghapus user ID $id: {$data['username']} ({$data['full_name']})");
            
            // Commit transaction
            $conn->commit();
            
            return [
                'success' => true,
                'message' => 'User berhasil dihapus',
                'id' => $id
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        handle_error("Error deleting user: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghapus user'
        ];
    }
}

/**
 * Delete category record
 * @param int $id Category ID to delete
 * @return array Result with success status and message
 */
function hapus_kategori($id) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0) {
            return [
                'success' => false,
                'message' => 'ID tidak valid'
            ];
        }
        
        // Check if record exists and get details for logging
        $check_query = "SELECT id, name, type FROM categories WHERE id = ?";
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
        
        $data = $result->fetch_assoc();
        
        // Check if category is being used (this would require additional tables)
        // For now, we'll allow deletion but in a real scenario you might want to check dependencies
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Soft delete - mark as deleted instead of actually deleting
            $delete_query = "UPDATE categories SET deleted_at = NOW() WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            
            if (!$delete_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $delete_stmt->bind_param("i", $id);
            
            if (!$delete_stmt->execute()) {
                throw new Exception("Execute failed: " . $delete_stmt->error);
            }
            
            // Log activity
            log_activity('DELETE_CATEGORY', "Menghapus kategori ID $id: {$data['name']} ({$data['type']})");
            
            // Commit transaction
            $conn->commit();
            
            return [
                'success' => true,
                'message' => 'Kategori berhasil dihapus',
                'id' => $id
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        handle_error("Error deleting category: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghapus kategori'
        ];
    }
}

/**
 * Permanently delete (hard delete) kas masuk record
 * @param int $id Record ID to permanently delete
 * @return array Result with success status and message
 */
function hapus_permanen_kas_masuk($id) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0) {
            return [
                'success' => false,
                'message' => 'ID tidak valid'
            ];
        }
        
        // Check if record exists and is already soft deleted
        $check_query = "SELECT id, Keterangan, Nominal FROM penerimaan_kas WHERE id = ? AND deleted_at IS NOT NULL";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Data kas masuk tidak ditemukan atau belum dihapus'
            ];
        }
        
        $data = $result->fetch_assoc();
        
        // Permanently delete
        $delete_query = "DELETE FROM penerimaan_kas WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        
        if (!$delete_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            // Log activity
            log_activity('PERMANENT_DELETE_KAS_MASUK', "Menghapus permanen kas masuk ID $id: {$data['Keterangan']}");
            
            return [
                'success' => true,
                'message' => 'Kas masuk berhasil dihapus permanen',
                'id' => $id
            ];
        } else {
            throw new Exception("Execute failed: " . $delete_stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error permanently deleting kas masuk: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghapus permanen kas masuk'
        ];
    }
}

/**
 * Restore soft-deleted record
 * @param string $table Table name (penerimaan_kas, pengeluaran_kas, jadwal_booking, etc.)
 * @param int $id Record ID to restore
 * @return array Result with success status and message
 */
function restore_record($table, $id) {
    $conn = get_db_connection();
    
    try {
        // Validate input
        if ($id <= 0 || empty($table)) {
            return [
                'success' => false,
                'message' => 'Parameter tidak valid'
            ];
        }
        
        // Validate table name (security check)
        $allowed_tables = ['penerimaan_kas', 'pengeluaran_kas', 'jadwal_booking', 'users', 'categories'];
        if (!in_array($table, $allowed_tables)) {
            return [
                'success' => false,
                'message' => 'Tabel tidak valid'
            ];
        }
        
        // Check if record exists and is soft deleted
        $check_query = "SELECT id FROM $table WHERE id = ? AND deleted_at IS NOT NULL";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Data tidak ditemukan atau belum dihapus'
            ];
        }
        
        // Restore record
        $restore_query = "UPDATE $table SET deleted_at = NULL WHERE id = ?";
        $restore_stmt = $conn->prepare($restore_query);
        
        if (!$restore_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $restore_stmt->bind_param("i", $id);
        
        if ($restore_stmt->execute()) {
            // Log activity
            log_activity('RESTORE_RECORD', "Memulihkan data dari tabel $table ID $id");
            
            return [
                'success' => true,
                'message' => 'Data berhasil dipulihkan',
                'id' => $id
            ];
        } else {
            throw new Exception("Execute failed: " . $restore_stmt->error);
        }
        
    } catch (Exception $e) {
        handle_error("Error restoring record: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat memulihkan data'
        ];
    }
}
?>