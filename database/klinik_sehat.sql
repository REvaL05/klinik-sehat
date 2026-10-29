CREATE DATABASE IF NOT EXISTS klinik_sehat
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE klinik_sehat;

CREATE TABLE IF NOT EXISTS pasien (
  id_pasien INT AUTO_INCREMENT PRIMARY KEY,
  no_rm VARCHAR(20) NOT NULL UNIQUE,
  nik VARCHAR(20) UNIQUE,
  nama_pasien VARCHAR(100) NOT NULL,
  jenis_kelamin ENUM('Laki-laki','Perempuan') NOT NULL,
  tanggal_lahir DATE,
  no_hp VARCHAR(20),
  alamat TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;



-- Akun login untuk admin dan pasien. Akun pasien terhubung ke tabel pasien.
CREATE TABLE IF NOT EXISTS akun (
  id_akun INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','pasien') NOT NULL,
  id_pasien INT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_akun_pasien FOREIGN KEY (id_pasien)
    REFERENCES pasien(id_pasien) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dokter (
  id_dokter INT AUTO_INCREMENT PRIMARY KEY,
  nama_dokter VARCHAR(100) NOT NULL,
  spesialisasi VARCHAR(80) NOT NULL,
  no_hp VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pendaftaran (
  id_pendaftaran INT AUTO_INCREMENT PRIMARY KEY,
  kode_pendaftaran VARCHAR(30) NOT NULL UNIQUE,
  id_pasien INT NOT NULL,
  id_dokter INT NOT NULL,
  tanggal_kunjungan DATE NOT NULL,
  keluhan TEXT NOT NULL,
  status ENUM('Menunggu','Diperiksa','Selesai','Batal') NOT NULL DEFAULT 'Menunggu',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pendaftaran_pasien FOREIGN KEY (id_pasien)
    REFERENCES pasien(id_pasien) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_pendaftaran_dokter FOREIGN KEY (id_dokter)
    REFERENCES dokter(id_dokter) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pemeriksaan (
  id_pemeriksaan INT AUTO_INCREMENT PRIMARY KEY,
  id_pendaftaran INT NOT NULL UNIQUE,
  diagnosa TEXT,
  tindakan TEXT,
  resep_obat TEXT,
  catatan TEXT,
  tanggal_pemeriksaan DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pemeriksaan_pendaftaran FOREIGN KEY (id_pendaftaran)
    REFERENCES pendaftaran(id_pendaftaran) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pembayaran (
  id_pembayaran INT AUTO_INCREMENT PRIMARY KEY,
  id_pendaftaran INT NOT NULL UNIQUE,
  biaya_konsultasi DECIMAL(12,2) NOT NULL DEFAULT 0,
  biaya_obat DECIMAL(12,2) NOT NULL DEFAULT 0,
  total_biaya DECIMAL(12,2) NOT NULL DEFAULT 0,
  metode_pembayaran ENUM('Tunai','Transfer','QRIS','BPJS') NOT NULL DEFAULT 'Tunai',
  status_pembayaran ENUM('Belum Bayar','Lunas') NOT NULL DEFAULT 'Belum Bayar',
  tanggal_pembayaran DATETIME NULL,
  CONSTRAINT fk_pembayaran_pendaftaran FOREIGN KEY (id_pendaftaran)
    REFERENCES pendaftaran(id_pendaftaran) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO pasien (no_rm, nik, nama_pasien, jenis_kelamin, tanggal_lahir, no_hp, alamat) VALUES
('RM-0001','1701010101010001','Alya Putri','Perempuan','2003-04-12','081234567801','Jl. Melati No. 10'),
('RM-0002','1701010101010002','Bima Pratama','Laki-laki','2001-09-22','081234567802','Jl. Mawar No. 5'),
('RM-0003','1701010101010003','Citra Lestari','Perempuan','1998-12-03','081234567803','Jl. Kenanga No. 8');

INSERT INTO dokter (nama_dokter, spesialisasi, no_hp)
SELECT 'dr. Andi Saputra','Dokter Umum','081300000001'
WHERE NOT EXISTS (SELECT 1 FROM dokter WHERE nama_dokter='dr. Andi Saputra');
INSERT INTO dokter (nama_dokter, spesialisasi, no_hp)
SELECT 'dr. Siti Rahma','Kesehatan Anak','081300000002'
WHERE NOT EXISTS (SELECT 1 FROM dokter WHERE nama_dokter='dr. Siti Rahma');
INSERT INTO dokter (nama_dokter, spesialisasi, no_hp)
SELECT 'dr. Rudi Hartono','Penyakit Dalam','081300000003'
WHERE NOT EXISTS (SELECT 1 FROM dokter WHERE nama_dokter='dr. Rudi Hartono');
