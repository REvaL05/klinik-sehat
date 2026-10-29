# Dokumentasi Tugas Besar Basis Data
## Sistem Informasi Manajemen Klinik dan Pendaftaran Pasien — Klinik Sehat

## 1. Deskripsi sistem
Klinik Sehat adalah aplikasi web sederhana untuk mengelola data pasien, dokter, pendaftaran kunjungan, pemeriksaan, dan pembayaran. Aplikasi dibuat menggunakan HTML, CSS, JavaScript, PHP, dan MySQL.

## 2. Kesesuaian dengan ketentuan tugas
- **Front-end:** HTML untuk struktur halaman, CSS untuk tampilan responsif, JavaScript untuk interaksi dan konfirmasi penghapusan serta hitung total pembayaran.
- **Back-end:** PHP dengan PDO untuk membaca dan mengolah data.
- **Database:** MySQL.
- **Tabel berelasi:** pasien, dokter, pendaftaran, pemeriksaan, pembayaran.
- **Primary key:** id pada setiap tabel.
- **Foreign key:** pendaftaran.id_pasien → pasien.id_pasien; pendaftaran.id_dokter → dokter.id_dokter; pemeriksaan.id_pendaftaran → pendaftaran.id_pendaftaran; pembayaran.id_pendaftaran → pendaftaran.id_pendaftaran.
- **CRUD:** fitur tambah, lihat, edit, dan hapus tersedia pada menu data pasien, dokter, pendaftaran, pemeriksaan, dan pembayaran.
- **Pencarian:** tersedia pada daftar pasien, dokter, dan pendaftaran.
- **Deployment:** aplikasi dapat diunggah ke hosting yang mendukung PHP dan MySQL. Petunjuk ada pada bagian 7.

## 3. Struktur tabel
### pasien
- `id_pasien` — Primary Key, auto increment
- `no_rm` — nomor rekam medis, unik
- `nik` — nomor identitas, unik dan boleh kosong
- `nama_pasien`, `jenis_kelamin`, `tanggal_lahir`, `no_hp`, `alamat`

### dokter
- `id_dokter` — Primary Key, auto increment
- `nama_dokter`, `spesialisasi`, `no_hp`

### pendaftaran
- `id_pendaftaran` — Primary Key, auto increment
- `kode_pendaftaran` — unik
- `id_pasien` — Foreign Key ke `pasien`
- `id_dokter` — Foreign Key ke `dokter`
- `tanggal_kunjungan`, `keluhan`, `status`

### pemeriksaan
- `id_pemeriksaan` — Primary Key, auto increment
- `id_pendaftaran` — Foreign Key dan unik, sehingga satu pendaftaran memiliki maksimal satu catatan pemeriksaan
- `diagnosa`, `tindakan`, `resep_obat`, `catatan`, `tanggal_pemeriksaan`

### pembayaran
- `id_pembayaran` — Primary Key, auto increment
- `id_pendaftaran` — Foreign Key dan unik, sehingga satu pendaftaran memiliki maksimal satu catatan pembayaran
- `biaya_konsultasi`, `biaya_obat`, `total_biaya`, `metode_pembayaran`, `status_pembayaran`, `tanggal_pembayaran`

## 4. Kardinalitas relasi
1. **PASIEN 1 : M PENDAFTARAN** — satu pasien dapat melakukan banyak pendaftaran; satu pendaftaran hanya mengacu pada satu pasien.
2. **DOKTER 1 : M PENDAFTARAN** — satu dokter dapat menangani banyak pendaftaran; satu pendaftaran hanya mengacu pada satu dokter.
3. **PENDAFTARAN 1 : 0..1 PEMERIKSAAN** — pendaftaran boleh belum memiliki catatan pemeriksaan atau memiliki satu catatan pemeriksaan.
4. **PENDAFTARAN 1 : 0..1 PEMBAYARAN** — pendaftaran boleh belum memiliki catatan pembayaran atau memiliki satu catatan pembayaran.

Sumber ERD: `database/ERD.mmd`. ERD harus konsisten dengan tabel yang benar-benar dibuat pada `database/klinik_sehat.sql`. Entitas OBAT terpisah tidak dimasukkan ke versi kode ini; informasi resep saat ini dicatat sebagai teks di tabel pemeriksaan.

## 5. Contoh query untuk demonstrasi SQL
Jalankan query berikut setelah mengimpor `database/klinik_sehat.sql`.

