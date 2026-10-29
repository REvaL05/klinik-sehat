<?php
require_once __DIR__ . '/functions.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$pageTitle = $pageTitle ?? 'Dashboard';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | Klinik Sehat</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="index.php"><span class="brand-mark">✚</span><span>Klinik<span class="brand-light">Sehat</span><small>Sistem Informasi Klinik</small></span></a>
    <div class="nav-label">MENU UTAMA</div>
    <nav>
      <?php if (($_SESSION['role'] ?? 'admin') === 'pasien'): ?>
      <a class="<?= $currentPage==='portal_pasien.php'?'active':'' ?>" href="portal_pasien.php"><span>♡</span> Portal Pasien</a>
      <?php else: ?>
      <a class="<?= $currentPage==='index.php'?'active':'' ?>" href="index.php"><span>▦</span> Dashboard</a>
      <a class="<?= $currentPage==='pasien.php'?'active':'' ?>" href="pasien.php"><span>♙</span> Data Pasien</a>
      <a class="<?= $currentPage==='dokter.php'?'active':'' ?>" href="dokter.php"><span>⚕</span> Data Dokter</a>
      <a class="<?= $currentPage==='pendaftaran.php'?'active':'' ?>" href="pendaftaran.php"><span>▤</span> Pendaftaran</a>
      <a class="<?= $currentPage==='pemeriksaan.php'?'active':'' ?>" href="pemeriksaan.php"><span>♡</span> Pemeriksaan</a>
      <a class="<?= $currentPage==='pembayaran.php'?'active':'' ?>" href="pembayaran.php"><span>▣</span> Pembayaran</a>
      <?php endif; ?>
    </nav>
    <div class="sidebar-bottom"><div class="mini-avatar">KS</div><div><strong><?= e($_SESSION['username'] ?? 'Pengguna') ?></strong><small><?= e(($_SESSION['role'] ?? 'admin') === 'pasien' ? 'Pasien' : 'Pengelola Klinik') ?></small></div></div>
  </aside>
  <main class="main">
    <header class="topbar">
      <button class="menu-toggle" id="menuToggle" aria-label="Buka menu">☰</button>
      <div><span class="eyebrow">SISTEM INFORMASI MANAJEMEN</span><h1><?= e($pageTitle) ?></h1></div>
      <div class="topbar-right"><span class="status-dot"></span><span><?= e(($_SESSION['role'] ?? 'admin') === 'pasien' ? 'Mode Pasien' : 'Mode Admin') ?></span><span class="top-avatar"><?= e(strtoupper(substr($_SESSION['username'] ?? 'A',0,1))) ?></span><a class="btn btn-secondary" href="logout.php">Keluar</a></div>
    </header>
    <section class="content">
      <?php if ($f = take_flash()): ?>
        <div class="alert <?= e($f['type']) ?>"><?= e($f['message']) ?><button class="alert-close" aria-label="Tutup" onclick="this.parentElement.remove()">×</button></div>
      <?php endif; ?>
