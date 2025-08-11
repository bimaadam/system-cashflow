<?php
require_once '../config/database.php';

class KasKeluarEdit
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConfig::getConnection();
    }

    public function editKasKeluar($id, $data)
    {
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
                    'message' => 'Data kas keluar tidak ditemukan!'
                ];
            }

            // Get old data for logging
            $oldData = $this->getById($id);

            $sql = "UPDATE pengeluaran_kas 
                    SET Tanggal_Input = :tanggal, 
                        Event_WLE = :event, 
                        Keterangan = :keterangan, 
                        Nama_Akun = :nama_akun, 
                        Nominal = :nominal 
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);

            $result = $stmt->execute([
                ':id' => $id,
                ':tanggal' => $data['tanggal'],
                ':event' => $data['event'],
                ':keterangan' => $data['keterangan'],
                ':nama_akun' => $data['nama_akun'],
                ':nominal' => $data['nominal']
            ]);

            if ($result && $stmt->rowCount() > 0) {
                $this->logActivity('UPDATE', "Mengubah kas keluar ID: {$id} dari '{$oldData['Keterangan']}' menjadi '{$data['keterangan']}'");

                return [
                    'success' => true,
                    'message' => 'Data kas keluar berhasil diubah!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Tidak ada perubahan data atau gagal mengubah data!'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error editing kas keluar: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan pada database!'
            ];
        }
    }

    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM pengeluaran_kas WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting kas keluar by ID: " . $e->getMessage());
            return false;
        }
    }

    private function recordExists($id)
    {
        $sql = "SELECT COUNT(*) as count FROM pengeluaran_kas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    private function validateInput($data)
    {
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

        return ['valid' => true, 'message' => 'Valid'];
    }

    private function isValidDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    private function logActivity($action, $description)
    {
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

    public function sanitizeInput($data)
    {
        return [
            'tanggal' => trim(htmlspecialchars($data['tanggal'] ?? '')),
            'event' => trim(htmlspecialchars($data['event'] ?? '')),
            'keterangan' => trim(htmlspecialchars($data['keterangan'] ?? '')),
            'nama_akun' => trim(htmlspecialchars($data['nama_akun'] ?? '')),
            'nominal' => floatval($data['nominal'] ?? 0)
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_kas_keluar') {
    header('Content-Type: application/json');

    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid!']);
        exit;
    }

    $kasKeluar = new KasKeluarEdit();
    $data = $kasKeluar->sanitizeInput($_POST);
    $result = $kasKeluar->editKasKeluar($_POST['id'], $data);

    echo json_encode($result);
    exit;
}

// Handle GET request for single record
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_kas_keluar') {
    header('Content-Type: application/json');

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid!']);
        exit;
    }

    $kasKeluar = new KasKeluarEdit();
    $data = $kasKeluar->getById($_GET['id']);

    if ($data) {
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan!']);
    }
    exit;
}
