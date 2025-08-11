<?php
require_once '../config/database.php';

class KasKeluarTambah {
    private $pdo;
    
    public function __construct() {
        $this->pdo = DatabaseConfig::getConnection();
    }
    
    public function tambahKasKeluar($data) {
        try {
            // Validate input
            $validation = $this->validateInput($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['message']
                ];
            }
            
            // Check for duplicate
            if ($this->isDuplicate($data)) {
                return [
                    'success' => false,
                    'message' => 'Data dengan informasi yang sama sudah ada!'
                ];
            }
            
            $sql = "INSERT INTO pengeluaran_kas (Tanggal_Input, Event_WLE, Keterangan, Nama_Akun, Nominal) 
                    VALUES (:tanggal, :event, :keterangan, :nama_akun, :nominal)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $result = $stmt->execute([
                ':tanggal' => $data['tanggal'],
                ':event' => $data['event'],
                ':keterangan' => $data['keterangan'],
                ':nama_akun' => $data['nama_akun'],
                ':nominal' => $data['nominal']
            ]);
            
            if ($result) {
                $this->logActivity('CREATE', "Menambah kas keluar: {$data['keterangan']} - Rp " . number_format($data['nominal']));
                
                return [
                    'success' => true,
                    'message' => 'Data kas keluar berhasil ditambahkan!',
                    'id' => $this->pdo->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menambahkan data kas keluar!'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error adding kas keluar: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan pada database!'
            ];
        }
    }
    
    private function validateInput($data) {
        // Check required fields
        if (empty($data['tanggal'])) {
            return ['valid' => false, 'message' => 'Tanggal harus diisi!'];
        }
        
        if (empty($data['keterangan'])) {
            return ['valid' => false, 'message' => 'Keterangan harus diisi!'];
        }
        
        if (empty($data['nama_akun'])) {
            return ['valid' => false, 'message' => 'Nama akun harus diisi!'];
        }
        
        if (empty($data['nominal']) || $data['nominal'] <= 0) {
            return ['valid' => false, 'message' => 'Nominal harus lebih dari 0!'];
        }
        
        // Validate date format
        if (!$this->isValidDate($data['tanggal'])) {
            return ['valid' => false, 'message' => 'Format tanggal tidak valid!'];
        }
        
        // Validate nominal (should be numeric)
        if (!is_numeric($data['nominal'])) {
            return ['valid' => false, 'message' => 'Nominal harus berupa angka!'];
        }
        
        // Check future date (optional business rule)
        if (strtotime($data['tanggal']) > strtotime('+1 month')) {
            return ['valid' => false, 'message' => 'Tanggal tidak boleh lebih dari 1 bulan ke depan!'];
        }
        
        return ['valid' => true, 'message' => 'Valid'];
    }
    
    private function isValidDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    private function isDuplicate($data) {
        $sql = "SELECT COUNT(*) as count FROM pengeluaran_kas 
                WHERE Tanggal_Input = :tanggal 
                AND Keterangan = :keterangan 
                AND Nama_Akun = :nama_akun 
                AND Nominal = :nominal";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':tanggal' => $data['tanggal'],
            ':keterangan' => $data['keterangan'],
            ':nama_akun' => $data['nama_akun'],
            ':nominal' => $data['nominal']
        ]);
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    private function logActivity($action, $description) {
        try {
            $sql = "INSERT INTO activity_log (user_id, action, description, ip_address, user_agent) 
                    VALUES (:user_id, :action, :description, :ip, :user_agent)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'] ?? 1,
                ':action' => $action,
                ':description' => $description,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log("Error logging activity: " . $e->getMessage());
        }
    }
    
    public function sanitizeInput($data) {
        return [
            'tanggal' => trim(htmlspecialchars($data['tanggal'] ?? '')),
            'event' => trim(htmlspecialchars($data['event'] ?? '')),
            'keterangan' => trim(htmlspecialchars($data['keterangan'] ?? '')),
            'nama_akun' => trim(htmlspecialchars($data['nama_akun'] ?? '')),
            'nominal' => floatval($data['nominal'] ?? 0)
        ];
    }
    
    public function getAkunList() {
        $sql = "SELECT DISTINCT Nama_Akun FROM pengeluaran_kas ORDER BY Nama_Akun";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah_kas_keluar') {
    header('Content-Type: application/json');
    
    $kasKeluar = new KasKeluarTambah();
    $data = $kasKeluar->sanitizeInput($_POST);
    $result = $kasKeluar->tambahKasKeluar($data);
    
    echo json_encode($result);
    exit;
}

// Handle GET request for account list
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_akun_list') {
    header('Content-Type: application/json');
    
    $kasKeluar = new KasKeluarTambah();
    $akun_list = $kasKeluar->getAkunList();
    
    echo json_encode(['success' => true, 'data' => $akun_list]);
    exit;
}
?>