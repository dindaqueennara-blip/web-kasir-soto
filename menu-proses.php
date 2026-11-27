<?php
require_once 'config.php';
require_login(true);
$pdo = db();
$aksi = $_GET['aksi'] ?? 'list';

function baca_gambar(array $f): ?array {
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload gambar gagal.');
    if ($f['size'] > 10 * 1024 * 1024) throw new RuntimeException('Ukuran gambar maksimal 10 MB.');
    $info = @getimagesize($f['tmp_name']);
    $mime = $info['mime'] ?? '';
    if (!in_array($mime, ['image/jpeg', 'image/png'], true)) throw new RuntimeException('Gambar harus berformat jpg atau png.');
    return [file_get_contents($f['tmp_name']), $mime];
}

if ($aksi === 'list') {
    $rows = $pdo->query('SELECT id, nama, harga, ikon, (gambar IS NOT NULL) AS ada_gambar, aktif FROM menu ORDER BY id')->fetchAll();
    json_out(['ok'=>true,'rows'=>$rows]);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok'=>false,'msg'=>'Metode tidak valid'], 405);

try {
    if ($aksi === 'simpan') {
        $id = (int)($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        $harga = $_POST['harga'] ?? '';
        if ($nama === '') throw new RuntimeException('Nama menu wajib diisi.');
        if ($harga === '' || !ctype_digit((string)$harga)) throw new RuntimeException('Harga harus berupa angka.');
        $harga = (int)$harga;
        $gambar = baca_gambar($_FILES['gambar'] ?? []);
        $is_soto = stripos($nama, 'soto') === 0 ? 1 : 0;

        if ($id) {
            if ($gambar) {
                $st = $pdo->prepare('UPDATE menu SET nama=?, harga=?, is_soto=?, gambar=?, gambar_tipe=? WHERE id=?');
                $st->bindValue(1, $nama); $st->bindValue(2, $harga, PDO::PARAM_INT); $st->bindValue(3, $is_soto, PDO::PARAM_INT);
                $st->bindValue(4, $gambar[0], PDO::PARAM_LOB); $st->bindValue(5, $gambar[1]); $st->bindValue(6, $id, PDO::PARAM_INT);
                $st->execute();
            } else {
                $pdo->prepare('UPDATE menu SET nama=?, harga=?, is_soto=? WHERE id=?')->execute([$nama,$harga,$is_soto,$id]);
            }
        } else {
            $ikon = $is_soto ? '🍜' : (preg_match('/^(es|teh|jeruk|kopi)/i', $nama) ? '🥤' : '🍢');
            $st = $pdo->prepare('INSERT INTO menu (nama, harga, ikon, gambar, gambar_tipe, is_soto, aktif) VALUES (?,?,?,?,?,?,1)');
            $st->bindValue(1, $nama); $st->bindValue(2, $harga, PDO::PARAM_INT); $st->bindValue(3, $ikon);
            $st->bindValue(4, $gambar ? $gambar[0] : null, $gambar ? PDO::PARAM_LOB : PDO::PARAM_NULL);
            $st->bindValue(5, $gambar ? $gambar[1] : null, $gambar ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $st->bindValue(6, $is_soto, PDO::PARAM_INT);
            $st->execute();
        }
        json_out(['ok'=>true]);
    }
    $in = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int)($in['id'] ?? 0);
    if ($aksi === 'toggle') {
        $pdo->prepare('UPDATE menu SET aktif = 1 - aktif WHERE id=?')->execute([$id]);
        json_out(['ok'=>true]);
    }
    if ($aksi === 'hapus') {
        $pdo->prepare('DELETE FROM menu WHERE id=?')->execute([$id]);
        json_out(['ok'=>true]);
    }
    json_out(['ok'=>false,'msg'=>'Aksi tidak dikenal'], 400);
} catch (RuntimeException $ex) {
    json_out(['ok'=>false,'msg'=>$ex->getMessage()], 422);
} catch (Throwable $ex) {
    json_out(['ok'=>false,'msg'=>'Terjadi kesalahan pada server.'], 500);
}
