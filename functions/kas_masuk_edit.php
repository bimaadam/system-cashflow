<?php
require_once '../config/database.php';

class KasMasukEdit {
    private $pdo;
    
    public function __construct() {
        $this->pdo = DatabaseConfig::getConnection();
    }
    
    public function editKasMasuk($id, $data) {
        try {
            // Validate input
            $validation = $this->validateInput($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['message']
                ];
            }
            
            // Check if record exists
            if (!$this->recordExists($id)) {
                return [
                    'success' => false,
                    'message' => 'Data tidak ditemukan!'
                ];
            }
            
            // Get old data for logging
            $oldData = $this->getKasMasukById($id);
            
            $sql = "UPDATE penerimaan_kas 
                    SET Tanggal_Input = :tanggal, 
                        Event_WLE = :event, 
                        Keterangan = :keterangan, 
                        Nominal = :nominal,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            $result = $stmt->execute([
                ':id' => $id,
                ':tanggal' => $data['tanggal'],
                ':event' => $data['event'],
                ':keterangan' => $data['keterangan'],
                ':nominal' => $data['nominal']
            ]);
            
            if ($result) {
                $this->logActivity('UPDATE', "Mengubah kas masuk ID {$id}: {$oldData['Event_WLE']} -> {$data['event']}");
                
                return [
                    'success' => true,
                    'message' => 'Data kas masuk berhasil diubah!',
                    'data' => $this->getKasMasukById($id)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mengubah data kas masuk!'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error editing kas masuk: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan pada database!'
            ];
        }
    }
    
    public function getKasMasukById($id) {
        try {
            $sql = "SELECT * FROM penerimaan_kas WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting kas masuk by ID: " . $e->getMessage());
            return false;
        }
    }
    
    public function getKasMasukForEdit($id) {
        $data = $this->getKasMasukById($id);
        
        if ($data) {
            return [
                'success' => true,
                'data' => [
                    'id' => $data['id'],
                    'tanggal' => $data['Tanggal_Input'],
                    'event' => $data['Event_WLE'],
                    'keterangan' => $data['Keterangan'],
                    'nominal' => $data['Nominal']
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ];
        }
    }
    
    private function recordExists($id) {
        $sql = "SELECT COUNT(*) as count FROM penerimaan_kas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
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
        
        return ['valid' => true, 'message' => 'Valid'];
    }
    
    private function isValidDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $kasMasuk = new KasMasukEdit();
    
    if (isset($_POST['action']) && $_POST['action'] === 'edit_kas_masuk') {
        $id = intval($_POST['id'] ?? 0);
        $data = $kasMasuk->sanitizeInput($_POST);
        $result = $kasMasuk->editKasMasuk($id, $data);
        
        echo json_encode($result);
        exit;
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'get_kas_masuk') {
        $id = intval($_POST['id'] ?? 0);
        $result = $kasMasuk->getKasMasukForEdit($id);
        
        echo json_encode($result);
        exit;
    }
}
?>