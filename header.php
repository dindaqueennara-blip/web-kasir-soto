<?php
// Variabel: $halaman (dashboard|kasir|laporan|menu), $judul
require_login();
if (!isset($halaman)) { header('Location: dashboard.php'); exit; }
$navs = ['dashboard'=>'Dashboard','kasir'=>'Kasir','laporan'=>'Laporan','menu'=>'Menu'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> - Soto Semarang Tan Nyana</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="app">
<div class="panel">
  <header class="header">
    <div class="brand">
      <img class="brand-logo" src="logo-soto.svg" alt="">
      <span class="brand-sep"></span>
      <div class="brand-teks"><span>SOTO SEMARANG</span><span>TAN NYANA</span></div>
    </div>
    <nav class="nav">
      <?php foreach ($navs as $k => $label): ?>
        <a href="<?= $k ?>.php" class="<?= $halaman === $k ? 'active' : '' ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="user"><?= e($_SESSION['user']['nama']) ?>  |  <a href="logout.php" class="logout">Keluar</a></div>
    <img class="header-mangkuk" src="mangkuk-header.svg" alt="">
  </header>
  <main class="body">