<?php
require_once 'includes/auth.php';
require_role('admin');
?>
<?php
require_once 'config/database.php'; require_once 'includes/functions.php'; $pageTitle='Pemeriksaan Pasien'; $edit=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  if(($_POST['action']??'')==='save'){
   $id=(int)($_POST['id_pemeriksaan']??0);$reg=(int)($_POST['id_pendaftaran']??0);$diag=trim($_POST['diagnosa']??'');$tind=trim($_POST['tindakan']??'');$resep=trim($_POST['resep_obat']??'');$cat=trim($_POST['catatan']??'');
   if(!$reg||$diag==='')throw new RuntimeException('Pendaftaran dan diagnosa wajib diisi.');
   if($id){$s=$pdo->prepare('UPDATE pemeriksaan SET id_pendaftaran=?,diagnosa=?,tindakan=?,resep_obat=?,catatan=? WHERE id_pemeriksaan=?');$s->execute([$reg,$diag,$tind,$resep,$cat,$id]);}
   else{$s=$pdo->prepare('INSERT INTO pemeriksaan(id_pendaftaran,diagnosa,tindakan,resep_obat,catatan) VALUES(?,?,?,?,?)');$s->execute([$reg,$diag,$tind,$resep,$cat]);}
   $pdo->prepare("UPDATE pendaftaran SET status='Selesai' WHERE id_pendaftaran=?")->execute([$reg]);flash('success','Catatan pemeriksaan berhasil disimpan.');redirect('pemeriksaan.php');
  }elseif(($_POST['action']??'')==='delete'){$s=$pdo->prepare('DELETE FROM pemeriksaan WHERE id_pemeriksaan=?');$s->execute([(int)$_POST['id_pemeriksaan']]);flash('success','Catatan pemeriksaan dihapus.');redirect('pemeriksaan.php');}
 }catch(PDOException $e){flash('error','Gagal menyimpan. Setiap pendaftaran hanya boleh memiliki satu catatan pemeriksaan.');redirect('pemeriksaan.php');}
 catch(RuntimeException $e){flash('error',$e->getMessage());redirect('pemeriksaan.php');}
}
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM pemeriksaan WHERE id_pemeriksaan=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$regs=$pdo->query("SELECT p.id_pendaftaran,p.kode_pendaftaran,ps.nama_pasien FROM pendaftaran p JOIN pasien ps ON ps.id_pasien=p.id_pasien WHERE p.status!='Batal' ORDER BY p.id_pendaftaran DESC")->fetchAll();
$rows=$pdo->query('SELECT pm.*,p.kode_pendaftaran,ps.nama_pasien,d.nama_dokter FROM pemeriksaan pm JOIN pendaftaran p ON p.id_pendaftaran=pm.id_pendaftaran JOIN pasien ps ON ps.id_pasien=p.id_pasien JOIN dokter d ON d.id_dokter=p.id_dokter ORDER BY pm.tanggal_pemeriksaan DESC')->fetchAll();
require 'includes/header.php'; ?>
<div class="section-heading"><div><h2>Pemeriksaan Pasien</h2><p>Catat diagnosa, tindakan, dan resep berdasarkan pendaftaran.</p></div></div>
<div class="panel form-panel wide-form"><div class="panel-title"><div><h3><?= $edit?'Edit Pemeriksaan':'Catat Hasil Pemeriksaan' ?></h3><p>Gunakan informasi dari pemeriksaan tenaga medis.</p></div><span class="panel-symbol">♡</span></div>
<form method="post" class="form-grid two-fields"><input type="hidden" name="action" value="save"><input type="hidden" name="id_pemeriksaan" value="<?= e($edit['id_pemeriksaan']??'') ?>">
<label class="full">Pendaftaran <em>*</em><select name="id_pendaftaran" required><option value="">Pilih pendaftaran</option><?php foreach($regs as $r): ?><option value="<?= (int)$r['id_pendaftaran'] ?>" <?= (int)($edit['id_pendaftaran']??0)===(int)$r['id_pendaftaran']?'selected':'' ?>><?= e($r['kode_pendaftaran'].' — '.$r['nama_pasien']) ?></option><?php endforeach; ?></select></label>
<label>Diagnosa <em>*</em><textarea name="diagnosa" rows="2" required><?= e($edit['diagnosa']??'') ?></textarea></label><label>Tindakan<textarea name="tindakan" rows="2"><?= e($edit['tindakan']??'') ?></textarea></label>
<label>Resep Obat<textarea name="resep_obat" rows="2"><?= e($edit['resep_obat']??'') ?></textarea></label><label>Catatan Tambahan<textarea name="catatan" rows="2"><?= e($edit['catatan']??'') ?></textarea></label>
<div class="form-actions full"><button class="btn btn-primary"><?= $edit?'Simpan Perubahan':'＋ Simpan Pemeriksaan' ?></button><?php if($edit): ?><a class="btn btn-muted" href="pemeriksaan.php">Batal</a><?php endif; ?></div></form></div>
<div class="section-heading table-heading"><div><h2>Riwayat Pemeriksaan</h2><p><?= count($rows) ?> catatan tersimpan</p></div></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Pasien</th><th>Dokter</th><th>Diagnosa</th><th>Tindakan</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><strong><?= e($r['kode_pendaftaran']) ?></strong></td><td><?= e($r['nama_pasien']) ?></td><td><?= e($r['nama_dokter']) ?></td><td><?= e($r['diagnosa']) ?></td><td><?= e($r['tindakan']?:'—') ?></td><td><?= e($r['tanggal_pemeriksaan']) ?></td><td class="actions"><a class="action-link" href="pemeriksaan.php?edit=<?= (int)$r['id_pemeriksaan'] ?>">Edit</a><form method="post" data-confirm="Hapus catatan pemeriksaan ini?"><input type="hidden" name="action" value="delete"><input type="hidden" name="id_pemeriksaan" value="<?= (int)$r['id_pemeriksaan'] ?>"><button class="action-link danger-link">Hapus</button></form></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="7" class="empty-cell">Belum ada catatan pemeriksaan.</td></tr><?php endif; ?></tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
