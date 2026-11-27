<?php
require_once 'config.php';
require_login(true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok'=>false,'msg'=>'Metode tidak valid'], 405);

$in = json_decode(file_get_contents('php://input'), true) ?: [];
$items = $in['items'] ?? [];
$jenis = $in['jenis_pesanan'] ?? 'Makan di tempat';
$metode = $in['pembayaran'] ?? 'Tunai';
$bayar = (int)($in['bayar'] ?? 0);

if (!in_array($jenis, ['Makan di tempat','Dibungkus'], true)) json_out(['ok'=>false,'msg'=>'Jenis pesanan tidak valid'], 422);
if (!in_array($metode, ['Tunai','QRIS'], true)) json_out(['ok'=>false,'msg'=>'Jenis pembayaran tidak valid'], 422);
if (!$items) json_out(['ok'=>false,'msg'=>'Pilih minimal satu menu.'], 422);

$pdo = db();
try {
    $pdo->beginTransaction();
    $detail = []; $total = 0;
    $q = $pdo->prepare('SELECT id, nama, harga, is_soto FROM menu WHERE id = ? AND aktif = 1');
    foreach ($items as $it) {
        $qty = (int)($it['qty'] ?? 0);
        if ($qty < 1) continue;
        $q->execute([(int)$it['id']]);
        $m = $q->fetch();
        if (!$m) throw new RuntimeException('Ada menu yang tidak tersedia.');
        $detail[] = [$m, $qty];
        $total += $m['harga'] * $qty;   // harga selalu dihitung ulang dari database
    }
    if (!$detail) throw new RuntimeException('Pilih minimal satu menu.');

    if ($metode === 'QRIS') { $bayar = $total; $kembali = 0; }
    else {
        if ($bayar < $total) throw new RuntimeException('Jumlah bayar kurang dari total.');
        $kembali = $bayar - $total;
    }

    $max = (int)$pdo->query("SELECT COALESCE(MAX(CAST(SUBSTRING(kode, 5) AS UNSIGNED)), -1) FROM transaksi FOR UPDATE")->fetchColumn();
    $kode = 'TRX-' . str_pad((string)($max + 1), 4, '0', STR_PAD_LEFT);

    $pdo->prepare('INSERT INTO transaksi (kode, waktu, jenis_pesanan, pembayaran, total, bayar, kembalian) VALUES (?,NOW(),?,?,?,?,?)')
        ->execute([$kode, $jenis, $metode, $total, $bayar, $kembali]);
    $tid = (int)$pdo->lastInsertId();

    $ins = $pdo->prepare('INSERT INTO transaksi_detail (transaksi_id, menu_id, nama, harga, qty, is_soto) VALUES (?,?,?,?,?,?)');
    foreach ($detail as [$m, $qty]) $ins->execute([$tid, $m['id'], $m['nama'], $m['harga'], $qty, $m['is_soto']]);

    $pdo->commit();
    json_out(['ok'=>true,'kode'=>$kode,'jenis_pesanan'=>$jenis,'pembayaran'=>$metode,'total'=>$total,'bayar'=>$bayar,'kembalian'=>$kembali]);
} catch (RuntimeException $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_out(['ok'=>false,'msg'=>$ex->getMessage()], 422);
} catch (Throwable $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_out(['ok'=>false,'msg'=>'Terjadi kesalahan pada server.'], 500);
}
