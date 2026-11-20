# Rancangan izin akses aplikasi

Status: belum diterapkan. Tidak ada perubahan pada database, login, atau akun aktif.

## Hasil pada pengaturan pengguna

Pada formulir Tambah/Edit Pengguna, tambahkan bagian **Izin akses aplikasi**:

- [ ] Penjualan
- [ ] Proyek
- [ ] Keuangan

Seorang pengguna boleh memiliki beberapa izin. Tidak memilih izin berarti pengguna tidak dapat masuk ke aplikasi mana pun yang memeriksa izin ini. Formulir edit menampilkan nilai yang tersimpan; melepas centang harus menyimpan nilai false.

Tambahkan kolom **Akses aplikasi** pada daftar pengguna agar administrator dapat melihat izin tanpa membuka formulir edit.

## Penyimpanan bersama

Tambahkan tiga kolom boolean pada tabel `pap_kendari.users`:

| Kolom | Nilai bawaan akun baru |
| --- | --- |
| `akses_penjualan` | false |
| `akses_proyek` | false |
| `akses_keuangan` | false |

Tidak mengubah password, role, data penjualan/proyek, atau hak akses menu yang sudah ada. Tidak menjalankan migration fresh/reset atau seeder sumber. Ambil backup tabel pengguna sebelum perubahan skema.

## Akses awal tiga akun yang sudah ada

Pemeriksaan database menemukan tepat tiga akun aktif. Pengaturan awal berikut mempertahankan akses administrator yang sudah ada dan membatasi akun proyek ke aplikasi proyek:

| Username | Penjualan | Proyek | Keuangan |
| --- | --- | --- | --- |
| master | Ya | Ya | Tidak |
| lia | Ya | Ya | Tidak |
| proyek | Tidak | Ya | Tidak |

Tidak memberikan izin otomatis berdasarkan role untuk akun yang dibuat berikutnya. Keuangan tidak dicentang otomatis karena khusus aplikasi keuangan tersendiri, sesuai arahan pengguna.

## Pemeriksaan login dan sesi

| Aplikasi | Izin wajib |
| --- | --- |
| `pap_kendari` | `akses_penjualan` |
| `pap_kendari_proyek` (nama folder kerja aplikasi proyek) | `akses_proyek` |
| Aplikasi keuangan tersendiri | `akses_keuangan` ketika aplikasi tersebut diintegrasikan |

Pada kedua aplikasi yang tersedia, periksa status aktif dan izin sebelum membentuk sesi login. Akun dengan password benar tetapi tanpa centang aplikasi harus ditolak dengan pesan: **Akun Anda tidak memiliki izin akses aplikasi [Penjualan/Proyek]. Hubungi administrator.**

Tambahkan middleware untuk membaca izin terbaru pada setiap permintaan yang sudah login. Jika izin dicabut atau akun diblokir, keluarkan pengguna, batalkan sesi, dan minta login kembali. Pemeriksaan ini berlaku juga untuk URL langsung dan AJAX. Tidak ada pengecualian izin berdasarkan role Admin atau password khusus.

Perubahan centang harus dibatasi pada pengguna yang memiliki hak mengelola pengguna, diperiksa di server. Pengguna tidak boleh menambahkan izin sendiri melalui perubahan profil atau permintaan HTTP buatan.

Hak akses aplikasi hanya menentukan boleh masuk aplikasi. Hak akses menu dan tindakan tetap ditentukan oleh `hak_akses` dan `hak_akses_proyek` masing-masing.

## Cakupan file implementasi

Dalam `pap_kendari_proyek`:

- Migrasi tambahan tiga kolom, tanpa menghapus kolom/tabel lama.
- Model `User`: boolean cast dan pemeriksaan izin.
- Controller login: tolak akun tanpa izin Proyek.
- Middleware dan pendaftaran middleware: putus sesi setelah pencabutan izin.
- Controller serta view pengaturan pengguna: validasi, simpan centang, tampilkan izin, dan otorisasi perubahan.
- Template login: tampilkan alasan sesi ditolak.
- Pengujian otomatis memakai SQLite in-memory agar data bersama tidak berubah.

Dalam `D:\2_laravel_project\pap_kendari`:

- Model `User` dan `PengaturanPengguna`: dukungan tiga kolom yang sama.
- Controller login dan middleware: wajib izin Penjualan.
- Controller dan view pengaturan pengguna: dukungan tambah/edit tiga centang dan otorisasi di server.
- Formulir profil pribadi tidak menerima perubahan izin aplikasi.

Folder aplikasi keuangan belum ditentukan; pada tahap ini hanya simpan izin Keuangan, tanpa menganggap izin tersebut berlaku di Penjualan atau Proyek.

## Pengujian yang harus lulus sebelum dinyatakan selesai

1. Penjualan saja tidak bisa login ke aplikasi Proyek, termasuk dengan password yang benar.
2. Proyek saja tidak bisa login ke aplikasi Penjualan.
3. Keuangan saja tidak bisa login ke kedua aplikasi tersebut.
4. Dua centang Penjualan dan Proyek mengizinkan login ke kedua aplikasi.
5. Akun tanpa centang atau diblokir ditolak.
6. Admin yang centang aplikasinya dicabut juga ditolak.
7. Melepas centang pada formulir edit tersimpan dan memutus akses sesi lama pada permintaan berikutnya.
8. Akun tanpa hak mengelola pengguna tidak bisa mengubah centang melalui URL langsung.
9. Ketiga akun lama memperoleh tepat izin awal pada tabel di atas.
10. Password dan hak akses menu lama tidak berubah.
