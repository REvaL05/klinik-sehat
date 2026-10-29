<?php
require_once 'includes/auth.php';
require_role('admin');
?>
<?php
require_once 'config/database.php'; require_once 'includes/functions.php';
$pageTitle='Pendaftaran Pasien'; $edit=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  $action=$_POST['action']??'';
  if($action==='save'){
   $id=(int)($_POST['id_pendaftaran']??0);$kode=trim($_POST['kode_pendaftaran']??'');$pasien=(int)($_POST['id_pasien']??0);$dokter=(int)($_POST['id_dokter']??0);$tgl=$_POST['tanggal_kunjungan']??'';$keluhan=trim($_POST['keluhan']??'');$status=$_POST['status']??'Menunggu';
   if(!$pasien||!$dokter||!$tgl||$keluhan==='')throw new RuntimeException('Pasien, dokter, tanggal kunjungan, dan keluhan wajib diisi.');
   if(!in_array($status,['Menunggu','Diperiksa','Selesai','Batal'],true))$status='Menunggu';
   if($kode==='')$kode='REG-'.date('Ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)),0,6));
   if($id){$s=$pdo->prepare('UPDATE pendaftaran SET kode_pendaftaran=?,id_pasien=?,id_dokter=?,tanggal_kunjungan=?,keluhan=?,status=? WHERE id_pendaftaran=?');$s->execute([$kode,$pasien,$dokter,$tgl,$keluhan,$status,$id]);flash('success','Pendaftaran berhasil diperbarui.');}
   else{$s=$pdo->prepare('INSERT INTO pendaftaran(kode_pendaftaran,id_pasien,id_dokter,tanggal_kunjungan,keluhan,status) VALUES(?,?,?,?,?,?)');$s->execute([$kode,$pasien,$dokter,$tgl,$keluhan,$status]);flash('success','Pendaftaran pasien berhasil dibuat. Kode: '.$kode);}
  }elseif($action==='delete'){$s=$pdo->prepare('DELETE FROM pendaftaran WHERE id_pendaftaran=?');$s->execute([(int)$_POST['id_pendaftaran']]);flash('success','Pendaftaran berhasil dihapus beserta catatan terkait.');}
  redirect('pendaftaran.php');
 }catch(PDOException $e){flash('error','Pendaftaran gagal. Pastikan kode pendaftaran unik dan pasien/dokter tersedia.');redirect('pendaftaran.php');}
 catch(RuntimeException $e){flash('error',$e->getMessage());redirect('pendaftaran.php');}
}
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM pendaftaran WHERE id_pendaftaran=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$pasien=$pdo->query('SELECT id_pasien,no_rm,nama_pasien FROM pasien ORDER BY nama_pasien')->fetchAll();$dokter=$pdo->query('SELECT id_dokter,nama_dokter,spesialisasi FROM dokter ORDER BY nama_dokter')->fetchAll();
$q=trim($_GET['q']??'');$s=$pdo->prepare("SELECT p.*,ps.nama_pasien,ps.no_rm,d.nama_dokter FROM pendaftaran p JOIN pasien ps ON ps.id_pasien=p.id_pasien JOIN dokter d ON d.id_dokter=p.id_dokter WHERE p.kode_pendaftaran LIKE ? OR ps.nama_pasien LIKE ? ORDER BY p.tanggal_kunjungan DESC,p.id_pendaftaran DESC");$s->execute(["%$q%","%$q%"]);$rows=$s->fetchAll();
require 'includes/header.php'; ?>
<div class="section-heading"><div><h2>Pendaftaran Pasien</h2><p>Catat kunjungan dan keluhan pasien ke klinik.</p></div></div>
<div class="panel form-panel wide-form"><div class="panel-title"><div><h3><?= $edit?'Edit Pendaftaran':'Pendaftaran Kunjungan Baru' ?></h3><p>Kode dapat dikosongkan untuk dibuat otomatis.</p></div><span class="panel-symbol">▤</span></div>
<?php if(!$pasien||!$dokter): ?><div class="alert error">Tambahkan minimal satu pasien dan satu dokter sebelum membuat pendaftaran.</div><?php endif; ?>
<form method="post" class="form-grid three-fields"><input type="hidden" name="action" value="save"><input type="hidden" name="id_pendaftaran" value="<?= e($edit['id_pendaftaran']??'') ?>">
<label>Kode Pendaftaran<input name="kode_pendaftaran" placeholder="Otomatis jika kosong" value="<?= e($edit['kode_pendaftaran']??'') ?>"></label>
<label>Pasien <em>*</em><select name="id_pasien" required><option value="">Pilih pasien</option><?php foreach($pasien as $p): ?><option value="<?= (int)$p['id_pasien'] ?>" <?= (int)($edit['id_pasien']??0)===(int)$p['id_pasien']?'selected':'' ?>><?= e($p['no_rm'].' — '.$p['nama_pasien']) ?></option><?php endforeach; ?></select></label>
<label>Dokter <em>*</em><select name="id_dokter" required><option value="">Pilih dokter</option><?php foreach($dokter as $d): ?><option value="<?= (int)$d['id_dokter'] ?>" <?= (int)($edit['id_dokter']??0)===(int)$d['id_dokter']?'selected':'' ?>><?= e($d['nama_dokter'].' — '.$d['spesialisasi']) ?></option><?php endforeach; ?></select></label>
<label>Tanggal Kunjungan <em>*</em><input type="date" name="tanggal_kunjungan" required value="<?= e($edit['tanggal_kunjungan']??date('Y-m-d')) ?>"></label>
<label>Status<select name="status"><?php foreach(['Menunggu','Diperiksa','Selesai','Batal'] as $st): ?><option <?= ($edit['status']??'Menunggu')===$st?'selected':'' ?>><?= e($st) ?></option><?php endforeach; ?></select></label>
<label class="full">Keluhan Utama <em>*</em><textarea name="keluhan" rows="2" required placeholder="Tuliskan keluhan pasien..."><?= e($edit['keluhan']??'') ?></textarea></label>
<div class="form-actions full"><button class="btn btn-primary" <?= (!$pasien||!$dokter)?'disabled':'' ?>><?= $edit?'Simpan Perubahan':'＋ Buat Pendaftaran' ?></button><?php if($edit): ?><a class="btn btn-muted" href="pendaftaran.php">Batal</a><?php endif; ?></div></form></div>
<div class="section-heading table-heading"><div><h2>Riwayat Pendaftaran</h2><p><?= count($rows) ?> pendaftaran ditemukan</p></div><form class="search-form"><input name="q" placeholder="Cari kode atau nama pasien..." value="<?= e($q) ?>"><button class="btn btn-outline">Cari</button></form></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Pasien</th><th>Dokter</th><th>Tanggal</th><th>Keluhan</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><strong><?= e($r['kode_pendaftaran']) ?></strong><small class="sub-cell"><?= e($r['no_rm']) ?></small></td><td><?= e($r['nama_pasien']) ?></td><td><?= e($r['nama_dokter']) ?></td><td><?= e($r['tanggal_kunjungan']) ?></td><td class="truncate-cell"><?= e($r['keluhan']) ?></td><td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= e($r['status']) ?></span></td><td class="actions"><a class="action-link" href="pendaftaran.php?edit=<?= (int)$r['id_pendaftaran'] ?>">Edit</a><form method="post" data-confirm="Hapus pendaftaran ini? Data pemeriksaan dan pembayaran terkait juga akan dihapus."><input type="hidden" name="action" value="delete"><input type="hidden" name="id_pendaftaran" value="<?= (int)$r['id_pendaftaran'] ?>"><button class="action-link danger-link">Hapus</button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="7" class="empty-cell">Belum ada data pendaftaran.</td></tr><?php endif; ?></tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
