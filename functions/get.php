<?php
// Functions for retrieving/getting data
// This file contains all functions related to fetching data from database

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/utils.php';

/**
 * Get kas masuk (income) data with filtering and pagination
 * @param array $filters Filtering options
 * @param int $limit Limit number of results
 * @param int $offset Offset for pagination
 * @return array Result with data and metadata
 */
function get_kas_masuk($filters = [], $limit = null, $offset = null) {
    $conn = get_db_connection();
    
    try {
        // Base query
        $query = "SELECT * FROM penerimaan_kas WHERE deleted_at IS NULL";
        $count_query = "SELECT COUNT(*) as total FROM penerimaan_kas WHERE deleted_at IS NULL";
        
        $params = [];
        $types = "";
        $where_conditions = [];
        
        // Apply filters
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $where_conditions[] = "MONTH(Tanggal_Input) = ? AND YEAR(Tanggal_Input) = ?";
            $params[] = (int)$filters['month'];
            $params[] = (int)$filters['year'];
            $types .= "ii";
        } elseif (!empty($filters['year'])) {
            $where_conditions[] = "YEAR(Tanggal_Input) = ?";
            $params[] = (int)$filters['year'];
            $types .= "i";
        }
        
        if (!empty($filters['event'])) {
            $where_conditions[] = "Event_WLE LIKE ?";
            $params[] = "%" . $filters['event'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['date_from'])) {
            $where_conditions[] = "Tanggal_Input >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }
        
        if (!empty($filters['date_to'])) {
            $where_conditions[] = "Tanggal_Input <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }
        
        if (!empty($filters['min_amount'])) {
            $where_conditions[] = "Nominal >= ?";
            $params[] = (int)$filters['min_amount'];
            $types .= "i";
        }
        
        if (!empty($filters['max_amount'])) {
            $where_conditions[] = "Nominal <= ?";
            $params[] = (int)$filters['max_amount'];
            $types .= "i";
        }
        
        // Add where conditions to queries
        if (!empty($where_conditions)) {
            $where_clause = " AND " . implode(" AND ", $where_conditions);
            $query .= $where_clause;
            $count_query .= $where_clause;
        }
        
        // Add sorting
        $query .= " ORDER BY Tanggal_Input DESC, created_at DESC";
        
        // Add pagination
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
            if ($offset) {
                $query .= " OFFSET " . (int)$offset;
            }
        }
        
        // Get total count
        $count_stmt = $conn->prepare($count_query);
        if (!empty($params)) {
            $count_stmt->bind_param($types, ...$params);
        }
        $count_stmt->execute();
        $total_result = $count_stmt->get_result();
        $total = $total_result->fetch_assoc()['total'];
        
        // Get data
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'data' => $data,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ];
        
    } catch (Exception $e) {
        handle_error("Error getting kas masuk data: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data kas masuk',
            'data' => [],
            'total' => 0
        ];
    }
}

/**
 * Get kas keluar (expense) data with filtering and pagination
 * @param array $filters Filtering options
 * @param int $limit Limit number of results
 * @param int $offset Offset for pagination
 * @return array Result with data and metadata
 */
