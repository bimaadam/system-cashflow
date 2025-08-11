<?php
require_once '../config/database.php';

class KasMasukGet {
    private $pdo;
    
    public function __construct() {
        $this->pdo = DatabaseConfig::getConnection();
    }
    
    public function getAllKasMasuk($params = []) {
        try {
            $where = [];
            $bindParams = [];
            
            // Build WHERE conditions
            if (!empty($params['tanggal_dari'])) {
                $where[] = "Tanggal_Input >= :tanggal_dari";
                $bindParams[':tanggal_dari'] = $params['tanggal_dari'];
            }
            
            if (!empty($params['tanggal_sampai'])) {
                $where[] = "Tanggal_Input <= :tanggal_sampai";
                $bindParams[':tanggal_sampai'] = $params['tanggal_sampai'];
            }
            
            if (!empty($params['event'])) {
                $where[] = "Event_WLE LIKE :event";
                $bindParams[':event'] = '%' . $params['event'] . '%';
            }
            
            if (!empty($params['min_nominal'])) {
                $where[] = "Nominal >= :min_nominal";
                $bindParams[':min_nominal'] = $params['min_nominal'];
            }
            
            if (!empty($params['max_nominal'])) {
                $where[] = "Nominal <= :max_nominal";
                $bindParams[':max_nominal'] = $params['max_nominal'];
            }
            
            // Exclude soft deleted records
            $where[] = "(deleted_at IS NULL OR deleted_at = '')";
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            // Build ORDER BY
            $orderBy = $this->buildOrderBy($params);
            
            // Build LIMIT
            $limit = $this->buildLimit($params);
            
            $sql = "SELECT * FROM penerimaan_kas {$whereClause} {$orderBy} {$limit}";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($bindParams);
            
            $data = $stmt->fetchAll();
            
            // Get total count for pagination
            $totalCount = $this->getTotalCount($where, $bindParams);
            
            return [
                'success' => true,
                'data' => $data,
                'total' => $totalCount,
                'filtered' => count($data)
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting kas masuk data: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data!'
            ];
        }
    }
    
    public function getKasMasukById($id) {
        try {
            $sql = "SELECT * FROM penerimaan_kas WHERE id = :id AND (deleted_at IS NULL OR deleted_at = '')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $data = $stmt->fetch();
            
            if ($data) {
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Data tidak ditemukan!'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error getting kas masuk by ID: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data!'
            ];
        }
    }
    
    public function getKasMasukByMonth($bulan, $tahun) {
        try {
            $sql = "SELECT * FROM penerimaan_kas 
                    WHERE MONTH(Tanggal_Input) = :bulan 
                    AND YEAR(Tanggal_Input) = :tahun 
                    AND (deleted_at IS NULL OR deleted_at = '')
                    ORDER BY Tanggal_Input DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':bulan' => $bulan,
                ':tahun' => $tahun
            ]);
            
