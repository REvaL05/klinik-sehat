<?php
require_once 'includes/auth.php';
ensure_default_admin($pdo);
if (!empty($_SESSION['user_id'])) {
    redirect(($_SESSION['role'] ?? '') === 'admin' ? 'index.php' : 'portal_pasien.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = ($_POST['role'] ?? 'pasien') === 'admin' ? 'admin' : 'pasien';
    $stmt = $pdo->prepare('SELECT id_akun, username, password_hash, role, id_pasien FROM akun WHERE username = ? AND role = ? LIMIT 1');
    $stmt->execute([$username, $role]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id_akun'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['id_pasien'] = $user['id_pasien'] ? (int)$user['id_pasien'] : null;
        redirect($role === 'admin' ? 'index.php' : 'portal_pasien.php');
    }
    $error = 'Username, password, atau jenis akun tidak sesuai.';
}
$pageTitle = 'Login Klinik Sehat';
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($pageTitle) ?></title><link rel="stylesheet" href="assets/style.css"><style>.auth-wrap{min-height:100vh;display:grid;place-items:center;padding:24px;background:#f3f7fb}.auth-card{width:min(460px,100%);background:white;border-radius:20px;padding:32px;box-shadow:0 15px 45px #10243a12}.auth-card h1{margin:0 0 8px}.auth-card .field{margin:16px 0}.auth-card label{display:block;margin-bottom:7px;font-weight:600}.auth-card input,.auth-card select{width:100%;padding:12px;border:1px solid #d8e0ea;border-radius:10px;font:inherit}.auth-card .btn{width:100%;justify-content:center}.auth-links{display:flex;justify-content:space-between;gap:10px;margin-top:18px;font-size:14px}.auth-links a{color:#2563eb}.auth-logo{font-size:30px;color:#2563eb;margin-bottom:10px}.auth-error{background:#fee2e2;color:#991b1b;padding:12px;border-radius:9px;margin:14px 0}</style></head><body><main class="auth-wrap"><section class="auth-card"><div class="auth-logo">✚ Klinik Sehat</div><h1>Masuk ke akun</h1><p>Silakan masuk sebagai admin atau pasien.</p><?php if ($error): ?><div class="auth-error"><?= e($error) ?></div><?php endif; ?><?php if ($f=take_flash()): ?><div class="auth-error"><?= e($f['message']) ?></div><?php endif; ?><form method="post"><div class="field"><label for="role">Masuk sebagai</label><select name="role" id="role"><option value="pasien">Pasien</option><option value="admin">Admin</option></select></div><div class="field"><label for="username">Username</label><input id="username" name="username" required autocomplete="username"></div><div class="field"><label for="password">Password</label><input type="password" id="password" name="password" required autocomplete="current-password"></div><button class="btn btn-primary" type="submit">Login</button></form><div class="auth-links"><a href="register.php">Daftar sebagai pasien</a><a href="index.php">Halaman admin</a></div><p style="font-size:12px;color:#64748b;margin-top:22px">Akun admin awal: <b>admin</b> / <b>AdminKlinik123!</b>. Ganti password setelah login untuk penggunaan nyata.</p></section></main></body></html>
