# Sistem Manajemen Keuangan Dekorasi – untuk Klien: Dwiyanti Halimah

Dokumen ini merangkum gambaran proyek, cara menjalankan, struktur file, dan alur penggunaan. Disusun singkat, jelas, dan siap dipakai saat seminar proposal.

## Ringkasan Proyek
- __Tujuan__: Mencatat dan memantau keuangan (Kas Masuk, Kas Keluar), Jadwal Booking, serta mencetak laporan PDF bulanan.
- __Peran Pengguna__: Admin (login), mengelola transaksi dan booking, melihat ringkasan dashboard.
- __Teknologi__: PHP + MySQL, Bootstrap 5, Font Awesome, mPDF untuk PDF.

## Fitur Utama
- __Dashboard (`views/dashboard.php`)__
  - Ringkasan jumlah dan total: Booking, Kas Masuk, Kas Keluar (mengambil data dari tabel: `jadwal_booking`, `penerimaan_kas`, `pengeluaran_kas`).
  - Menampilkan nama admin dari tabel `users` (berdasarkan session login).
  - Galeri foto dekorasi dari folder `assets/img/` (file berawalan `Dekor*.jpg/png`), maksimal 12 foto agar ringan.
- __Kas Masuk (`views/kas_masuk.php`)__
  - Input transaksi penerimaan, edit/hapus, dan tabel data.
  - Handler backend di `functions/` (mis. `kas_masuk_tambah.php`, `kas_masuk_edit.php`, `kas_masuk_hapus.php`).
- __Kas Keluar (`views/kas_keluar.php`)__
  - Input transaksi pengeluaran, edit/hapus, dan tabel data.
  - Handler backend di `functions/` (mis. `kas_keluar_tambah.php`, `kas_keluar_edit.php`, `kas_keluar_hapus.php`).
- __Booking (`views/booking.php`)__
  - Input jadwal booking, pilih paket, edit/hapus.
  - Handler di `functions/booking_*.php`.
- __Laporan Keuangan (`views/laporan.php`)__
  - Tabel gabungan Kas Masuk & Kas Keluar bulan berjalan, total bulanan, dan tombol cetak PDF.
  - __Cetak PDF__: `cetaklaporan.php` (mPDF, ada filter bulan/tahun; direktori `tmp` untuk mPDF diatur aman).
- __Autentikasi__
  - Login via `Login.php`, cek session di `cek.php`. Nama admin di dashboard diambil dari tabel `users` jika tersedia.

## Pembaruan Penting (Agustus 2025)
Beberapa perubahan signifikan telah diimplementasikan untuk meningkatkan fungsionalitas dan manajemen pengguna:

-   **Sistem Hak Akses Berbasis Peran (Role-Based Access Control):**
    *   **Admin:** Memiliki akses penuh ke semua fitur dan halaman.
    *   **Owner:** Memiliki akses terbatas hanya pada **Dashboard**, **Jadwal Booking**, dan **Laporan Keuangan**.

-   **Alur Kerja Terintegrasi - Booking Otomatis ke Kas Masuk:**
    *   Setiap kali Anda melakukan input booking baru, sistem akan secara otomatis membuat entri yang sesuai di bagian **Kas Masuk** dengan nominal berdasarkan paket yang dipilih.

-   **Peningkatan Halaman Kas Masuk:**
    *   Pada halaman **Kas Masuk**, Anda sekarang dapat memilih event dari daftar booking yang sudah ada. Ketika event dipilih, kolom keterangan dan nominal akan terisi secara otomatis berdasarkan data booking dan harga paket yang telah ditentukan.

-   **Akun Pengguna Owner Baru:**
    *   Telah dibuat akun khusus untuk peran Owner:
        *   **Email/Username:** `owner@graceful.com`
        *   **Password:** `owner123`
        *   **Nama Lengkap:** Owner Graceful

-   **Perubahan Konfigurasi Database (Penting untuk Pengguna Windows):**
    *   Untuk meningkatkan kompatibilitas koneksi database, terutama pada lingkungan Windows, konfigurasi host database di file `function.php` telah diubah dari `localhost` menjadi `127.0.0.1`.
    *   **Lokasi File:** `/opt/lampp/htdocs/cashflow-dwi/function.php`
    *   **Baris yang Diubah:** `$db_host = "localhost";` menjadi `$db_host = "127.0.0.1";`

-   **Pembaruan Skema Database:**
    *   Kolom `role` pada tabel `users` telah diperbarui untuk menyertakan nilai `'owner'` sebagai opsi yang valid. Ini memastikan sistem dapat mengenali dan mengelola peran Owner dengan benar.