            $data = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting kas masuk by month: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data!'
            ];
        }
    }
    
    public function getKasMasukStatistics() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_transaksi,
                        SUM(Nominal) as total_nominal,
                        AVG(Nominal) as rata_rata,
                        MIN(Nominal) as minimal,
                        MAX(Nominal) as maksimal,
                        MIN(Tanggal_Input) as tanggal_pertama,
                        MAX(Tanggal_Input) as tanggal_terakhir
                    FROM penerimaan_kas 
                    WHERE (deleted_at IS NULL OR deleted_at = '')";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $stats = $stmt->fetch();
            
            // Get monthly statistics
            $monthlyStats = $this->getMonthlyStatistics();
            
            return [
                'success' => true,
                'data' => [
                    'overview' => $stats,
                    'monthly' => $monthlyStats
                ]
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting kas masuk statistics: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik!'
            ];
        }
    }
    
    public function getTopEvents($limit = 10) {
        try {
            $sql = "SELECT 
                        Event_WLE,
                        COUNT(*) as jumlah_transaksi,
                        SUM(Nominal) as total_nominal
                    FROM penerimaan_kas 
                    WHERE (deleted_at IS NULL OR deleted_at = '')
                    GROUP BY Event_WLE 
                    ORDER BY total_nominal DESC 
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $data = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $data
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting top events: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data event teratas!'
            ];
        }
    }
    
    public function searchKasMasuk($keyword) {
        try {
            $sql = "SELECT * FROM penerimaan_kas 
                    WHERE (Event_WLE LIKE :keyword 
                    OR Keterangan LIKE :keyword)
                    AND (deleted_at IS NULL OR deleted_at = '')
                    ORDER BY Tanggal_Input DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':keyword' => '%' . $keyword . '%']);
            
            $data = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ];
            
        } catch (PDOException $e) {
            error_log("Error searching kas masuk: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data!'
            ];
        }
    }
    
    public function getKasMasukForExport($bulan = null, $tahun = null) {
        try {
            $where = ["(deleted_at IS NULL OR deleted_at = '')"];
            $bindParams = [];
            
            if ($bulan && $tahun) {
                $where[] = "MONTH(Tanggal_Input) = :bulan AND YEAR(Tanggal_Input) = :tahun";
                $bindParams[':bulan'] = $bulan;
                $bindParams[':tahun'] = $tahun;
            } elseif ($tahun) {
                $where[] = "YEAR(Tanggal_Input) = :tahun";
                $bindParams[':tahun'] = $tahun;
            }
            
            $whereClause = 'WHERE ' . implode(' AND ', $where);
            
            $sql = "SELECT 
                        Tanggal_Input as 'Tanggal',
                        Event_WLE as 'Event',
                        Keterangan as 'Keterangan',
                        Nominal as 'Nominal'
                    FROM penerimaan_kas 
                    {$whereClause}
                    ORDER BY Tanggal_Input ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($bindParams);
            
            $data = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $data
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting kas masuk for export: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data export!'
            ];
        }
    }
    
    private function getTotalCount($where, $bindParams) {
        try {
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            $sql = "SELECT COUNT(*) as total FROM penerimaan_kas {$whereClause}";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($bindParams);
            
            $result = $stmt->fetch();
            return $result['total'];
        } catch (Exception $e) {
            return 0;
        }
    }
    
    private function getMonthlyStatistics() {
        try {
            $sql = "SELECT 
                        YEAR(Tanggal_Input) as tahun,
                        MONTH(Tanggal_Input) as bulan,
                        MONTHNAME(Tanggal_Input) as nama_bulan,
                        COUNT(*) as total_transaksi,
                        SUM(Nominal) as total_nominal
                    FROM penerimaan_kas 
                    WHERE (deleted_at IS NULL OR deleted_at = '')
                    GROUP BY YEAR(Tanggal_Input), MONTH(Tanggal_Input)
                    ORDER BY tahun DESC, bulan DESC
                    LIMIT 12";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function buildOrderBy($params) {
        $allowedColumns = ['Tanggal_Input', 'Event_WLE', 'Nominal', 'created_at'];
        $allowedDirections = ['ASC', 'DESC'];
        
        $orderColumn = isset($params['order_by']) && in_array($params['order_by'], $allowedColumns) 
                      ? $params['order_by'] 
                      : 'Tanggal_Input';
                      
        $orderDirection = isset($params['order_direction']) && in_array(strtoupper($params['order_direction']), $allowedDirections)
                         ? strtoupper($params['order_direction'])
                         : 'DESC';
        
        return "ORDER BY {$orderColumn} {$orderDirection}";
    }
    
    private function buildLimit($params) {
        $page = max(1, intval($params['page'] ?? 1));
        $perPage = max(1, min(100, intval($params['per_page'] ?? 25))); // Max 100 per page
        
        $offset = ($page - 1) * $perPage;
        
        return "LIMIT {$perPage} OFFSET {$offset}";
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $kasMasuk = new KasMasukGet();
    $action = $_REQUEST['action'] ?? '';
    
    switch ($action) {
        case 'get_all_kas_masuk':
            $params = [
                'tanggal_dari' => $_REQUEST['tanggal_dari'] ?? '',
                'tanggal_sampai' => $_REQUEST['tanggal_sampai'] ?? '',
                'event' => $_REQUEST['event'] ?? '',
                'min_nominal' => $_REQUEST['min_nominal'] ?? '',
                'max_nominal' => $_REQUEST['max_nominal'] ?? '',
                'order_by' => $_REQUEST['order_by'] ?? '',
                'order_direction' => $_REQUEST['order_direction'] ?? '',
                'page' => $_REQUEST['page'] ?? 1,
                'per_page' => $_REQUEST['per_page'] ?? 25
            ];
            $result = $kasMasuk->getAllKasMasuk($params);
            break;
            
        case 'get_kas_masuk_by_id':
            $id = intval($_REQUEST['id'] ?? 0);
            $result = $kasMasuk->getKasMasukById($id);
            break;
            
        case 'get_kas_masuk_by_month':
            $bulan = intval($_REQUEST['bulan'] ?? date('m'));
            $tahun = intval($_REQUEST['tahun'] ?? date('Y'));
            $result = $kasMasuk->getKasMasukByMonth($bulan, $tahun);
            break;
            
        case 'get_kas_masuk_statistics':
            $result = $kasMasuk->getKasMasukStatistics();
            break;
            
        case 'get_top_events':
            $limit = intval($_REQUEST['limit'] ?? 10);
            $result = $kasMasuk->getTopEvents($limit);
            break;
            
        case 'search_kas_masuk':
            $keyword = $_REQUEST['keyword'] ?? '';
            $result = $kasMasuk->searchKasMasuk($keyword);
            break;
            
        case 'get_kas_masuk_export':
            $bulan = $_REQUEST['bulan'] ?? null;
            $tahun = $_REQUEST['tahun'] ?? null;
            $result = $kasMasuk->getKasMasukForExport($bulan, $tahun);
            break;
            
        default:
            $result = [
                'success' => false,
                'message' => 'Action tidak dikenali!'
            ];
    }
    
    echo json_encode($result);
    exit;
}
?>