function get_kas_keluar($filters = [], $limit = null, $offset = null) {
    $conn = get_db_connection();
    
    try {
        // Base query
        $query = "SELECT * FROM pengeluaran_kas WHERE deleted_at IS NULL";
        $count_query = "SELECT COUNT(*) as total FROM pengeluaran_kas WHERE deleted_at IS NULL";
        
        $params = [];
        $types = "";
        $where_conditions = [];
        
        // Apply filters (similar to kas masuk)
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $where_conditions[] = "MONTH(Tanggal_Input) = ? AND YEAR(Tanggal_Input) = ?";
            $params[] = (int)$filters['month'];
            $params[] = (int)$filters['year'];
            $types .= "ii";
        } elseif (!empty($filters['year'])) {
            $where_conditions[] = "YEAR(Tanggal_Input) = ?";
            $params[] = (int)$filters['year'];
            $types .= "i";
        }
        
        if (!empty($filters['event'])) {
            $where_conditions[] = "Event_WLE LIKE ?";
            $params[] = "%" . $filters['event'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['account'])) {
            $where_conditions[] = "Nama_Akun LIKE ?";
            $params[] = "%" . $filters['account'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['date_from'])) {
            $where_conditions[] = "Tanggal_Input >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }
        
        if (!empty($filters['date_to'])) {
            $where_conditions[] = "Tanggal_Input <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }
        
        if (!empty($filters['min_amount'])) {
            $where_conditions[] = "Nominal >= ?";
            $params[] = (int)$filters['min_amount'];
            $types .= "i";
        }
        
        if (!empty($filters['max_amount'])) {
            $where_conditions[] = "Nominal <= ?";
            $params[] = (int)$filters['max_amount'];
            $types .= "i";
        }
        
        // Add where conditions to queries
        if (!empty($where_conditions)) {
            $where_clause = " AND " . implode(" AND ", $where_conditions);
            $query .= $where_clause;
            $count_query .= $where_clause;
        }
        
        // Add sorting
        $query .= " ORDER BY Tanggal_Input DESC, created_at DESC";
        
        // Add pagination
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
            if ($offset) {
                $query .= " OFFSET " . (int)$offset;
            }
        }
        
        // Get total count
        $count_stmt = $conn->prepare($count_query);
        if (!empty($params)) {
            $count_stmt->bind_param($types, ...$params);
        }
        $count_stmt->execute();
        $total_result = $count_stmt->get_result();
        $total = $total_result->fetch_assoc()['total'];
        
        // Get data
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'data' => $data,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ];
        
    } catch (Exception $e) {
        handle_error("Error getting kas keluar data: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data kas keluar',
            'data' => [],
            'total' => 0
        ];
    }
}

/**
 * Get booking data with filtering and pagination
 * @param array $filters Filtering options
 * @param int $limit Limit number of results
 * @param int $offset Offset for pagination
 * @return array Result with data and metadata
 */
function get_bookings($filters = [], $limit = null, $offset = null) {
    $conn = get_db_connection();
    
    try {
        // Base query
        $query = "SELECT * FROM jadwal_booking WHERE deleted_at IS NULL";
        $count_query = "SELECT COUNT(*) as total FROM jadwal_booking WHERE deleted_at IS NULL";
        
        $params = [];
        $types = "";
        $where_conditions = [];
        
        // Apply filters
        if (!empty($filters['status'])) {
            $where_conditions[] = "status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (!empty($filters['date_from'])) {
            $where_conditions[] = "Tanggal >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }
        
        if (!empty($filters['date_to'])) {
            $where_conditions[] = "Tanggal <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }
        
        if (!empty($filters['event'])) {
            $where_conditions[] = "Event LIKE ?";
            $params[] = "%" . $filters['event'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['paket'])) {
            $where_conditions[] = "Paket LIKE ?";
            $params[] = "%" . $filters['paket'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['client'])) {
            $where_conditions[] = "client_name LIKE ?";
            $params[] = "%" . $filters['client'] . "%";
            $types .= "s";
        }
        
        // Add where conditions to queries
        if (!empty($where_conditions)) {
            $where_clause = " AND " . implode(" AND ", $where_conditions);
            $query .= $where_clause;
            $count_query .= $where_clause;
        }
        
        // Add sorting
        $query .= " ORDER BY Tanggal ASC, created_at DESC";
        
        // Add pagination
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
            if ($offset) {
                $query .= " OFFSET " . (int)$offset;
            }
        }
        
        // Get total count
        $count_stmt = $conn->prepare($count_query);
        if (!empty($params)) {
            $count_stmt->bind_param($types, ...$params);
        }
        $count_stmt->execute();
        $total_result = $count_stmt->get_result();
        $total = $total_result->fetch_assoc()['total'];
        
        // Get data
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'data' => $data,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ];
        
    } catch (Exception $e) {
        handle_error("Error getting booking data: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data booking',
            'data' => [],
            'total' => 0
        ];
    }
}

/**
 * Get single record by ID
 * @param string $table Table name
 * @param int $id Record ID
 * @return array Result with data
 */
