<?php
declare(strict_types=1);

$host = 'localhost';
$db   = 'klinik_sehat';
$user = 'root';
$pass = ''; // Sesuaikan jika MySQL Anda menggunakan password.

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa config/database.php dan pastikan database klinik_sehat sudah diimpor.');
}
