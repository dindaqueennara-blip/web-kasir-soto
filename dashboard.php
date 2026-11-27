<?php
require_once 'config.php';
require_login();
$halaman = 'dashboard'; $judul = 'Dashboard';

$pdo = db();
$hari = $pdo->query("SELECT COALESCE(SUM(total),0) AS total, COUNT(*) AS jml FROM transaksi WHERE DATE(waktu) = CURDATE()")->fetch();
$mangkok = (int)$pdo->query("SELECT COALESCE(SUM(d.qty),0) FROM transaksi_detail d JOIN transaksi t ON t.id=d.transaksi_id WHERE d.is_soto=1 AND DATE(t.waktu)=CURDATE()")->fetchColumn();
$terbaru = $pdo->query("SELECT kode, waktu, pembayaran, total FROM transaksi ORDER BY waktu DESC, id DESC LIMIT 10")->fetchAll();

include 'header.php';
?>
<h1 class="page-title">DASHBOARD</h1>
<section class="cards">
  <div class="card"><span class="card-label">Total Penjualan</span><span class="card-value"><?= rupiah((int)$hari['total']) ?></span></div>
  <div class="card"><span class="card-label">Jumlah Transaksi</span><span class="card-value"><?= (int)$hari['jml'] ?></span></div>
  <div class="card"><span class="card-label">Mangkok Soto Terjual</span><span class="card-value"><?= $mangkok ?> mangkok</span></div>
</section>

<section>
  <div class="section-head">
    <strong>10 Transaksi Terbaru</strong>
    <small>Klik baris untuk membuka halaman Laporan →</small>
  </div>
  <table class="tbl tbl-click" id="tblTerbaru">
    <colgroup><col style="width:20.6%"><col style="width:33.2%"><col style="width:23.7%"><col></colgroup>
    <thead><tr><th>ID Transaksi</th><th>Tanggal &amp; Waktu</th><th>Pembayaran</th><th>Total</th></tr></thead>
    <tbody>
    <?php foreach ($terbaru as $r): ?>
      <tr onclick="location.href='laporan.php'">
        <td><?= e($r['kode']) ?></td>
        <td class="pre"><?= e(tgl_indo($r['waktu'])) ?></td>
        <td><?= e($r['pembayaran']) ?></td>
        <td><?= rupiah((int)$r['total']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$terbaru): ?><tr><td colspan="4" class="empty">Belum ada transaksi.</td></tr><?php endif; ?>
    </tbody>
  </table>
</section>
<?php include 'footer.php'; ?>