function get_by_id($table, $id) {
    $conn = get_db_connection();
    
    try {
        // Validate inputs
        if (empty($table) || $id <= 0) {
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
        
        $query = "SELECT * FROM $table WHERE id = ? AND deleted_at IS NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => true,
                'data' => $result->fetch_assoc()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ];
        }
        
    } catch (Exception $e) {
        handle_error("Error getting record by ID: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data'
        ];
    }
}

/**
 * Get financial summary
 * @param array $filters Filtering options (month, year)
 * @return array Financial summary data
 */
function get_financial_summary($filters = []) {
    $conn = get_db_connection();
    
    try {
        $params = [];
        $types = "";
        $where_conditions = [];
        
        // Apply date filters
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $where_conditions[] = "MONTH(Tanggal_Input) = ? AND YEAR(Tanggal_Input) = ?";
            $params[] = (int)$filters['month'];
            $params[] = (int)$filters['year'];
            $types .= "ii";
        } elseif (!empty($filters['year'])) {
            $where_conditions[] = "YEAR(Tanggal_Input) = ?";
            $params[] = (int)$filters['year'];
            $types .= "i";
        }
        
        $where_clause = "";
        if (!empty($where_conditions)) {
            $where_clause = " AND " . implode(" AND ", $where_conditions);
        }
        
        // Get total kas masuk
        $kas_masuk_query = "SELECT COALESCE(SUM(Nominal), 0) as total FROM penerimaan_kas WHERE deleted_at IS NULL" . $where_clause;
        $kas_masuk_stmt = $conn->prepare($kas_masuk_query);
        if (!empty($params)) {
            $kas_masuk_stmt->bind_param($types, ...$params);
        }
        $kas_masuk_stmt->execute();
        $kas_masuk_result = $kas_masuk_stmt->get_result();
        $total_kas_masuk = $kas_masuk_result->fetch_assoc()['total'];
        
        // Get total kas keluar
        $kas_keluar_query = "SELECT COALESCE(SUM(Nominal), 0) as total FROM pengeluaran_kas WHERE deleted_at IS NULL" . $where_clause;
        $kas_keluar_stmt = $conn->prepare($kas_keluar_query);
        if (!empty($params)) {
            $kas_keluar_stmt->bind_param($types, ...$params);
        }
        $kas_keluar_stmt->execute();
        $kas_keluar_result = $kas_keluar_stmt->get_result();
        $total_kas_keluar = $kas_keluar_result->fetch_assoc()['total'];
        
        // Calculate balance
        $saldo = $total_kas_masuk - $total_kas_keluar;
        
        // Get total bookings (all time for basic count)
        $booking_query = "SELECT COUNT(*) as total FROM jadwal_booking WHERE deleted_at IS NULL";
        $booking_result = $conn->query($booking_query);
        $total_bookings = $booking_result->fetch_assoc()['total'];
        
        return [
            'success' => true,
            'data' => [
                'total_kas_masuk' => (int)$total_kas_masuk,
                'total_kas_keluar' => (int)$total_kas_keluar,
                'saldo' => (int)$saldo,
                'total_bookings' => (int)$total_bookings,
                'kas_masuk_formatted' => format_currency($total_kas_masuk),
                'kas_keluar_formatted' => format_currency($total_kas_keluar),
                'saldo_formatted' => format_currency($saldo)
            ]
        ];
        
    } catch (Exception $e) {
        handle_error("Error getting financial summary: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil ringkasan keuangan',
            'data' => []
        ];
    }
}

/**
 * Get combined financial report (kas masuk + kas keluar)
 * @param array $filters Filtering options
 * @param int $limit Limit number of results
 * @param int $offset Offset for pagination
 * @return array Combined financial data
 */
