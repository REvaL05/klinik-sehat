# Klinik Sehat — Sistem Informasi Manajemen Klinik dan Pendaftaran Pasien

Aplikasi contoh tugas besar Basis Data menggunakan HTML, CSS, JavaScript, PHP, dan MySQL. Dirancang untuk dijalankan secara lokal dengan Laragon atau XAMPP.

## Fitur
- Dashboard ringkasan pasien, dokter, pendaftaran, dan antrean.
- CRUD data pasien (nomor rekam medis, NIK, identitas, kontak).
- CRUD data dokter dan spesialisasi.
- CRUD pendaftaran kunjungan dengan kode otomatis, pasien, dokter, tanggal, keluhan, dan status.
- CRUD catatan pemeriksaan: diagnosa, tindakan, resep, dan catatan.
- CRUD pembayaran, pilihan metode, status, dan perhitungan total.
- Pencarian pada daftar pasien, dokter, dan pendaftaran.
- Validasi input, prepared statements PDO, konfirmasi penghapusan, dan foreign key.

## Struktur database / ERD
- `pasien (1) —— (N) pendaftaran`
- `dokter (1) —— (N) pendaftaran`
- `pendaftaran (1) —— (0..1) pemeriksaan`
- `pendaftaran (1) —— (0..1) pembayaran`

Tabel: `pasien`, `dokter`, `pendaftaran`, `pemeriksaan`, `pembayaran`.

## Cara menjalankan di Laragon
1. Jalankan Laragon, lalu aktifkan Apache dan MySQL.
2. Ekstrak folder `klinik_sehat_project` ke `C:\laragon\www\`.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih tab **Import**, lalu impor file `database/klinik_sehat.sql`.
5. Jika username/password MySQL berbeda, sesuaikan `config/database.php`.
6. Buka `http://localhost/klinik_sehat_project/`.

## Cara menjalankan di XAMPP
1. Ekstrak folder ke `C:\xampp\htdocs\`.
2. Aktifkan Apache dan MySQL dari XAMPP Control Panel.
3. Impor `database/klinik_sehat.sql` melalui phpMyAdmin.
4. Buka `http://localhost/klinik_sehat_project/`.

## Alur demonstrasi yang disarankan
1. Buka dashboard dan jelaskan ringkasan.
2. Tambahkan pasien baru pada **Data Pasien**.
3. Tambahkan dokter pada **Data Dokter** bila diperlukan.
4. Buat pendaftaran baru pada **Pendaftaran**.
5. Tambahkan catatan pada **Pemeriksaan**.
6. Catat biaya pada **Pembayaran** dan ubah status menjadi Lunas.
7. Demonstrasikan Edit, Cari, dan Hapus pada data uji.

## Catatan akademik dan keamanan
- Data contoh adalah data fiktif untuk keperluan demonstrasi; jangan gunakan data pasien nyata.
- Ini adalah prototipe tugas, belum merupakan sistem rekam medis siap produksi.
- Sebelum dipakai di lingkungan nyata, tambahkan autentikasi dan otorisasi, CSRF protection, audit log, backup, HTTPS, validasi aturan klinis, dan kebijakan privasi yang sesuai.
- Akun admin pada tampilan saat ini hanya ilustrasi; belum ada fitur login.


## Login Admin dan Pasien (fitur tambahan)
- Buka `login.php` untuk memilih jenis akun Admin atau Pasien.
- Akun admin awal dibuat otomatis saat halaman login dibuka: username `admin`, password `AdminKlinik123!`. Untuk pemakaian nyata, ganti password awal.
- Pasien membuat akun melalui `register.php`. Sistem membuat baris di tabel `pasien` dan tabel `akun` dalam satu transaksi; `akun.id_pasien` menjadi foreign key ke `pasien.id_pasien`.
- Pasien login dan mengajukan kunjungan dari `portal_pasien.php`. Pendaftaran disimpan ke tabel `pendaftaran` dengan `id_pasien` akun yang login, sehingga otomatis muncul di halaman admin.
- Jika database sudah pernah dibuat sebelum fitur akun ditambahkan, jalankan ulang bagian `CREATE TABLE akun` dari `database/klinik_sehat.sql` atau impor SQL yang telah diperbarui. Backup database dahulu jika sudah berisi data penting.
