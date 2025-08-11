<?php
require_once '../config/database.php';

class KasMasukHapus {
    private $pdo;
    
    public function __construct() {
        $this->pdo = DatabaseConfig::getConnection();
    }
    
    public function hapusKasMasuk($id) {
        try {
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                return [
                    'success' => false,
                    'message' => 'ID tidak valid!'
                ];
            }
            
            // Check if record exists and get data for logging
            $existingData = $this->getKasMasukById($id);
            if (!$existingData) {
                return [
                    'success' => false,
                    'message' => 'Data tidak ditemukan!'
                ];
            }
            
            // Check if record is referenced in other tables (business rule)
            if ($this->hasReferences($id)) {
                return [
                    'success' => false,
                    'message' => 'Data tidak dapat dihapus karena masih digunakan!'
                ];
            }
            
            $sql = "DELETE FROM penerimaan_kas WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);
            
            if ($result && $stmt->rowCount() > 0) {
                $this->logActivity(
                    'DELETE', 
                    "Menghapus kas masuk ID {$id}: {$existingData['Event_WLE']} - Rp " . number_format($existingData['Nominal'])
                );
                
                return [
                    'success' => true,
                    'message' => 'Data kas masuk berhasil dihapus!',
                    'deleted_data' => $existingData
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus data kas masuk!'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error deleting kas masuk: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan pada database!'
            ];
        }
    }
    
    public function hapusMultipleKasMasuk($ids) {
        try {
            if (!is_array($ids) || empty($ids)) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih untuk dihapus!'
                ];
            }
            
            $deletedCount = 0;
            $errors = [];
            
            $this->pdo->beginTransaction();
            
            foreach ($ids as $id) {
                $result = $this->hapusKasMasuk($id);
                if ($result['success']) {
                    $deletedCount++;
                } else {
                    $errors[] = "ID {$id}: " . $result['message'];
                }
            }
            
            if ($deletedCount > 0) {
                $this->pdo->commit();
                $message = "Berhasil menghapus {$deletedCount} data";
                if (!empty($errors)) {
                    $message .= ". Gagal: " . implode(', ', $errors);
                }
                
                return [
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount
                ];
            } else {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'message' => 'Tidak ada data yang berhasil dihapus. Errors: ' . implode(', ', $errors)
                ];
            }
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error deleting multiple kas masuk: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data!'
            ];
        }
    }
    
    public function softDeleteKasMasuk($id) {
        try {
            // Check if record exists
            if (!$this->recordExists($id)) {
                return [
                    'success' => false,
                    'message' => 'Data tidak ditemukan!'
                ];
            }
            
            // Add deleted_at column if not exists (for soft delete)
            $this->addDeletedAtColumn();
            
            $sql = "UPDATE penerimaan_kas SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id AND deleted_at IS NULL";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);
            
            if ($result && $stmt->rowCount() > 0) {
                $data = $this->getKasMasukById($id);
                $this->logActivity('SOFT_DELETE', "Soft delete kas masuk ID {$id}: {$data['Event_WLE']}");
                
                return [
                    'success' => true,
                    'message' => 'Data kas masuk berhasil dihapus (soft delete)!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus data atau data sudah dihapus sebelumnya!'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error soft deleting kas masuk: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan pada database!'
            ];
        }
    }
    
    public function restoreKasMasuk($id) {
        try {
            $sql = "UPDATE penerimaan_kas SET deleted_at = NULL WHERE id = :id AND deleted_at IS NOT NULL";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);
            
            if ($result && $stmt->rowCount() > 0) {
                $data = $this->getKasMasukById($id);
                $this->logActivity('RESTORE', "Mengembalikan kas masuk ID {$id}: {$data['Event_WLE']}");
                
                return [
                    'success' => true,
                    'message' => 'Data kas masuk berhasil dikembalikan!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mengembalikan data atau data tidak ditemukan!'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error restoring kas masuk: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan pada database!'
            ];
        }
    }
    
    private function getKasMasukById($id) {
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
    
    private function recordExists($id) {
        $sql = "SELECT COUNT(*) as count FROM penerimaan_kas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    private function hasReferences($id) {
        // Check if this kas masuk is referenced in other tables
        // Add your business logic here
        // For example, check if used in reports, etc.
        return false; // For now, allow deletion
    }
    
    private function addDeletedAtColumn() {
        try {
            $sql = "SHOW COLUMNS FROM penerimaan_kas LIKE 'deleted_at'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                $alterSql = "ALTER TABLE penerimaan_kas ADD COLUMN deleted_at TIMESTAMP NULL";
                $this->pdo->exec($alterSql);
            }
        } catch (Exception $e) {
            error_log("Error adding deleted_at column: " . $e->getMessage());
        }
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
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $kasMasuk = new KasMasukHapus();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'hapus_kas_masuk':
                $id = intval($_POST['id'] ?? 0);
                $result = $kasMasuk->hapusKasMasuk($id);
                break;
                
            case 'hapus_multiple_kas_masuk':
                $ids = array_map('intval', $_POST['ids'] ?? []);
                $result = $kasMasuk->hapusMultipleKasMasuk($ids);
                break;
                
            case 'soft_delete_kas_masuk':
                $id = intval($_POST['id'] ?? 0);
                $result = $kasMasuk->softDeleteKasMasuk($id);
                break;
                
            case 'restore_kas_masuk':
                $id = intval($_POST['id'] ?? 0);
                $result = $kasMasuk->restoreKasMasuk($id);
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
}

// Handle GET request for direct deletion (with confirmation)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $kasMasuk = new KasMasukHapus();
    $result = $kasMasuk->hapusKasMasuk($id);
    
    if ($result['success']) {
        header("Location: ../index.php?tab=kas-masuk&deleted=1");
    } else {
        header("Location: ../index.php?tab=kas-masuk&error=" . urlencode($result['message']));
    }
    exit;
}
?>