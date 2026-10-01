# SIMORA — Fondasi basis data

Fondasi tahap pertama ini mengikuti keputusan desain yang telah dibahas:

- SAKTI menjadi sumber utama untuk anggaran, POK/RPD, dan realisasi.
- MyINTRESS menjadi sumber pendukung untuk penelusuran SPP/SPM/SP2D dan rekonsiliasi.
- Kelompok anggaran (misalnya D3, S1, S2) dikelola sebagai konfigurasi, sehingga admin dapat menambah, mengubah, atau menonaktifkannya.
- PJK berarti berkas Pertanggungjawaban Keuangan. Pembuat PJK adalah orang yang dapat ditugaskan ke komponen anggaran.
- Penugasan pembuat PJK bersifat opsional dan memiliki tanggal efektif untuk mencatat pergantian. Hanya pembuat yang didaftarkan untuk notifikasi SIMORA yang menjadi penerima notifikasi otomatis.
- GAP dihitung dari realisasi kumulatif dikurangi RPD kumulatif; GAP (%) = GAP dibagi RPD kumulatif. Peringatan dashboard muncul jika nilai absolut GAP (%) melebihi 5%, baik positif maupun negatif.
- Pengingat awal bulan dijadwalkan tanggal 1 pukul 08.00 WIB dan bergeser ke hari kerja berikutnya bila perlu.

## Isi

- `database/schema.sql` — tabel MySQL untuk autentikasi, impor, staging baris sumber, hierarki anggaran, RPD bulanan, realisasi SAKTI, kelompok anggaran, penugasan pembuat PJK, rincian MyINTRESS, rekonsiliasi, monitoring, dan log notifikasi.
- `database/migrations/001_app_users.sql` — migrasi login untuk instalasi lama yang database-nya sudah dibuat sebelum penambahan halaman masuk.
- `app/Web.php` — sesi aman, pembatasan akses lokal, autentikasi, navigasi, dan layout aplikasi bersama.
- `public/setup.php`, `public/login.php`, dan `public/logout.php` — pembuatan admin pertama, halaman masuk, dan keluar.
- `public/assets/app.css` — tampilan responsif bersama untuk login, navigasi, dashboard, tabel, formulir, dan modul lain.
- `app/ImportBatchService.php` — layanan PHP yang menyimpan hasil parser ke tabel penampung dalam satu transaksi dan menghindari impor ganda berdasarkan hash file.
- `app/Database.php` dan `config/database.example.php` — sambungan PDO MySQL dan contoh konfigurasi lokal.
- `public/index.php` — halaman lokal untuk membaca workbook dan memasukkan baris hasil parser ke tabel penampung; modul aplikasi lainnya mewajibkan login.
- `public/dashboard.php` — ringkasan realisasi kumulatif dari batch Laporan Fa Detail SAKTI yang sudah dinormalisasi; menampilkan total dan pecahan Pegawai, Barang, Modal, serta akun yang belum dikategorikan.
- `public/transactions.php` — pencarian rincian MyINTRESS berdasarkan nomor SPP/SPM, SP2D, COA 16 segmen, dan akun transaksi.
- `public/notifications.php` — melihat antrean pesan, menyiapkan pengingat/peringatan, dan mengirim pesan jatuh tempo bila WhatsApp telah dikonfigurasi.
- `public/review.php` — pratinjau bertahap dan langkah persetujuan sebelum baris penampung dinormalisasi ke tabel anggaran/realisasi.
- `public/settings.php` — pengelolaan pemetaan kode akun ke kelompok belanja dashboard, kelompok anggaran, cakupan komponen DIPA, pembuat PJK SIMORA, dan masa berlaku penugasan.
- `app/ImportNormalizer.php` — pemetaan baris yang diterima ke hierarki dan tabel finansial; mempertahankan kode sumber pada tabel rujukan.
- `app/MonitoringService.php` — menghitung realisasi kumulatif dan RPD kumulatif per komponen serta menyimpan kelengkapan pemadanan dan status ambang GAP.
- `app/NotificationQueueGenerator.php` dan `bin/queue_notifications.php` — memperbarui hasil monitoring terbaru lalu membuat antrean pesan yang tidak berulang.
- `app/NotificationDispatcher.php`, `app/WhatsAppCloudApi.php`, dan `bin/send_notifications.php` — pengiriman antrean lewat template WhatsApp Business setelah konfigurasi lokal diaktifkan.
- `importer/parse_sources.py` — pembaca awal empat format sumber yang mempertahankan nomor baris dan nilai asli dalam keluaran JSON untuk ditinjau sebelum pemuatan ke basis data.
- `IMPORT_PREVIEW.md` — ringkasan jumlah baris yang berhasil dibaca dari workbook contoh.
- `SOURCE_FIELD_MAPPING.md` — lokasi field dan perbedaan struktur pada empat workbook sumber.
- `PEMBAGIAN_MODUL.md` — pembagian pekerjaan dan status tahap prototipe.
- `PANDUAN_PENGGUNA.md` — langkah memasang, mengimpor data, mengatur, dan menjalankan SIMORA.

