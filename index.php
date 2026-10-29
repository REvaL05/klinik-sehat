<?php
require_once 'includes/auth.php';
require_role('admin');
?>
<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
$pageTitle = 'Dashboard';
$jumlahPasien = (int)$pdo->query('SELECT COUNT(*) FROM pasien')->fetchColumn();
$jumlahDokter = (int)$pdo->query('SELECT COUNT(*) FROM dokter')->fetchColumn();
$jumlahPendaftaran = (int)$pdo->query('SELECT COUNT(*) FROM pendaftaran')->fetchColumn();
$menunggu = (int)$pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status='Menunggu'")->fetchColumn();
$recent = $pdo->query("SELECT p.kode_pendaftaran, ps.nama_pasien, d.nama_dokter, p.tanggal_kunjungan, p.status
 FROM pendaftaran p JOIN pasien ps ON ps.id_pasien=p.id_pasien
 JOIN dokter d ON d.id_dokter=p.id_dokter ORDER BY p.id_pendaftaran DESC LIMIT 6")->fetchAll();
require 'includes/header.php';
?>
<div class="welcome-panel">
  <div><span class="pill pill-white">RINGKASAN KLINIK</span><h2>Selamat datang di Klinik Sehat</h2><p>Kelola data pasien, pendaftaran, pemeriksaan, dan pembayaran dalam satu sistem.</p></div>
  <div class="welcome-icon">✚</div>
</div>
<div class="stats-grid">
  <article class="stat-card"><div class="stat-icon blue">♙</div><div><span>Total Pasien</span><strong><?= $jumlahPasien ?></strong><small>Pasien terdaftar</small></div></article>
  <article class="stat-card"><div class="stat-icon purple">⚕</div><div><span>Dokter</span><strong><?= $jumlahDokter ?></strong><small>Tenaga medis</small></div></article>
  <article class="stat-card"><div class="stat-icon green">▤</div><div><span>Total Pendaftaran</span><strong><?= $jumlahPendaftaran ?></strong><small>Semua kunjungan</small></div></article>
  <article class="stat-card"><div class="stat-icon orange">◷</div><div><span>Menunggu</span><strong><?= $menunggu ?></strong><small>Antrean pasien</small></div></article>
</div>
<div class="section-heading"><div><h2>Pendaftaran Terbaru</h2><p>Daftar kunjungan pasien yang terakhir dicatat.</p></div><a class="btn btn-primary" href="pendaftaran.php">＋ Pendaftaran Baru</a></div>
<div class="table-card">
  <?php if (!$recent): ?><div class="empty-state"><div>▤</div><h3>Belum ada pendaftaran</h3><p>Tambahkan pendaftaran pasien untuk melihat data di bagian ini.</p></div>
  <?php else: ?>
  <div class="table-wrap"><table><thead><tr><th>Kode</th><th>Nama Pasien</th><th>Dokter</th><th>Tanggal Kunjungan</th><th>Status</th></tr></thead><tbody>
  <?php foreach ($recent as $r): ?><tr><td><strong><?= e($r['kode_pendaftaran']) ?></strong></td><td><?= e($r['nama_pasien']) ?></td><td><?= e($r['nama_dokter']) ?></td><td><?= e($r['tanggal_kunjungan']) ?></td><td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= e($r['status']) ?></span></td></tr><?php endforeach; ?>
  </tbody></table></div><?php endif; ?>
</div>
<div class="quick-grid">
  <a href="pasien.php" class="quick-card"><span class="quick-icon blue">♙</span><span><strong>Kelola Pasien</strong><small>Tambah dan perbarui data pasien</small></span><b>→</b></a>
  <a href="dokter.php" class="quick-card"><span class="quick-icon purple">⚕</span><span><strong>Kelola Dokter</strong><small>Atur data tenaga medis</small></span><b>→</b></a>
  <a href="pemeriksaan.php" class="quick-card"><span class="quick-icon green">♡</span><span><strong>Catat Pemeriksaan</strong><small>Masukkan hasil pemeriksaan</small></span><b>→</b></a>
</div>
<?php require 'includes/footer.php'; ?>
