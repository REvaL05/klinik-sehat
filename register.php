<?php
require_once 'includes/auth.php';
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $nama=trim($_POST['nama_pasien']??''); $username=trim($_POST['username']??''); $password=$_POST['password']??''; $konfirmasi=$_POST['konfirmasi']??'';
  $jk=$_POST['jenis_kelamin']??''; $tgl=$_POST['tanggal_lahir']??''; $nik=trim($_POST['nik']??''); $hp=trim($_POST['no_hp']??''); $alamat=trim($_POST['alamat']??'');
  if ($nama===''||$username===''||$password==='') $errors[]='Nama, username, dan password wajib diisi.';
  if (!in_array($jk,['Laki-laki','Perempuan'],true)) $errors[]='Pilih jenis kelamin yang valid.';
  if (strlen($password)<8) $errors[]='Password minimal 8 karakter.';
  if ($password!==$konfirmasi) $errors[]='Konfirmasi password tidak sama.';
  if (!preg_match('/^[a-zA-Z0-9_.-]{4,60}$/',$username)) $errors[]='Username harus 4–60 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
  $check=$pdo->prepare('SELECT COUNT(*) FROM akun WHERE username=?'); $check->execute([$username]); if ((int)$check->fetchColumn()>0) $errors[]='Username sudah digunakan.';
  if ($nik!=='') { $check=$pdo->prepare('SELECT COUNT(*) FROM pasien WHERE nik=?'); $check->execute([$nik]); if ((int)$check->fetchColumn()>0) $errors[]='NIK sudah terdaftar.'; }
  if (!$errors) {
    try {
      $pdo->beginTransaction();
      $noRm='RM-'.strtoupper(bin2hex(random_bytes(4)));
      $stmt=$pdo->prepare('INSERT INTO pasien (no_rm, nik, nama_pasien, jenis_kelamin, tanggal_lahir, no_hp, alamat) VALUES (?, ?, ?, ?, ?, ?, ?)');
      $stmt->execute([$noRm,$nik!==''?$nik:null,$nama,$jk,$tgl!==''?$tgl:null,$hp!==''?$hp:null,$alamat!==''?$alamat:null]);
      $idPasien=(int)$pdo->lastInsertId();
      $stmt=$pdo->prepare("INSERT INTO akun (username,password_hash,role,id_pasien) VALUES (?,?,'pasien',?)");
      $stmt->execute([$username,password_hash($password,PASSWORD_DEFAULT),$idPasien]);
      $pdo->commit();
      flash('success','Pendaftaran akun berhasil. Silakan login menggunakan akun yang baru dibuat.'); redirect('login.php');
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $errors[]='Pendaftaran belum berhasil. Pastikan database terbaru sudah diimpor dan username/NIK belum digunakan.'; }
  }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Daftar Pasien | Klinik Sehat</title><link rel="stylesheet" href="assets/style.css"><style>.auth-wrap{min-height:100vh;display:grid;place-items:center;padding:24px;background:#f3f7fb}.auth-card{width:min(720px,100%);background:white;border-radius:20px;padding:32px;box-shadow:0 15px 45px #10243a12}.auth-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field{margin:10px 0}.field label{display:block;margin-bottom:7px;font-weight:600}.field input,.field select,.field textarea{width:100%;padding:12px;border:1px solid #d8e0ea;border-radius:10px;font:inherit}.field textarea{min-height:85px}.auth-card .btn{margin-top:12px}.err{background:#fee2e2;color:#991b1b;padding:12px;border-radius:9px;margin:10px 0}@media(max-width:600px){.auth-grid{grid-template-columns:1fr}}</style></head><body><main class="auth-wrap"><section class="auth-card"><h1>Daftar sebagai Pasien</h1><p>Isi data diri untuk membuat akun dan masuk ke layanan pendaftaran klinik.</p><?php foreach($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?><form method="post"><div class="auth-grid"><div class="field"><label>Nama lengkap *</label><input name="nama_pasien" required value="<?= e($_POST['nama_pasien']??'') ?>"></div><div class="field"><label>Username *</label><input name="username" required minlength="4" value="<?= e($_POST['username']??'') ?>"></div><div class="field"><label>Password * (min. 8 karakter)</label><input type="password" name="password" required minlength="8"></div><div class="field"><label>Konfirmasi password *</label><input type="password" name="konfirmasi" required minlength="8"></div><div class="field"><label>NIK</label><input name="nik" value="<?= e($_POST['nik']??'') ?>"></div><div class="field"><label>Jenis kelamin *</label><select name="jenis_kelamin" required><option value="">Pilih</option><option>Laki-laki</option><option>Perempuan</option></select></div><div class="field"><label>Tanggal lahir</label><input type="date" name="tanggal_lahir" value="<?= e($_POST['tanggal_lahir']??'') ?>"></div><div class="field"><label>Nomor HP</label><input name="no_hp" value="<?= e($_POST['no_hp']??'') ?>"></div></div><div class="field"><label>Alamat</label><textarea name="alamat"><?= e($_POST['alamat']??'') ?></textarea></div><button class="btn btn-primary" type="submit">Buat Akun Pasien</button></form><p><a href="login.php">Sudah punya akun? Login</a></p></section></main></body></html>
