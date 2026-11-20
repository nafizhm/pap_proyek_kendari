# PAP Kendari Proyek

Salinan aplikasi `nazura_proyek`, menggunakan koneksi database aplikasi `pap_kendari`.

- Semua 68 tabel yang sudah ada dipertahankan, termasuk struktur, data, akun, lokasi, kavling, konfigurasi, menu dan hak akses aplikasi PAP.
- Ditambahkan 27 tabel proyek. Master barang (77), satuan (18), level user (4), dan menu proyek (35) disalin dari sumber. Riwayat transaksi dan aktivitas dimulai kosong.
- Akun dan password menggunakan `users` PAP. Admin aktif dengan `id_role = 1` memperoleh akses awal menu proyek. Pengaturan hak akses proyek memakai `hak_akses_proyek`, terpisah dari hak akses aplikasi PAP.
- Relasi lokasi memakai `lokasi_kavling.id`, kavling memakai `kavling_peta.id`, dan role memakai `users.id_role` serta tabel `role` PAP.
- Informasi penandatangan nota unit mengikuti perusahaan yang terhubung ke lokasi melalui `lokasi_kavling_perusahaan`. Nota umum memakai konfigurasi PAP; tanda tangan dibiarkan kosong bila tidak ada lokasi.
- Session dan cache memakai file serta cookie tersendiri. Antrean memakai `sync`.

Jalankan melalui Laragon pada `http://pap_kendari_proyek.test/admin/login`, atau `php artisan serve` lalu buka alamat yang ditampilkan.

## Migrasi dan backup

Migrasi aktif hanya migrasi integrasi tambahan. Riwayatnya memakai tabel `migrations_proyek`; riwayat migrasi PAP tetap utuh. Migrasi Nazura lama disimpan di `database/legacy-migrations` karena berisi penghapusan tabel/menu dan tidak cocok dijalankan ke database bersama.

Snapshot struktur tabel proyek ada di `database/project-schema.json`. Migrasi melewati tabel yang sudah ada. Rollback otomatis dinonaktifkan untuk menjaga data operasional.

Backup JSON lengkap sebelum integrasi tersimpan secara lokal di `storage/app/private/database-backups/`. File ini mengandung data database dan tidak masuk version control. Jangan menjalankan migrasi fresh/reset, seeder sumber, atau migrasi legacy pada database bersama.

## Verifikasi

```powershell
php scripts/verify-pages.php
php scripts/verify-pages.php --ajax
php scripts/verify-database-preservation.php
php artisan test
```

Pengecekan halaman memakai akun admin PAP yang sudah ada, tanpa mengubah password atau membuat transaksi. Pengecekan preservasi membandingkan tabel lama dengan backup lokal; setelah ada transaksi operasional, perubahan data yang sah akan ikut terdeteksi.