## Cara Menjalankan (XAMPP/LAMPP)
1. __Siapkan Server__
   - Jalankan Apache & MySQL.
   - Salin folder proyek ke: `htdocs/(namafolderprojek)` (XAMPP) atau `/opt/lampp/htdocs/(namafolderprojek)` (LAMPP).
2. __Konfigurasi Database__
   - Buat database MySQL: `cashflow`.
   - Import struktur tabel minimal: `penerimaan_kas`, `pengeluaran_kas`, `jadwal_booking`, `users` (untuk login/nama admin).
3. __Konfigurasi Koneksi__
   - File koneksi: `config/koneksi.php` (nama baru pengganti `database.php`). Pastikan `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` sesuai.
4. __Akses Aplikasi__
   - Buka `http://localhost/(namafolderprojek)/index.php`.
   - Login via `Login.php` (gunakan akun yang ada di tabel `users`).

## Struktur Direktori Penting
```
cashflow-dwi/
├── index.php                 # Router/entry utama halaman
├── dashboard.php             # Router konten, menyertakan file di folder views/
├── Login.php                 # Halaman login
├── Logout.php                # Keluar sesi
├── cek.php                   # Helper session/login
├── config/
│   ├── koneksi.php           # Koneksi MySQL (pengganti database.php)
│   └── database.php          # Shim yang mengarah ke koneksi.php (kompatibilitas lama)
├── layout/
│   ├── sidebar.php           # Sidebar navigasi (aktif berdasarkan tab)
│   └── navbar.php            # Navbar (menu user & logout)
├── views/
│   ├── dashboard.php         # Ringkasan + galeri
│   ├── kas_masuk.php         # UI kas masuk
│   ├── kas_keluar.php        # UI kas keluar
│   ├── booking.php           # UI booking
│   └── laporan.php           # Tabel gabungan + ekspor PDF
├── functions/
│   ├── kas_masuk_tambah.php  # Handler tambah kas masuk
│   ├── kas_masuk_edit.php    # Handler edit kas masuk
│   ├── kas_masuk_hapus.php   # Handler hapus kas masuk
│   ├── kas_keluar_tambah.php # Handler tambah kas keluar
│   ├── kas_keluar_edit.php   # Handler edit kas keluar
│   ├── kas_keluar_hapus.php  # Handler hapus kas keluar
│   ├── booking_tambah.php    # Handler tambah booking
│   ├── booking_edit.php      # Handler edit booking
│   └── booking_hapus.php     # Handler hapus booking
├── includes/
│   └── utils.php             # Helper umum (sanitasi, format rupiah, dsb.)
├── assets/
│   └── img/                  # Gambar galeri (Dekor*.jpg/png)
├── cetaklaporan.php          # Generator PDF (mPDF) laporan bulanan
├── cetak_kasmasuk.php        # Cetak laporan kas masuk (opsional)
├── cetak_kaskeluar.php       # Cetak laporan kas keluar (opsional)
└── README.md                 # Dokumen ini
```

## Alur Kerja Singkat (untuk Presentasi)
- __Login__ → masuk sebagai admin.
- __Dashboard__ → lihat ringkasan dan galeri (nama admin tampil dari DB `users`).
- __Kas Masuk/Keluar__ → input transaksi, simpan, edit/hapus dari tabel.
- __Booking__ → input jadwal, pilih paket, kelola data.
- __Laporan__ → pilih bulan/tahun → lihat tabel gabungan → klik cetak PDF (`cetaklaporan.php`).

## Catatan Teknis Penting
- __Normalisasi Redirect__: Semua handler `functions/*` mengarah ke `dashboard.php?tab=...` agar konsisten.
- __mPDF__:
  - Autoload: `vendor/autoload.php` (jalankan `composer require mpdf/mpdf` bila belum ada).
  - Temp folder aman: otomatis membuat folder `tmp` lokal jika belum ada.
- __Keamanan__: Sanitasi input, prepared statements, dan cek session (`cek.php`).

## Rencana Lanjutan (Opsional)
- Filter periode di dashboard (mis. bulan berjalan).
- Lightbox untuk galeri.
- Ekspor Excel/CSV.
- Multi-user & role (admin/staff).

—

Disusun untuk __Dwiyanti Halimah__. Jika butuh materi slide ringkas untuk seminar (alur, fitur, dan demo singkat), bisa saya siapkan berdasarkan poin-poin di atas.