```sql
USE klinik_sehat;

-- READ: menampilkan pasien
SELECT * FROM pasien ORDER BY nama_pasien;

-- CREATE: menambahkan pasien
INSERT INTO pasien (no_rm, nama_pasien, jenis_kelamin, no_hp, alamat)
VALUES ('RM-TEST-01', 'Pasien Demo', 'Perempuan', '081200000001', 'Alamat demo');

-- UPDATE: mengubah nomor HP
UPDATE pasien
SET no_hp = '081200000002'
WHERE no_rm = 'RM-TEST-01';

-- READ: melihat relasi pendaftaran, pasien, dan dokter
SELECT p.kode_pendaftaran, ps.nama_pasien, d.nama_dokter,
       p.tanggal_kunjungan, p.status
FROM pendaftaran p
JOIN pasien ps ON ps.id_pasien = p.id_pasien
JOIN dokter d ON d.id_dokter = p.id_dokter;

-- DELETE: hapus data demo jika belum terhubung ke pendaftaran
DELETE FROM pasien WHERE no_rm = 'RM-TEST-01';
```

Catatan: jangan menghapus pasien atau dokter yang masih direferensikan pendaftaran. Foreign key sengaja mencegah penghapusan yang merusak riwayat.

## 6. Langkah pengujian CRUD
1. **Create:** tambah pasien dan dokter dari formulir.
2. **Read:** pastikan data muncul di tabel dan dashboard.
3. **Update:** tekan Edit, ubah informasi, lalu simpan.
4. **Delete:** hapus data uji yang belum mempunyai relasi; konfirmasi penghapusan.
5. Buat pendaftaran dan pastikan nama pasien serta dokter muncul sesuai relasi.
6. Tambahkan pemeriksaan dan pembayaran untuk pendaftaran yang sama.
7. Periksa apakah dashboard dan status berubah setelah data dibuat.

## 7. Menjalankan lokal dan deployment online
### Lokal dengan Laragon
1. Ekstrak folder ke `C:\\laragon\\www\\klinik_sehat_project`.
2. Jalankan Apache dan MySQL.
3. Buka phpMyAdmin, impor `database/klinik_sehat.sql`.
4. Periksa konfigurasi pada `config/database.php`.
5. Akses `http://localhost/klinik_sehat_project/`.

### Deployment online
1. Pilih hosting yang mendukung PHP dan MySQL serta menyediakan HTTPS.
2. Buat database MySQL dan user database melalui panel hosting.
3. Impor isi `database/klinik_sehat.sql` melalui phpMyAdmin hosting.
4. Unggah isi folder proyek ke document root/subfolder situs melalui File Manager atau SFTP.
5. Sesuaikan host, nama database, username, dan password pada `config/database.php` dengan kredensial hosting. Jangan mengunggah kredensial ke repositori publik.
6. Aktifkan HTTPS, uji semua menu CRUD, relasi, dan unggah ulang jika ada perbaikan.
7. Pastikan backup database tersedia dan hapus data contoh sebelum demonstrasi publik.

## 8. Batasan dan keamanan
Ini adalah prototipe akademik, bukan aplikasi rekam medis siap produksi. Halaman admin belum memiliki autentikasi/login sungguhan. Sebelum digunakan untuk data nyata, perlu ditambahkan login dan otorisasi, perlindungan CSRF, validasi sisi server yang lebih lengkap, audit log, backup, HTTPS, dan pengaturan privasi. Gunakan hanya data fiktif untuk presentasi tugas.


## Login Admin dan Pasien (fitur tambahan)
- Buka `login.php` untuk memilih jenis akun Admin atau Pasien.
- Akun admin awal dibuat otomatis saat halaman login dibuka: username `admin`, password `AdminKlinik123!`. Untuk pemakaian nyata, ganti password awal.
- Pasien membuat akun melalui `register.php`. Sistem membuat baris di tabel `pasien` dan tabel `akun` dalam satu transaksi; `akun.id_pasien` menjadi foreign key ke `pasien.id_pasien`.
- Pasien login dan mengajukan kunjungan dari `portal_pasien.php`. Pendaftaran disimpan ke tabel `pendaftaran` dengan `id_pasien` akun yang login, sehingga otomatis muncul di halaman admin.
- Jika database sudah pernah dibuat sebelum fitur akun ditambahkan, jalankan ulang bagian `CREATE TABLE akun` dari `database/klinik_sehat.sql` atau impor SQL yang telah diperbarui. Backup database dahulu jika sudah berisi data penting.
