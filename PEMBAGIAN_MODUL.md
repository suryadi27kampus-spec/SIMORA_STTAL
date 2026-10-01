# Pembagian modul pembangunan SIMORA

Pekerjaan disusun per modul agar mudah ditinjau dan dilanjutkan. Modul independen dapat dikerjakan paralel; sambungan antar-modul tetap ditinjau bersama.

| Modul | Berkas utama | Hasil yang dituju |
|---|---|---|
| Login dan tampilan bersama | `app/Web.php`, `public/setup.php`, `public/login.php`, `public/logout.php`, `public/assets/app.css`, `database/migrations/001_app_users.sql` | Pembuatan admin pertama, sesi login, logout, navigasi responsif, dan gaya konsisten di seluruh modul. |
| Impor dan tinjauan | `importer/parse_sources.py`, `app/ImportBatchService.php`, `app/ImportNormalizer.php`, `public/index.php`, `public/review.php` | Membaca empat format workbook, menyimpan data sumber, meninjau, lalu membentuk data SIMORA. |
| Database dan pengaturan | `database/schema.sql`, `public/settings.php` | Mengatur kelompok D3/S1/S2, pemetaan kode akun belanja, Pembuat PJK, masa tugas, ambang GAP, kalender libur, dan jendela pembaruan RPD. |
| Dashboard dan monitoring | `app/MonitoringService.php`, `public/dashboard.php` | Menampilkan realisasi per jenis belanja, RPD kumulatif, GAP (%), kelengkapan pemadanan, komponen, kelompok, dan Pembuat PJK. |
| Penelusuran MyINTRESS | `public/transactions.php` | Mencari detail SPP/SPM, SP2D, COA 16 segmen, dan akun transaksi sebagai data pendukung SAKTI. |
| Peringatan dan WhatsApp | `app/NotificationQueueGenerator.php`, `app/NotificationDispatcher.php`, `app/WhatsAppCloudApi.php`, `public/notifications.php`, `bin/` | Membuat antrean idempoten, melihat status, lalu mengirim pesan setelah konfigurasi WhatsApp diaktifkan. |
| Pemasangan dan panduan | `README.md`, `PANDUAN_PENGGUNA.md` | Menjelaskan pemasangan XAMPP, impor workbook, pengaturan, dan penjadwalan lokal. |

## Status prototipe

Alur kode dan antarmuka awal untuk modul-modul di atas sudah tersedia. Login memakai pembuatan akun admin pertama dan tidak memiliki kata sandi bawaan. Tahap yang masih memerlukan konfigurasi dari pengguna adalah akun WhatsApp Business, token akses, template pesan yang disetujui, serta tanggal jendela pemutakhiran RPD. Data workbook STTAL dan instalasi XAMPP juga perlu diproses di lingkungan pengguna untuk memastikan hasil akhir.

Belum ada database yang dibuat dan belum ada uji jalan aplikasi yang dilakukan pada XAMPP pengguna.
