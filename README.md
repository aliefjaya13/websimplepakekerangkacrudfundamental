# Sistem Piket Adiwiyata

Aplikasi CRUD sederhana untuk jadwal piket kelas XI RPL 1 SMKN 1 Probolinggo.

## Kebutuhan

- PHP dengan ekstensi `mysqli`
- MySQL atau MariaDB

## Menyiapkan database

1. Buat database bernama `tbsiswa`.
2. Jalankan migration SQL dalam urutan nama file di `Database/migration/crud-fundamental/database/migration/`:
	- `2025_01_create_table_tbsiswa.sql`
	- `2026_10_add_no_absen_to_tbsiswa.sql`
	- `2026_10_add_piket_attendance_columns.sql`
3. Buat user database dan berikan hak akses ke database `tbsiswa`.

## Konfigurasi

`config.php` membaca konfigurasi dari environment agar kredensial database tidak disimpan di repository:

```sh
export DB_HOST=localhost
export DB_USER=dev
export DB_PASS='password-database-anda'
export DB_NAME=tbsiswa
```

Sesuaikan nilainya dengan konfigurasi MySQL lokal.

## Menjalankan aplikasi

Jalankan dari direktori proyek:

```sh
php -S 127.0.0.1:8000
```

Buka `http://127.0.0.1:8000/index.php`.

Daftar siswa aktif tidak disertakan dalam repository; masukkan data kelas melalui form atau query SQL lokal.
