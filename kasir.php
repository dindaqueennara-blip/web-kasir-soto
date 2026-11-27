<?php
require_once 'config.php';
require_login();
$halaman = 'kasir'; $judul = 'Kasir';
$menus = db()->query("SELECT id, nama, harga, ikon, (gambar IS NOT NULL) AS ada_gambar FROM menu WHERE aktif = 1 ORDER BY id")->fetchAll();
include 'header.php';
?>
<h1 class="page-title">KASIR</h1>
<div class="kasir-layout">
  <section class="menu-grid" id="menuGrid">
    <?php foreach ($menus as $m): ?>
    <div class="menu-card" data-id="<?= $m['id'] ?>" data-nama="<?= e($m['nama']) ?>" data-harga="<?= $m['harga'] ?>">
      <div class="menu-img">
        <?php if ($m['ada_gambar']): ?><img src="gambar.php?id=<?= $m['id'] ?>" alt="<?= e($m['nama']) ?>"><?php else: ?><?= e($m['ikon']) ?><?php endif; ?>
      </div>
      <div class="menu-info"><b><?= e($m['nama']) ?></b><span>Rp <?= $m['harga'] ?></span></div>
      <div class="qty">
        <button type="button" class="qbtn minus">−</button>
        <span class="qval">0</span>
        <button type="button" class="qbtn plus">+</button>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (!$menus): ?><p class="empty">Belum ada menu aktif.</p><?php endif; ?>
  </section>

  <aside class="summary">
    <h2>Ringkasan Pesanan</h2>
    <div id="items" class="items"><p class="muted">Belum ada pesanan</p></div>

    <label class="lbl" for="jenisPesanan">Jenis pesanan</label>
    <select id="jenisPesanan" class="input">
      <option>Makan di tempat</option>
      <option>Dibungkus</option>
    </select>

    <div class="row"><span>Subtotal</span><b id="subtotal">Rp 0</b></div>
    <div class="row total"><span>Total</span><b id="total">Rp 0</b></div>

    <span class="lbl">Jenis pembayaran</span>
    <div class="radios">
      <label><input type="radio" name="metode" value="Tunai" checked> Tunai</label>
      <label><input type="radio" name="metode" value="QRIS"> QRIS</label>
    </div>

    <div id="tunaiBox">
      <label class="lbl" for="bayar">Jumlah bayar</label>
      <input type="text" id="bayar" class="input" inputmode="numeric" placeholder="Rp 0" autocomplete="off">
      <div class="row" style="margin-top:10px"><span>Kembalian</span><b id="kembalian">Rp 0</b></div>
    </div>

    <button type="button" id="btnBayar" class="btn btn-primary btn-block">Proses Pembayaran</button>
    <p id="pesanError" class="form-error" hidden></p>
  </aside>
</div>

<!-- Dialog pembayaran berhasil -->
<div class="overlay" id="dlgSukses" hidden>
  <div class="dialog dialog-sukses">
    <svg class="ok-icon" viewBox="0 0 72 72" width="72" height="72"><circle cx="36" cy="36" r="36" fill="#0b5411"/><path d="M21 37l10 10 20-22" fill="none" stroke="#ced111" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <div class="ok-title"><b>Pembayaran Berhasil</b><span id="okInfo"></span></div>
    <div class="ok-box">
      <div class="row"><span>Total</span><b id="okTotal"></b></div>
      <div class="row" id="okBayarRow"><span>Dibayar</span><b id="okBayar"></b></div>
      <hr id="okHr">
      <div class="row ok-kembali" id="okKembaliRow"><span>Kembalian</span><b id="okKembali"></b></div>
    </div>
    <button type="button" id="btnBaru" class="btn btn-primary btn-block btn-lg">Pesanan Baru</button>
  </div>
</div>
<script src="kasir.js"></script>
<?php include 'footer.php'; ?>