function get_financial_report($filters = [], $limit = null, $offset = null) {
    $conn = get_db_connection();
    
    try {
        $params = [];
        $types = "";
        $where_conditions = [];
        
        // Apply filters
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $where_conditions[] = "MONTH(Tanggal_Input) = ? AND YEAR(Tanggal_Input) = ?";
            $params = array_merge($params, [(int)$filters['month'], (int)$filters['year'], (int)$filters['month'], (int)$filters['year']]);
            $types .= "iiii";
        } elseif (!empty($filters['year'])) {
            $where_conditions[] = "YEAR(Tanggal_Input) = ?";
            $params = array_merge($params, [(int)$filters['year'], (int)$filters['year']]);
            $types .= "ii";
        }
        
        $where_clause = "";
        if (!empty($where_conditions)) {
            $where_clause = " AND " . implode(" AND ", $where_conditions);
        }
        
        // Union query to combine kas masuk and kas keluar
        $query = "
            (SELECT Tanggal_Input as tanggal, 'Kas Masuk' as jenis, Event_WLE as event, 
                    Keterangan as keterangan, Nominal as nominal, 'success' as class,
                    created_at
             FROM penerimaan_kas 
             WHERE deleted_at IS NULL $where_clause)
            UNION ALL
            (SELECT Tanggal_Input as tanggal, 'Kas Keluar' as jenis, Event_WLE as event, 
                    Keterangan as keterangan, Nominal as nominal, 'danger' as class,
                    created_at
             FROM pengeluaran_kas 
             WHERE deleted_at IS NULL $where_clause)
            ORDER BY tanggal DESC, created_at DESC
        ";
        
        // Add pagination
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
            if ($offset) {
                $query .= " OFFSET " . (int)$offset;
            }
        }
        
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'data' => $data
        ];
        
    } catch (Exception $e) {
        handle_error("Error getting financial report: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil laporan keuangan',
            'data' => []
        ];
    }
}

/**
 * Get users data with filtering and pagination
 * @param array $filters Filtering options
 * @param int $limit Limit number of results
 * @param int $offset Offset for pagination
 * @return array Result with data and metadata
 */
function get_users($filters = [], $limit = null, $offset = null) {
    $conn = get_db_connection();
    
    try {
        // Base query (excluding password_hash from selection for security)
        $query = "SELECT id, username, email, full_name, role, status, created_at, updated_at FROM users WHERE deleted_at IS NULL";
        $count_query = "SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL";
        
        $params = [];
        $types = "";
        $where_conditions = [];
        
        // Apply filters
        if (!empty($filters['role'])) {
            $where_conditions[] = "role = ?";
            $params[] = $filters['role'];
            $types .= "s";
        }
        
        if (!empty($filters['status'])) {
            $where_conditions[] = "status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (!empty($filters['search'])) {
            $where_conditions[] = "(username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
            $search_term = "%" . $filters['search'] . "%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $types .= "sss";
        }
        
        // Add where conditions to queries
        if (!empty($where_conditions)) {
            $where_clause = " AND " . implode(" AND ", $where_conditions);
            $query .= $where_clause;
            $count_query .= $where_clause;
        }
        
        // Add sorting
        $query .= " ORDER BY created_at DESC";
        
        // Add pagination
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
            if ($offset) {
                $query .= " OFFSET " . (int)$offset;
            }
        }
        
        // Get total count
        $count_stmt = $conn->prepare($count_query);
        if (!empty($params)) {
            $count_stmt->bind_param($types, ...$params);
        }
        $count_stmt->execute();
        $total_result = $count_stmt->get_result();
        $total = $total_result->fetch_assoc()['total'];
        
        // Get data
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'data' => $data,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ];
        
    } catch (Exception $e) {
        handle_error("Error getting users data: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data users',
            'data' => [],
            'total' => 0
        ];
    }
}

/**
 * Get categories data
 * @param array $filters Filtering options
 * @return array Categories data
 */
function get_categories($filters = []) {
    $conn = get_db_connection();
    
    try {
        $query = "SELECT * FROM categories WHERE deleted_at IS NULL";
        $params = [];
        $types = "";
        
        // Apply filters
        if (!empty($filters['type'])) {
            $query .= " AND type = ?";
            $params[] = $filters['type'];
            $types .= "s";
        }
        
        if (!empty($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        $query .= " ORDER BY name ASC";
        
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'data' => $data
        ];
        
    } catch (Exception $e) {
        handle_error("Error getting categories data: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data kategori',
            'data' => []
        ];
    }
}
?>