<?php
require_once '../config/database.php';

class KasMasukTambah {
    private $pdo;
    
    public function __construct() {
        $this->pdo = DatabaseConfig::getConnection();
    }
    
    public function tambahKasMasuk($data) {
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
            
            $sql = "INSERT INTO penerimaan_kas (Tanggal_Input, Event_WLE, Keterangan, Nominal) 
                    VALUES (:tanggal, :event, :keterangan, :nominal)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $result = $stmt->execute([
                ':tanggal' => $data['tanggal'],
                ':event' => $data['event'],
                ':keterangan' => $data['keterangan'],
                ':nominal' => $data['nominal']
            ]);
            
            if ($result) {
                $this->logActivity('CREATE', "Menambah kas masuk: {$data['event']} - Rp " . number_format($data['nominal']));
                
                return [
                    'success' => true,
                    'message' => 'Data kas masuk berhasil ditambahkan!',
                    'id' => $this->pdo->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menambahkan data kas masuk!'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error adding kas masuk: " . $e->getMessage());
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
        
        if (empty($data['event'])) {
            return ['valid' => false, 'message' => 'Nama event harus diisi!'];
        }
        
        if (empty($data['keterangan'])) {
            return ['valid' => false, 'message' => 'Keterangan harus diisi!'];
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
        $sql = "SELECT COUNT(*) as count FROM penerimaan_kas 
                WHERE Tanggal_Input = :tanggal 
                AND Event_WLE = :event 
                AND Keterangan = :keterangan 
                AND Nominal = :nominal";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':tanggal' => $data['tanggal'],
            ':event' => $data['event'],
            ':keterangan' => $data['keterangan'],
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
            'nominal' => floatval($data['nominal'] ?? 0)
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah_kas_masuk') {
    header('Content-Type: application/json');
    
    $kasMasuk = new KasMasukTambah();
    $data = $kasMasuk->sanitizeInput($_POST);
    $result = $kasMasuk->tambahKasMasuk($data);
    
    echo json_encode($result);
    exit;
}
?>