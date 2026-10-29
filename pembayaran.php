<?php
require_once 'includes/auth.php';
require_role('admin');
?>
<?php
require_once 'config/database.php'; require_once 'includes/functions.php'; $pageTitle='Pembayaran Pasien'; $edit=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  if(($_POST['action']??'')==='save'){
   $id=(int)($_POST['id_pembayaran']??0);$reg=(int)($_POST['id_pendaftaran']??0);$k=max(0,(float)($_POST['biaya_konsultasi']??0));$o=max(0,(float)($_POST['biaya_obat']??0));$met=$_POST['metode_pembayaran']??'Tunai';$st=$_POST['status_pembayaran']??'Belum Bayar';
   if(!$reg)throw new RuntimeException('Pilih pendaftaran pasien.');
   if(!in_array($met,['Tunai','Transfer','QRIS','BPJS'],true))$met='Tunai';if(!in_array($st,['Belum Bayar','Lunas'],true))$st='Belum Bayar';$total=$k+$o;$paid=$st==='Lunas'?date('Y-m-d H:i:s'):null;
   if($id){$s=$pdo->prepare('UPDATE pembayaran SET id_pendaftaran=?,biaya_konsultasi=?,biaya_obat=?,total_biaya=?,metode_pembayaran=?,status_pembayaran=?,tanggal_pembayaran=? WHERE id_pembayaran=?');$s->execute([$reg,$k,$o,$total,$met,$st,$paid,$id]);}
   else{$s=$pdo->prepare('INSERT INTO pembayaran(id_pendaftaran,biaya_konsultasi,biaya_obat,total_biaya,metode_pembayaran,status_pembayaran,tanggal_pembayaran) VALUES(?,?,?,?,?,?,?)');$s->execute([$reg,$k,$o,$total,$met,$st,$paid]);}
   flash('success','Data pembayaran berhasil disimpan. Total: '.rupiah($total));redirect('pembayaran.php');
  }elseif(($_POST['action']??'')==='delete'){$s=$pdo->prepare('DELETE FROM pembayaran WHERE id_pembayaran=?');$s->execute([(int)$_POST['id_pembayaran']]);flash('success','Data pembayaran dihapus.');redirect('pembayaran.php');}
 }catch(PDOException $e){flash('error','Pendaftaran ini mungkin sudah memiliki catatan pembayaran. Gunakan Edit untuk memperbaruinya.');redirect('pembayaran.php');}
 catch(RuntimeException $e){flash('error',$e->getMessage());redirect('pembayaran.php');}
}
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM pembayaran WHERE id_pembayaran=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$regs=$pdo->query("SELECT p.id_pendaftaran,p.kode_pendaftaran,ps.nama_pasien FROM pendaftaran p JOIN pasien ps ON ps.id_pasien=p.id_pasien ORDER BY p.id_pendaftaran DESC")->fetchAll();
$rows=$pdo->query('SELECT b.*,p.kode_pendaftaran,ps.nama_pasien FROM pembayaran b JOIN pendaftaran p ON p.id_pendaftaran=b.id_pendaftaran JOIN pasien ps ON ps.id_pasien=p.id_pasien ORDER BY b.id_pembayaran DESC')->fetchAll();
$totalLunas=(float)$pdo->query("SELECT COALESCE(SUM(total_biaya),0) FROM pembayaran WHERE status_pembayaran='Lunas'")->fetchColumn();
require 'includes/header.php'; ?>
<div class="section-heading"><div><h2>Pembayaran Pasien</h2><p>Kelola biaya layanan dan status pembayaran kunjungan.</p></div><div class="revenue-chip"><small>Total pembayaran lunas</small><strong><?= rupiah($totalLunas) ?></strong></div></div>
<div class="panel form-panel wide-form"><div class="panel-title"><div><h3><?= $edit?'Edit Pembayaran':'Catat Pembayaran' ?></h3><p>Total biaya dihitung otomatis dari biaya konsultasi dan obat.</p></div><span class="panel-symbol">▣</span></div>
<form method="post" class="form-grid three-fields"><input type="hidden" name="action" value="save"><input type="hidden" name="id_pembayaran" value="<?= e($edit['id_pembayaran']??'') ?>">
<label class="full">Pendaftaran <em>*</em><select name="id_pendaftaran" required><option value="">Pilih pendaftaran</option><?php foreach($regs as $r): ?><option value="<?= (int)$r['id_pendaftaran'] ?>" <?= (int)($edit['id_pendaftaran']??0)===(int)$r['id_pendaftaran']?'selected':'' ?>><?= e($r['kode_pendaftaran'].' — '.$r['nama_pasien']) ?></option><?php endforeach; ?></select></label>
<label>Biaya Konsultasi (Rp)<input type="number" min="0" step="1000" name="biaya_konsultasi" id="feeConsult" value="<?= e($edit['biaya_konsultasi']??0) ?>" required></label>
<label>Biaya Obat (Rp)<input type="number" min="0" step="1000" name="biaya_obat" id="feeMedicine" value="<?= e($edit['biaya_obat']??0) ?>" required></label>
<label>Perkiraan Total<div class="total-preview" id="totalPreview"><?= rupiah(($edit['biaya_konsultasi']??0)+($edit['biaya_obat']??0)) ?></div></label>
<label>Metode Pembayaran<select name="metode_pembayaran"><?php foreach(['Tunai','Transfer','QRIS','BPJS'] as $m): ?><option <?= ($edit['metode_pembayaran']??'Tunai')===$m?'selected':'' ?>><?= e($m) ?></option><?php endforeach; ?></select></label>
<label>Status Pembayaran<select name="status_pembayaran"><?php foreach(['Belum Bayar','Lunas'] as $s): ?><option <?= ($edit['status_pembayaran']??'Belum Bayar')===$s?'selected':'' ?>><?= e($s) ?></option><?php endforeach; ?></select></label>
<div class="form-actions full"><button class="btn btn-primary"><?= $edit?'Simpan Perubahan':'＋ Simpan Pembayaran' ?></button><?php if($edit): ?><a class="btn btn-muted" href="pembayaran.php">Batal</a><?php endif; ?></div></form></div>
<div class="section-heading table-heading"><div><h2>Riwayat Pembayaran</h2><p><?= count($rows) ?> transaksi tercatat</p></div></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Pasien</th><th>Konsultasi</th><th>Obat</th><th>Total</th><th>Metode</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><strong><?= e($r['kode_pendaftaran']) ?></strong></td><td><?= e($r['nama_pasien']) ?></td><td><?= rupiah($r['biaya_konsultasi']) ?></td><td><?= rupiah($r['biaya_obat']) ?></td><td><strong><?= rupiah($r['total_biaya']) ?></strong></td><td><?= e($r['metode_pembayaran']) ?></td><td><span class="badge badge-<?= $r['status_pembayaran']==='Lunas'?'selesai':'menunggu' ?>"><?= e($r['status_pembayaran']) ?></span></td><td class="actions"><a class="action-link" href="pembayaran.php?edit=<?= (int)$r['id_pembayaran'] ?>">Edit</a><form method="post" data-confirm="Hapus transaksi pembayaran ini?"><input type="hidden" name="action" value="delete"><input type="hidden" name="id_pembayaran" value="<?= (int)$r['id_pembayaran'] ?>"><button class="action-link danger-link">Hapus</button></form></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="8" class="empty-cell">Belum ada transaksi pembayaran.</td></tr><?php endif; ?></tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
