<?php
require_once 'includes/auth.php';
require_role('pasien');
$idPasien=(int)$_SESSION['id_pasien'];
$stmt=$pdo->prepare('SELECT * FROM pasien WHERE id_pasien=?'); $stmt->execute([$idPasien]); $pasien=$stmt->fetch();
if (!$pasien) { session_destroy(); redirect('login.php'); }
$doctors=$pdo->query('SELECT id_dokter,nama_dokter,spesialisasi FROM dokter ORDER BY nama_dokter')->fetchAll();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $dokter=(int)($_POST['id_dokter']??0); $tanggal=$_POST['tanggal_kunjungan']??''; $keluhan=trim($_POST['keluhan']??'');
  $check=$pdo->prepare('SELECT COUNT(*) FROM dokter WHERE id_dokter=?');$check->execute([$dokter]);
  if (!$check->fetchColumn() || !$tanggal || $tanggal<date('Y-m-d') || $keluhan==='') { flash('error','Periksa kembali dokter, tanggal kunjungan, dan keluhan. Tanggal tidak boleh di masa lalu.'); redirect('portal_pasien.php'); }
  $kode='REG-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
  $stmt=$pdo->prepare("INSERT INTO pendaftaran (kode_pendaftaran,id_pasien,id_dokter,tanggal_kunjungan,keluhan,status) VALUES (?,?,?,?,?,'Menunggu')");
  $stmt->execute([$kode,$idPasien,$dokter,$tanggal,$keluhan]);
  flash('success','Pendaftaran berhasil dikirim. Admin klinik dapat melihat pendaftaran ini.'); redirect('portal_pasien.php');
}
$stmt=$pdo->prepare('SELECT p.kode_pendaftaran,p.tanggal_kunjungan,p.keluhan,p.status,d.nama_dokter,d.spesialisasi FROM pendaftaran p JOIN dokter d ON d.id_dokter=p.id_dokter WHERE p.id_pasien=? ORDER BY p.id_pendaftaran DESC');$stmt->execute([$idPasien]);$registrations=$stmt->fetchAll();
$pageTitle='Portal Pasien'; require 'includes/header.php';
?>
<div class="welcome-panel"><div><span class="pill pill-white">PORTAL PASIEN</span><h2>Halo, <?= e($pasien['nama_pasien']) ?>!</h2><p>Ajukan pendaftaran kunjungan dan pantau statusnya di sini.</p></div><div class="welcome-icon">♡</div></div>
<div class="section-heading"><div><h2>Daftar Kunjungan</h2><p>Data pendaftaran akan langsung terhubung ke dashboard admin.</p></div></div>
<div class="form-card"><form method="post" class="form-grid"><div class="field"><label>Dokter</label><select name="id_dokter" required><option value="">Pilih dokter</option><?php foreach($doctors as $d): ?><option value="<?= (int)$d['id_dokter'] ?>"><?= e($d['nama_dokter']) ?> — <?= e($d['spesialisasi']) ?></option><?php endforeach; ?></select></div><div class="field"><label>Tanggal kunjungan</label><input type="date" name="tanggal_kunjungan" min="<?= date('Y-m-d') ?>" required></div><div class="field field-full"><label>Keluhan</label><textarea name="keluhan" required placeholder="Jelaskan keluhan yang dirasakan..."></textarea></div><div class="form-actions"><button class="btn btn-primary" type="submit">Kirim Pendaftaran</button></div></form></div>
<div class="section-heading"><div><h2>Riwayat Pendaftaran Saya</h2><p>Informasi status kunjungan yang sudah diajukan.</p></div></div><div class="table-card"><?php if(!$registrations): ?><div class="empty-state"><h3>Belum ada pendaftaran</h3><p>Gunakan formulir di atas untuk membuat pendaftaran pertama.</p></div><?php else: ?><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Tanggal</th><th>Dokter</th><th>Keluhan</th><th>Status</th></tr></thead><tbody><?php foreach($registrations as $r): ?><tr><td><strong><?= e($r['kode_pendaftaran']) ?></strong></td><td><?= e($r['tanggal_kunjungan']) ?></td><td><?= e($r['nama_dokter']) ?><br><small><?= e($r['spesialisasi']) ?></small></td><td><?= e($r['keluhan']) ?></td><td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= e($r['status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
<?php require 'includes/footer.php'; ?>
