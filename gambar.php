<?php
require_once 'config.php';
if (empty($_SESSION['user'])) { http_response_code(401); exit; }
$st = db()->prepare('SELECT gambar, gambar_tipe FROM menu WHERE id = ?');
$st->execute([(int)($_GET['id'] ?? 0)]);
$r = $st->fetch();
if (!$r || $r['gambar'] === null) { http_response_code(404); exit; }
header('Content-Type: ' . ($r['gambar_tipe'] ?: 'image/jpeg'));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Content-Length: ' . strlen($r['gambar']));
echo $r['gambar'];
