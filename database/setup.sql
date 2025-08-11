-- Database Setup for Cashflow Management System
-- Dekorasi Graceful

-- Create database
CREATE DATABASE IF NOT EXISTS cashflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cashflow;

-- Table: penerimaan_kas (Kas Masuk)
CREATE TABLE IF NOT EXISTS penerimaan_kas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Tanggal_Input DATE NOT NULL,
    Event_WLE VARCHAR(255) NOT NULL,
    Keterangan TEXT NOT NULL,
    Nominal DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tanggal (Tanggal_Input),
    INDEX idx_event (Event_WLE),
    INDEX idx_nominal (Nominal)
) ENGINE=InnoDB;

-- Table: pengeluaran_kas (Kas Keluar)
CREATE TABLE IF NOT EXISTS pengeluaran_kas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Tanggal_Input DATE NOT NULL,
    Event_WLE VARCHAR(255),
    Keterangan TEXT NOT NULL,
    Nama_Akun VARCHAR(100) NOT NULL,
    Nominal DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tanggal (Tanggal_Input),
    INDEX idx_akun (Nama_Akun),
    INDEX idx_nominal (Nominal)
) ENGINE=InnoDB;

-- Table: jadwal_booking (Booking Schedule)
CREATE TABLE IF NOT EXISTS jadwal_booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Tanggal DATE NOT NULL,
    Event VARCHAR(255) NOT NULL,
    Paket ENUM('Silver', 'Gold', 'Platinum') NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tanggal (Tanggal),
    INDEX idx_paket (Paket),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Table: users (for authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    full_name VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table: activity_log (for logging activities)
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Insert default admin user
INSERT INTO users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@graceful.com', 'Administrator', 'admin')
ON DUPLICATE KEY UPDATE username = username;

-- Insert sample data for testing (optional)
INSERT INTO penerimaan_kas (Tanggal_Input, Event_WLE, Keterangan, Nominal) VALUES 
('2024-01-15', 'Wedding Andi & Sari', 'DP Dekorasi Wedding', 5000000),
('2024-01-20', 'Birthday Party Budi', 'Dekorasi Ulang Tahun', 2500000),
('2024-01-25', 'Corporate Event PT ABC', 'Dekorasi Event Kantor', 7500000)
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO pengeluaran_kas (Tanggal_Input, Event_WLE, Keterangan, Nama_Akun, Nominal) VALUES 
('2024-01-16', 'Wedding Andi & Sari', 'Beli Bunga Mawar', 'Peralatan', 1500000),
('2024-01-16', 'Wedding Andi & Sari', 'Sewa Karpet Merah', 'Rental', 800000),
('2024-01-21', 'Birthday Party Budi', 'Balon dan Pita', 'Peralatan', 300000)
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO jadwal_booking (Tanggal, Event, Paket) VALUES 
('2024-02-14', 'Wedding Valentine Special', 'Platinum'),
('2024-02-20', 'Anniversary Party', 'Gold'),
('2024-03-01', 'Corporate Meeting', 'Silver')
ON DUPLICATE KEY UPDATE id = id;

-- Create views for easier reporting
CREATE OR REPLACE VIEW v_monthly_summary AS
SELECT 
    YEAR(Tanggal_Input) as tahun,
    MONTH(Tanggal_Input) as bulan,
    MONTHNAME(Tanggal_Input) as nama_bulan,
    SUM(CASE WHEN table_name = 'penerimaan' THEN Nominal ELSE 0 END) as total_masuk,
    SUM(CASE WHEN table_name = 'pengeluaran' THEN Nominal ELSE 0 END) as total_keluar,
    SUM(CASE WHEN table_name = 'penerimaan' THEN Nominal ELSE 0 END) - 
    SUM(CASE WHEN table_name = 'pengeluaran' THEN Nominal ELSE 0 END) as saldo
FROM (
    SELECT Tanggal_Input, Nominal, 'penerimaan' as table_name FROM penerimaan_kas
    UNION ALL
    SELECT Tanggal_Input, Nominal, 'pengeluaran' as table_name FROM pengeluaran_kas
) combined
GROUP BY YEAR(Tanggal_Input), MONTH(Tanggal_Input)
ORDER BY tahun DESC, bulan DESC;

-- Optimize tables
OPTIMIZE TABLE penerimaan_kas, pengeluaran_kas, jadwal_booking, users, activity_log;