<?php
require_once 'config.php';
require_login(true);
$pdo = db();
$aksi = $_GET['aksi'] ?? 'list';

if ($aksi === 'list') {
    $where = []; $p = [];
    if (!empty($_GET['tanggal']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['tanggal'])) {
        $where[] = 'DATE(waktu) = ?'; $p[] = $_GET['tanggal'];
    }
    $cari = trim($_GET['cari'] ?? '');
    if ($cari !== '') {
        $where[] = "(kode LIKE ? OR DATE_FORMAT(waktu,'%d/%m/%Y %H:%i') LIKE ? OR DATE_FORMAT(waktu,'%Y-%m-%d %H:%i') LIKE ? OR DATE_FORMAT(waktu,'%H:%i') LIKE ?)";
        $l = '%'.$cari.'%'; array_push($p, $l, $l, $l, $l);
    }
    $sql = 'SELECT id, kode, waktu, jenis_pesanan, pembayaran, total FROM transaksi'
         . ($where ? ' WHERE '.implode(' AND ', $where) : '') . ' ORDER BY waktu DESC, id DESC';
    $st = $pdo->prepare($sql); $st->execute($p);
    $rows = $st->fetchAll(); $sum = 0;
    foreach ($rows as &$r) {
        $sum += (int)$r['total'];
        $r['waktu_label'] = tgl_indo($r['waktu']);
        $r['waktu_input'] = date('Y-m-d\TH:i', strtotime($r['waktu']));
        $r['total_label'] = rupiah((int)$r['total']);
    }
    json_out(['ok'=>true,'rows'=>$rows,'total'=>$sum,'total_label'=>rupiah($sum)]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok'=>false,'msg'=>'Metode tidak valid'], 405);
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$id = (int)($in['id'] ?? 0);

if ($aksi === 'update') {
    $waktu = str_replace('T', ' ', $in['waktu'] ?? '');
    $d = DateTime::createFromFormat('Y-m-d H:i', $waktu);
    if (!$d || $d->format('Y-m-d H:i') !== $waktu) json_out(['ok'=>false,'msg'=>'Tanggal & waktu tidak valid.'], 422);
    $jenis = $in['jenis_pesanan'] ?? ''; $bayar = $in['pembayaran'] ?? '';
    if (!in_array($jenis, ['Makan di tempat','Dibungkus'], true) || !in_array($bayar, ['Tunai','QRIS'], true))
        json_out(['ok'=>false,'msg'=>'Data tidak valid.'], 422);
    $pdo->prepare('UPDATE transaksi SET waktu=?, jenis_pesanan=?, pembayaran=? WHERE id=?')->execute([$waktu.':00', $jenis, $bayar, $id]);
    json_out(['ok'=>true]);
}
if ($aksi === 'delete') {
    $pdo->prepare('DELETE FROM transaksi WHERE id=?')->execute([$id]);
    json_out(['ok'=>true]);
}
json_out(['ok'=>false,'msg'=>'Aksi tidak dikenal'], 400);