## Catatan

Parser menghasilkan JSON internal yang ditinjau sebelum baris disimpan dan dinormalisasi melalui halaman web. Header laporan bertingkat dan baris subtotal tetap harus diperiksa sebelum data dijadikan laporan resmi. Nilai sumber dan hasil normalisasi disimpan terpisah, terutama untuk nominal POK yang dinyatakan dalam ribuan rupiah. Bila RPD kumulatif nol, dashboard tidak membuat persentase GAP dan menandai kondisi tersebut.

## Kebutuhan dashboard yang sudah disepakati

- Dashboard menampilkan realisasi belanja yang bersumber dari SAKTI dan dapat diuraikan menjadi Belanja Pegawai, Belanja Barang, serta Belanja Modal.
- Klasifikasi awal memakai awalan akun 51, 52, dan 53; admin dapat mengubah klasifikasi tiap kode akun untuk satker dan tahun anggaran. Kode lain tetap dihitung sebagai total dan terlihat pada kelompok belum dikategorikan.
- Pengelompokan menggunakan kode akun pada hierarki anggaran SAKTI, bukan rincian MyINTRESS. MyINTRESS tetap menjadi data pendukung penelusuran SPP/SPM/SP2D.
- Pemetaan kode akun ke kelompok belanja perlu disimpan sebagai konfigurasi yang dapat ditinjau dan diperbarui. Kode yang belum dipetakan tetap dihitung dalam total realisasi dan ditandai agar dapat ditindaklanjuti.
- Pemetaan otomatis awalan akun perlu ditinjau terhadap daftar akun dari workbook satker sebelum angka dashboard dipakai sebagai laporan resmi; perubahan dapat dilakukan melalui Pengaturan.
- Perbandingan RPD dan realisasi memakai kode akun/hierarki internal yang sama. Jika masih ada akun dari salah satu sumber yang belum berpasangan, dashboard menahan nilai GAP final dan menunjukkan jumlah akun yang perlu diperiksa.

Contoh pemakaian:

```text
python importer/parse_sources.py rkk path/to/RINCIAN-KERTAS-KERJA.xlsx work/rkk.json --fiscal-year 2026
python importer/parse_sources.py pok_rpd path/to/Form-Cetak-POK.xlsx work/pok.json
python importer/parse_sources.py fa_realisasi path/to/Laporan-Fa-Detail.xlsx work/realisasi.json
python importer/parse_sources.py myintress path/to/Detail-SPP-SPM-SP2D.xlsx work/myintress.json
```

## Menjalankan halaman impor lokal

Ikuti langkah lengkap pada [PANDUAN_PENGGUNA.md](PANDUAN_PENGGUNA.md). Ringkasnya: letakkan folder di `htdocs`, jalankan Apache dan MySQL, buat database `simora`, impor `database/schema.sql`, lalu buka `http://localhost/SIMORA/public/` untuk membuat akun admin dan masuk. Tampilan login dan semua modul memakai CSS responsif lokal tanpa dependensi font atau ikon daring.

Halaman hanya menerima koneksi lokal. File yang diunggah disimpan di `storage/private`, sedangkan hasil baca ditaruh di `import_source_rows` dengan status tinjauan. Dashboard menampilkan realisasi dan GAP (%) setelah Laporan Fa Detail serta POK/RPD ditinjau dan dinormalisasi. Nilai GAP ditahan bila pemadanan akun belum lengkap; peringatan di dashboard memakai batas absolut lebih dari 5%. Kode antrean dan pengiriman pesan tersedia, tetapi akun/token/template WhatsApp serta Windows Task Scheduler masih harus dikonfigurasi di komputer pengguna.
