<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}
function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function take_flash(): ?array {
    if (empty($_SESSION['flash'])) return null;
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
function rupiah($amount): string {
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}
