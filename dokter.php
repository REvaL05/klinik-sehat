<?php
require_once 'includes/auth.php';
require_role('admin');
?>
<?php
require_once 'config/database.php'; require_once 'includes/functions.php';
$pageTitle='Data Dokter'; $edit=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
 try {
  if(($_POST['action']??'')==='save'){
   $id=(int)($_POST['id_dokter']??0); $nama=trim($_POST['nama_dokter']??''); $sp=trim($_POST['spesialisasi']??''); $hp=trim($_POST['no_hp']??'');
   if($nama===''||$sp==='') throw new RuntimeException('Nama dokter dan spesialisasi wajib diisi.');
   if($id){$s=$pdo->prepare('UPDATE dokter SET nama_dokter=?,spesialisasi=?,no_hp=? WHERE id_dokter=?');$s->execute([$nama,$sp,$hp,$id]);flash('success','Data dokter berhasil diperbarui.');}
   else {$s=$pdo->prepare('INSERT INTO dokter(nama_dokter,spesialisasi,no_hp) VALUES(?,?,?)');$s->execute([$nama,$sp,$hp]);flash('success','Dokter berhasil ditambahkan.');}
  } elseif(($_POST['action']??'')==='delete') {$s=$pdo->prepare('DELETE FROM dokter WHERE id_dokter=?');$s->execute([(int)$_POST['id_dokter']]);flash('success','Data dokter berhasil dihapus.');}
  redirect('dokter.php');
 }catch(PDOException $e){flash('error','Dokter tidak dapat dihapus karena masih terkait dengan data pendaftaran, atau data tidak valid.');redirect('dokter.php');}
 catch(RuntimeException $e){flash('error',$e->getMessage());redirect('dokter.php');}
}
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM dokter WHERE id_dokter=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$q=trim($_GET['q']??'');$s=$pdo->prepare('SELECT d.*, (SELECT COUNT(*) FROM pendaftaran p WHERE p.id_dokter=d.id_dokter) jumlah_kunjungan FROM dokter d WHERE nama_dokter LIKE ? OR spesialisasi LIKE ? ORDER BY id_dokter DESC');$s->execute(["%$q%","%$q%"]);$rows=$s->fetchAll();
require 'includes/header.php'; ?>
<div class="section-heading"><div><h2>Data Dokter</h2><p>Kelola informasi dokter dan bidang keahliannya.</p></div></div>
<div class="two-column">
<div class="panel form-panel"><div class="panel-title"><div><h3><?= $edit?'Edit Dokter':'Tambah Dokter' ?></h3><p>Masukkan informasi tenaga medis.</p></div><span class="panel-symbol">⚕</span></div>
<form method="post" class="form-grid"><input type="hidden" name="action" value="save"><input type="hidden" name="id_dokter" value="<?= e($edit['id_dokter']??'') ?>">
<label class="full">Nama Dokter <em>*</em><input name="nama_dokter" required placeholder="Contoh: dr. Nama Lengkap" value="<?= e($edit['nama_dokter']??'') ?>"></label>
<label class="full">Spesialisasi <em>*</em><select name="spesialisasi" required><option value="">Pilih bidang</option><?php foreach(['Dokter Umum','Kesehatan Anak','Penyakit Dalam','Kesehatan Gigi','Kulit dan Kelamin','Kandungan','Mata','Lainnya'] as $sp): ?><option <?= ($edit['spesialisasi']??'')===$sp?'selected':'' ?>><?= e($sp) ?></option><?php endforeach; ?></select></label>
<label class="full">Nomor HP<input name="no_hp" value="<?= e($edit['no_hp']??'') ?>" placeholder="08xxxxxxxxxx"></label>
<div class="form-actions full"><button class="btn btn-primary"><?= $edit?'Simpan Perubahan':'＋ Simpan Dokter' ?></button><?php if($edit): ?><a class="btn btn-muted" href="dokter.php">Batal</a><?php endif; ?></div></form></div>
<div class="panel info-panel"><div class="info-bubble">⚕</div><h3>Tenaga Medis</h3><p>Data dokter digunakan saat membuat pendaftaran kunjungan. Pastikan spesialisasi sesuai dengan layanan yang tersedia.</p><div class="info-line"></div><div class="info-point"><span>✓</span><div><strong>Relasi data</strong><small>Dokter terhubung dengan pendaftaran pasien.</small></div></div><div class="info-point"><span>✓</span><div><strong>Riwayat terjaga</strong><small>Dokter yang sudah memiliki pendaftaran tidak dapat dihapus.</small></div></div></div></div>
<div class="section-heading table-heading"><div><h2>Daftar Dokter</h2><p><?= count($rows) ?> dokter ditemukan</p></div><form class="search-form"><input name="q" placeholder="Cari dokter/spesialisasi..." value="<?= e($q) ?>"><button class="btn btn-outline">Cari</button></form></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Nama Dokter</th><th>Spesialisasi</th><th>Nomor HP</th><th>Total Kunjungan</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><div class="person-cell"><span class="person-avatar"><?= e(mb_substr($r['nama_dokter'],0,1)) ?></span><strong><?= e($r['nama_dokter']) ?></strong></div></td><td><span class="specialty-tag"><?= e($r['spesialisasi']) ?></span></td><td><?= e($r['no_hp']?:'—') ?></td><td><?= (int)$r['jumlah_kunjungan'] ?> kunjungan</td><td class="actions"><a class="action-link" href="dokter.php?edit=<?= (int)$r['id_dokter'] ?>">Edit</a><form method="post" data-confirm="Hapus data dokter ini?"><input type="hidden" name="action" value="delete"><input type="hidden" name="id_dokter" value="<?= (int)$r['id_dokter'] ?>"><button class="action-link danger-link">Hapus</button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="5" class="empty-cell">Data dokter tidak ditemukan.</td></tr><?php endif; ?></tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
