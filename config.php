<?php
// Konfigurasi koneksi database (sesuaikan dengan XAMPP/Laragon Anda)
define('DB_HOST', 'localhost');
define('DB_NAME', 'soto_tan_nyana');
define('DB_USER', 'root');
define('DB_PASS', '');

date_default_timezone_set('Asia/Jakarta');
if (session_status() === PHP_SESSION_NONE) session_start();

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function require_login(bool $json = false): void {
    if (empty($_SESSION['user'])) {
        if ($json) { http_response_code(401); echo json_encode(['ok'=>false,'msg'=>'Sesi habis, silakan login ulang.']); exit; }
        header('Location: index.php'); exit;
    }
}

function rupiah(int $n): string { return 'Rp ' . number_format($n, 0, ',', '.'); }

function tgl_indo(string $dt): string {
    $b = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $t = strtotime($dt);
    return date('d', $t).' '.$b[(int)date('n', $t)].' '.date('Y', $t).'  '.date('H:i', $t);
}

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function json_out(array $d, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($d); exit;
}
