# Hasil pembacaan awal file sumber SIMORA

Parser membaca empat workbook yang sudah dibahas dan menyimpan data rinci sementara sebagai JSON untuk pemeriksaan lanjutan.

| Sumber | Baris yang dibaca parser | Catatan struktur |
|---|---:|---|
| Rincian Kertas Kerja Satker | 337 | Workbook tidak menyatakan tahun dengan jelas. Tahun 2026 diberikan sebagai parameter untuk membandingkan contoh ini. |
| Form Cetak POK | 272 | Header cetak yang berulang di halaman lanjutan dan baris total dikesampingkan; nilai kebutuhan dana disimpan per bulan dalam satuan ribuan rupiah. |
| Laporan Fa Detail (16 Segmen) | 144 | Baris total keseluruhan dan catatan kaki tidak dihitung sebagai rincian anggaran. |
| Detail Pengeluaran dan Potongan SPP/SPM/SP2D | 16 | Header ditemukan di baris 5; baris judul dan ruang kosong di atasnya diabaikan. |

Pemeriksaan lintas sumber menemukan 57 jalur hierarki akun yang sama pada Rincian Kertas Kerja, POK, dan Laporan Fa Detail. Setelah nominal POK dikonversi dari ribuan ke rupiah, nilai anggaran pada semua 57 jalur tersebut cocok persis antara ketiga file.

Total tingkat atas juga cocok: jumlah pagu program Rincian Kertas Kerja adalah Rp18.137.854.000; total POK adalah Rp18.137.854 ribu (setara Rp18.137.854.000); pagu revisi seluruhnya pada Laporan Fa Detail adalah Rp18.137.854.000. Hasil ini mendukung relasi data SAKTI untuk contoh TA 2026. Nilai RPD bulanan tetap merupakan distribusi rencana dan tidak disamakan dengan pagu tahunan.

Parser tidak melaporkan masalah struktur pada pembacaan ini. Itu berarti pola lembar dan kolom yang diharapkan ditemukan; **belum berarti seluruh isi keuangan sudah direkonsiliasi atau disetujui**. Pemetaan lintas sumber perlu memakai jalur hierarki, karena bentuk kodenya berbeda antar laporan (misalnya kode program ditampilkan lengkap di RKK/POK tetapi hanya segmen akhirnya pada Laporan Fa). Pada MyINTRESS, waktu unduh tercatat 26 September 2026 sementara tanggal SP2D pada baris contoh adalah 1 Oktober 2026; tanggal sumber dipertahankan apa adanya dan perlu ditinjau sebagai kondisi data.

JSON sementara berada di folder kerja internal `work/simora_import_preview/` dan tidak dimasukkan ke paket keluaran karena berisi rincian sumber keuangan.
