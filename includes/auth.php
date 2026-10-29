<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function ensure_default_admin(PDO $pdo): void {
    $check = $pdo->prepare("SELECT id_akun FROM akun WHERE username = ? AND role = 'admin' LIMIT 1");
    $check->execute(['admin']);
    if (!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO akun (username, password_hash, role, id_pasien) VALUES (?, ?, 'admin', NULL)");
        $stmt->execute(['admin', password_hash('AdminKlinik123!', PASSWORD_DEFAULT)]);
    }
}
function require_role(string $role): void {
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        flash('error', 'Silakan login dengan akun yang sesuai untuk mengakses halaman tersebut.');
        redirect('login.php');
    }
}
