# Pemetaan field sumber SIMORA

Dokumen ini mencatat cara membaca empat workbook yang dianalisis. Nomor baris/kolom merujuk pada file contoh; file ekspor versi lain tetap perlu diperiksa sebelum diproses.

## SAKTI — Rincian Kertas Kerja

- Sheet `RKK_MULTIYEAR_SATKER`; data berupa laporan bertingkat tanpa header tabel tunggal.
- Kode berada di kolom A; uraian umumnya di kolom D atau E.
- Nilai anggaran umumnya di kolom J, tetapi pada beberapa baris berpindah ke kolom K. Parser mengambil J lalu memakai K jika J kosong/tidak dapat dibaca sebagai angka.
- Kode memuat hierarki program, kegiatan, output, suboutput, komponen, subkomponen, akun, dan rincian. Nama uraian serta nomor baris sumber ikut disimpan.
- Tahun anggaran tidak dibaca otomatis dari file ini. Parameter tahun diberikan terpisah; file contoh dicocokkan ke TA 2026 berdasarkan kecocokan total dengan POK dan Laporan Fa.

## SAKTI — Form Cetak POK

- Sheet `UC_ANG_6.4`; data utama mulai baris 8, dengan header berulang pada halaman cetak berikutnya.
- Kode/uraian berada di kolom B/C. Rincian item juga dapat muncul pada baris tanpa kode.
- Kebutuhan dana bulanan berada pada kolom R, S, T, U, V, W, Y, AA, AB, AC, AD, dan AF untuk Januari–Desember.
- Total berada di AL, blokir di AM. Nilai sumber dinyatakan dalam ribuan rupiah; hasil normalisasi harus mengalikannya 1.000 ketika dibandingkan dengan rupiah penuh.
- `NAMA PPK UMUM` dan `NAMA PPK` disimpan sebagai data sumber. Keduanya tidak otomatis dipakai sebagai pembuat PJK.

## SAKTI — Laporan Fa Detail (16 Segmen)

- Sheet `GLP039_LAPORAN REALISASI SUPER`; header laporan berada pada baris 7–8 dan data hierarkis mulai baris 9.
- Kode dan uraian berada di kolom yang berbeda menurut tingkat: program/kegiatan B, uraian D/I; output/suboutput C, uraian G/K; komponen E/J; subkomponen F/L; akun H/M.
- Nilai pagu revisi di P, lock pagu di R, realisasi periode lalu di V, periode ini di W, realisasi s.d. periode di Y, persentase di AB, dan sisa anggaran di AD untuk baris rincian. Baris total laporan memakai posisi sel yang berbeda dan tidak dimasukkan sebagai baris rincian.
- Periode dan identitas satker dibaca dari bagian judul laporan.

## MyINTRESS — Detail Pengeluaran dan Potongan SPP/SPM/SP2D

- Sheet `Data`; dua baris judul diikuti ruang kosong, header terdapat pada baris 5, dan rincian mulai baris 6.
- Kolom berisi kode satker, nomor SPP/SPM, nomor SP2D, tanggal SP2D, COA 16 segmen, valuta/kurs, akun dan nilai pengeluaran, serta akun dan nilai potongan.
- Nilai teks dengan titik pemisah ribuan dipertahankan sebagai nilai sumber dan diparsing sebagai angka rupiah untuk kebutuhan hitung.
- COA 16 segmen dan nomor dokumen harus disimpan sebagai teks. Kecocokan MyINTRESS terhadap agregat realisasi SAKTI belum dibuktikan oleh file contoh.

## Kunci hierarki lintas sumber

Pada contoh data, 57 jalur sampai akun dapat dipadankan menggunakan seluruh urutan tingkat dan uraian. Setelah nominal POK dikonversi dari ribuan ke rupiah, nilai anggarannya sama pada Rincian Kertas Kerja, POK, dan Laporan Fa untuk semua 57 jalur tersebut.

Bentuk kode sumber berbeda antar laporan: misalnya program RKK/POK `012.23.AB` tampil sebagai `AB` di Laporan Fa; kegiatan `6513` tampil sebagai `AB.6513`; subkomponen `A` tampil sebagai `003.0A`. Karena itu, SIMORA perlu menyimpan kode asal dan jalur hierarki sumber serta menghubungkannya ke hierarki internal yang sama. Jangan mencocokkan berdasarkan kode akun saja karena akun yang sama dapat muncul pada beberapa komponen.
