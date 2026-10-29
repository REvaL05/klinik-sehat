<?php
require_once 'includes/auth.php';
require_role('admin');
?>
<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
$pageTitle = 'Data Pasien';
$edit = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'save') {
            $id = (int)($_POST['id_pasien'] ?? 0);
            $data = [trim($_POST['no_rm'] ?? ''), trim($_POST['nik'] ?? ''), trim($_POST['nama_pasien'] ?? ''),
                $_POST['jenis_kelamin'] ?? '', $_POST['tanggal_lahir'] ?: null, trim($_POST['no_hp'] ?? ''), trim($_POST['alamat'] ?? '')];
            if ($data[0] === '' || $data[2] === '' || !in_array($data[3], ['Laki-laki','Perempuan'], true)) throw new RuntimeException('Nomor rekam medis, nama, dan jenis kelamin wajib diisi.');
            if ($id) {
                $data[] = $id;
                $stmt = $pdo->prepare('UPDATE pasien SET no_rm=?, nik=NULLIF(?, ""), nama_pasien=?, jenis_kelamin=?, tanggal_lahir=?, no_hp=?, alamat=? WHERE id_pasien=?');
                $stmt->execute($data); flash('success','Data pasien berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO pasien (no_rm,nik,nama_pasien,jenis_kelamin,tanggal_lahir,no_hp,alamat) VALUES (?,NULLIF(?, ""),?,?,?,?,?)');
                $stmt->execute($data); flash('success','Pasien baru berhasil ditambahkan.');
            }
            redirect('pasien.php');
        }
        if ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM pasien WHERE id_pasien=?'); $stmt->execute([(int)$_POST['id_pasien']]);
            flash('success','Data pasien berhasil dihapus.');
            redirect('pasien.php');
        }
    } catch (PDOException $e) {
        flash('error', $e->getCode()==='23000' ? 'Nomor rekam medis/NIK sudah digunakan atau pasien masih memiliki riwayat pendaftaran.' : 'Operasi gagal. Periksa kembali data yang dimasukkan.');
        redirect('pasien.php');
    } catch (RuntimeException $e) { flash('error',$e->getMessage()); redirect('pasien.php'); }
}
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM pasien WHERE id_pasien=?'); $stmt->execute([(int)$_GET['edit']]); $edit = $stmt->fetch();
}
$q = trim($_GET['q'] ?? '');
$stmt = $pdo->prepare('SELECT * FROM pasien WHERE nama_pasien LIKE ? OR no_rm LIKE ? ORDER BY id_pasien DESC');
$stmt->execute(["%$q%","%$q%"]); $rows = $stmt->fetchAll();
require 'includes/header.php';
?>
<div class="section-heading"><div><h2>Data Pasien</h2><p>Informasi identitas dan kontak pasien klinik.</p></div></div>
<div class="two-column">
  <div class="panel form-panel"><div class="panel-title"><div><h3><?= $edit?'Edit Pasien':'Tambah Pasien' ?></h3><p>Isi informasi pasien di bawah ini.</p></div><span class="panel-symbol">♙</span></div>
  <form method="post" class="form-grid">
    <input type="hidden" name="action" value="save"><input type="hidden" name="id_pasien" value="<?= e($edit['id_pasien'] ?? '') ?>">
    <label>Nomor Rekam Medis <em>*</em><input name="no_rm" required placeholder="Contoh: RM-0004" value="<?= e($edit['no_rm'] ?? '') ?>"></label>
    <label>NIK <input name="nik" placeholder="Nomor identitas (opsional)" value="<?= e($edit['nik'] ?? '') ?>"></label>
    <label class="full">Nama Lengkap <em>*</em><input name="nama_pasien" required placeholder="Nama sesuai identitas" value="<?= e($edit['nama_pasien'] ?? '') ?>"></label>
    <label>Jenis Kelamin <em>*</em><select name="jenis_kelamin" required><option value="">Pilih jenis kelamin</option><?php foreach(['Laki-laki','Perempuan'] as $jk): ?><option <?= ($edit['jenis_kelamin']??'')===$jk?'selected':'' ?>><?= e($jk) ?></option><?php endforeach; ?></select></label>
    <label>Tanggal Lahir<input type="date" name="tanggal_lahir" value="<?= e($edit['tanggal_lahir'] ?? '') ?>"></label>
    <label>Nomor HP<input name="no_hp" placeholder="08xxxxxxxxxx" value="<?= e($edit['no_hp'] ?? '') ?>"></label>
    <label class="full">Alamat<textarea name="alamat" rows="2" placeholder="Alamat tempat tinggal"><?= e($edit['alamat'] ?? '') ?></textarea></label>
    <div class="form-actions full"><button class="btn btn-primary" type="submit"><?= $edit?'Simpan Perubahan':'＋ Simpan Pasien' ?></button><?php if($edit): ?><a class="btn btn-muted" href="pasien.php">Batal</a><?php endif; ?></div>
  </form></div>
  <div class="panel info-panel"><div class="info-bubble">i</div><h3>Catatan Pengisian</h3><p>Nomor rekam medis harus unik untuk setiap pasien. NIK bersifat opsional, tetapi jika diisi tidak boleh sama dengan pasien lain.</p><div class="info-line"></div><div class="info-point"><span>✓</span><div><strong>Data akurat</strong><small>Gunakan nama dan kontak yang benar.</small></div></div><div class="info-point"><span>✓</span><div><strong>Privasi pasien</strong><small>Data pasien hanya digunakan untuk keperluan administrasi klinik.</small></div></div></div>
</div>
<div class="section-heading table-heading"><div><h2>Daftar Pasien</h2><p><?= count($rows) ?> data ditemukan</p></div><form class="search-form" method="get"><input name="q" placeholder="Cari nama atau no. RM..." value="<?= e($q) ?>"><button class="btn btn-outline">Cari</button></form></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>No. RM</th><th>Nama Pasien</th><th>Jenis Kelamin</th><th>Tanggal Lahir</th><th>No. HP</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><strong><?= e($r['no_rm']) ?></strong></td><td><?= e($r['nama_pasien']) ?></td><td><?= e($r['jenis_kelamin']) ?></td><td><?= e($r['tanggal_lahir'] ?: '—') ?></td><td><?= e($r['no_hp'] ?: '—') ?></td><td class="actions"><a class="action-link" href="pasien.php?edit=<?= (int)$r['id_pasien'] ?>">Edit</a><form method="post" data-confirm="Hapus pasien ini? Pasien yang sudah memiliki pendaftaran tidak dapat dihapus."><input type="hidden" name="action" value="delete"><input type="hidden" name="id_pasien" value="<?= (int)$r['id_pasien'] ?>"><button class="action-link danger-link">Hapus</button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="6" class="empty-cell">Data pasien tidak ditemukan.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
