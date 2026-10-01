# Panduan menjalankan SIMORA

## 1. Persiapan satu kali

1. Ekstrak `SIMORA-paket.zip`, lalu salin folder hasil ekstrak bernama `SIMORA` ke `C:\xampp\htdocs\SIMORA` melalui File Explorer. Folder lama `simora-sttal` tidak perlu diubah.
2. Buka **XAMPP Control Panel**, lalu klik **Start** pada baris **Apache** dan **MySQL**. Keduanya perlu berstatus aktif.
3. Buka `http://localhost/phpmyadmin`, pilih **New**, buat database bernama `simora` dengan collation `utf8mb4_unicode_ci`, lalu pilih database itu dan buka tab **Import**. Pilih `database/schema.sql` dari folder SIMORA dan klik **Import/Go**.
4. Paket untuk komputer ini sudah memuat konfigurasi database XAMPP standar (`root`, tanpa kata sandi) serta lokasi Python yang terdeteksi. Jika akun MySQL `root` di komputer ini memakai kata sandi, ubah `password` pada `config/database.local.php`.
5. Buka `http://localhost/SIMORA/public/`.

Jika database `simora` sudah dibuat dan berisi tabel SIMORA dari paket sebelumnya, jangan impor ulang `schema.sql`. Pilih database itu di phpMyAdmin, buka **Import**, lalu jalankan `database/migrations/001_app_users.sql` untuk menambahkan tabel login.

Pada pembukaan pertama, SIMORA menampilkan halaman pembuatan **akun administrator**. Buat nama pengguna dan kata sandi minimal 12 karakter. Setelah akun dibuat, halaman masuk akan menjadi gerbang untuk dashboard dan semua modul. Tidak ada kata sandi bawaan.

Konfigurasi lokal dan file sumber dikecualikan dari Git melalui `.gitignore`. Folder `config` dan `storage` juga ditutup dari akses web Apache. Jangan mengunggah `database.local.php`, `python.local.php`, `whatsapp.local.php`, atau data Excel sumber ke GitHub.

## 2. Impor data

Impor satu workbook setiap kali. Pilih sumber, satker, tahun anggaran, dan bulan laporan bila diminta.

Urutan yang disarankan:

1. **SAKTI — Rincian Kertas Kerja** untuk struktur dan pagu DIPA.
2. **SAKTI — Form Cetak POK** untuk RPD/Halaman III DIPA.
3. **SAKTI — Laporan Fa Detail** untuk realisasi menyeluruh. Bulan laporan wajib dipilih.
4. **MyINTRESS — Detail SPP/SPM/SP2D** sebagai bahan penelusuran transaksi.

Setelah setiap file dibaca, buka batch di **Tinjau impor**. Periksa beberapa halaman dan catatan parser, lalu centang persetujuan untuk memproses seluruh batch. Data baru masuk ke tampilan monitoring setelah diterima dan dinormalisasi.

## 3. Pengaturan awal

- Di **Pengaturan**, kelompok D3, S1, dan S2 sudah disediakan. Pilih komponen DIPA yang sesuai untuk tiap kelompok; pemetaan ini bisa diedit dan memiliki tanggal berlaku.
- Petakan Pembuat PJK ke komponen yang menjadi tugasnya. SIMORA hanya mencatat penugasan, bukan menentukan siapa yang mengerjakan PJK.
- Aktifkan pilihan notifikasi SIMORA hanya untuk penerima yang memang akan menerima pesan otomatis. Masukkan nomor dalam format Indonesia, misalnya `+62812...`.
- Pemetaan belanja otomatis memakai awalan akun 51/52/53. Tinjau daftar akun dan koreksi pemetaan jika diperlukan. Akun lain tetap terlihat sebagai belum dikategorikan.
- Jendela pemutakhiran RPD tidak diisi otomatis. Masukkan hari awal dan akhir sesuai jadwal yang berlaku di satker; biarkan kosong bila belum dipastikan.

## 4. Melihat hasil

- **Dashboard** menampilkan realisasi kumulatif per jenis belanja dan perbandingan RPD kumulatif dengan realisasi sampai bulan Laporan Fa yang dipilih.
- GAP adalah realisasi dikurangi RPD. GAP (%) dihitung terhadap RPD kumulatif. Peringatan muncul bila nilai absolut GAP (%) lebih dari ambang yang ditetapkan (awal 5%).
- Bila akun RPD dan realisasi belum berpasangan, SIMORA menampilkan catatan pemadanan dan menahan status GAP final.
- **Penelusuran MyINTRESS** mencari nomor SPP/SPM, SP2D, COA 16 segmen, dan akun transaksi. MyINTRESS mendukung penelusuran; angka realisasi utama tetap dari SAKTI.

## 5. Pengingat dan WhatsApp

1. Tambahkan tanggal libur yang perlu diperhitungkan di **Pengaturan**. Pengingat awal bulan dijadwalkan pada tanggal 1 pukul 08.00 WIB dan bergeser ke hari kerja berikutnya bila opsi tersebut aktif.
2. Di **Antrean pemberitahuan**, pilih **Siapkan antrean sekarang** untuk menghitung monitoring terbaru dan menyiapkan pesan. Tindakan ini tidak mengirim pesan.
3. Pengiriman WhatsApp memakai WhatsApp Business Cloud API dan template yang disetujui. Salin `config/whatsapp.example.php` menjadi `config/whatsapp.local.php`; isi versi API, Phone Number ID, nama template, bahasa, dan token akses. File lokal ini dikecualikan dari Git; jangan menyalin token ke GitHub.
4. Pengiriman sengaja nonaktif secara awal. Setelah akun, token, dan template siap, aktifkan `enabled` pada konfigurasi lokal. Tombol **Kirim pesan yang sudah jatuh tempo** mengirim maksimal 20 pesan per tindakan. Periksa status dan pesan gagal di halaman antrean.
5. Untuk otomatisasi harian, jadwalkan `bin/queue_notifications.php` setelah waktu pengingat dan `bin/send_notifications.php` beberapa menit sesudahnya menggunakan Windows Task Scheduler. Jangan jadwalkan pengiriman sebelum konfigurasi layanan WhatsApp selesai.

Contoh program yang dipilih pada Task Scheduler bila folder berada di lokasi standar:

```text
Program: C:\xampp\php\php.exe
Argumen: C:\xampp\htdocs\SIMORA\bin\queue_notifications.php
```

Untuk tugas kedua, gunakan argumen `C:\xampp\htdocs\SIMORA\bin\send_notifications.php`.

## Batas tahap ini

Folder ini berisi prototipe lokal. Sebelum dipakai sebagai laporan resmi, tinjau pemetaan akun dari workbook satker dan pastikan kelengkapan pemadanan. Pengiriman WhatsApp nyata memerlukan akun bisnis, token, dan template dari penyedia. Aplikasi belum dijalankan atau diverifikasi pada instalasi XAMPP pengguna.
