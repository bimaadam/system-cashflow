# Cashflow Management System - Dekorasi Graceful

## Deskripsi
Sistem manajemen keuangan untuk bisnis dekorasi yang telah diperbarui dengan UI/UX modern dan struktur kode yang lebih baik.

## Peningkatan yang Dilakukan

### 1. **Perbaikan UI/UX**
- **Desain Modern**: Menggunakan gradient background dan glassmorphism effect
- **Responsif**: Tampilan menyesuaikan dengan berbagai ukuran layar
- **Animasi Smooth**: Transisi dan hover effects yang halus
- **Color Scheme**: Menggunakan color palette yang konsisten dan profesional
- **Typography**: Font yang lebih readable dan hierarki yang jelas

### 2. **Organisasi Kode yang Lebih Baik**
- **Struktur File Terorganisir**: Pemisahan logic dan presentation
- **Clean Code**: Kode yang mudah dibaca dan di-maintain
- **Security Improvements**: Prepared statements untuk mencegah SQL injection
- **Error Handling**: Penanganan error yang lebih baik

### 3. **Fitur-Fitur Utama**

#### Dashboard
- **Statistics Cards**: Menampilkan total kas masuk, keluar, saldo, dan booking
- **Welcome Header**: Header yang menarik dengan animasi
- **Gallery Section**: Galeri foto dekorasi dengan hover effects

#### Kas Masuk
- **Form Input**: Form yang clean dengan validation
- **Data Table**: Tabel yang terorganisir dengan DataTables
- **CRUD Operations**: Create, Read, Update, Delete dengan modal
- **Export Feature**: Cetak laporan per bulan/tahun

#### Kas Keluar
- **Kategorisasi**: Dropdown kategori pengeluaran
- **Management**: Edit dan hapus data dengan konfirmasi
- **Reporting**: Laporan pengeluaran yang detail

#### Jadwal Booking
- **Event Management**: Manajemen jadwal booking event
- **Package Selection**: Pilihan paket Silver, Gold, Platinum
- **Status Tracking**: Status booking (Selesai, Hari Ini, Mendatang)

#### Laporan Keuangan
- **Summary Cards**: Ringkasan keuangan bulanan
- **Combined Report**: Laporan gabungan kas masuk dan keluar
- **Export Function**: Download laporan dalam format PDF

### 4. **Teknologi yang Digunakan**
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 7.4+, MySQL
- **Libraries**: 
  - Simple DataTables untuk manajemen tabel
  - Font Awesome untuk icons
  - Bootstrap untuk responsive design

### 5. **Fitur Keamanan**
- **Prepared Statements**: Mencegah SQL injection
- **Input Sanitization**: Pembersihan input data
- **Session Management**: Pengelolaan sesi yang aman
- **Error Handling**: Penanganan error yang tidak mengungkap informasi sensitif

## Struktur File

```
cashflow-dwi/
├── index.php              # File utama dengan UI yang diperbaiki
├── function.php            # Functions dengan security improvements
├── cek.php                # Authentication check
├── cetak_kasmasuk.php     # Print report kas masuk
├── css/
│   ├── styles.css         # CSS framework Bootstrap
│   └── enhanced-styles.css # Custom CSS untuk UI enhancement
├── Dekor*.jpg             # Gallery images
└── README.md              # Dokumentasi ini
```

## Cara Menggunakan

### Setup Database
1. Buat database MySQL dengan nama `cashflow`
2. Buat tabel-tabel berikut:

```sql
-- Tabel penerimaan kas
CREATE TABLE penerimaan_kas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Tanggal_Input DATE NOT NULL,
    Event_WLE VARCHAR(255) NOT NULL,
    Keterangan TEXT NOT NULL,
    Nominal INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pengeluaran kas
CREATE TABLE pengeluaran_kas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Tanggal_Input DATE NOT NULL,
    Event_WLE VARCHAR(255),
    Keterangan TEXT NOT NULL,
    Nama_Akun VARCHAR(100) NOT NULL,
    Nominal INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel jadwal booking
CREATE TABLE jadwal_booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Tanggal DATE NOT NULL,
    Event VARCHAR(255) NOT NULL,
    Paket VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Instalasi
1. Clone atau download file ke folder web server (htdocs untuk XAMPP)
2. Pastikan web server (Apache) dan MySQL sudah berjalan
3. Akses melalui browser: `http://localhost/cashflow-dwi/index.php`

### Penggunaan
1. **Dashboard**: Melihat ringkasan keuangan dan galeri
2. **Kas Masuk**: Mengelola penerimaan kas dari event
3. **Kas Keluar**: Mengelola pengeluaran dengan kategori
4. **Booking**: Mengelola jadwal booking event
5. **Laporan**: Melihat dan mencetak laporan keuangan

## Peningkatan yang Dapat Dilakukan

### Jangka Pendek
- [ ] Implementasi user authentication yang lebih robust
- [ ] Backup otomatis database
- [ ] Export ke Excel/CSV
- [ ] Filter tanggal yang lebih fleksibel

### Jangka Panjang
- [ ] API untuk integrasi dengan sistem lain
- [ ] Dashboard analytics dengan chart
- [ ] Notification system
- [ ] Multi-user dengan role management
- [ ] Mobile app companion

## Browser Support
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## Credits
Dikembangkan untuk Dekorasi Graceful dengan fokus pada:
- User Experience yang intuitif
- Performance yang optimal
- Maintainability yang baik
- Security yang terjamin

---

**Note**: Sistem ini telah dioptimalkan untuk kemudahan penggunaan dan tampilan yang modern sambil mempertahankan semua fungsionalitas inti yang diperlukan untuk manajemen keuangan bisnis dekorasi.