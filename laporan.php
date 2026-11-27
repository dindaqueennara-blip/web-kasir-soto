<?php
require_once 'config.php';
require_login();
$halaman = 'laporan'; $judul = 'Laporan';
include 'header.php';
?>
<h1 class="page-title">LAPORAN PENJUALAN</h1>
<form class="filter" id="filterForm" onsubmit="return false">
  <div class="fg"><label for="fTanggal">Pilih tanggal</label><input type="date" id="fTanggal" class="input" style="width:220px"></div>
  <div class="fg"><label for="fCari">Cari ID / waktu</label><input type="text" id="fCari" class="input" style="width:220px" placeholder="TRX-0005 atau 14:30"></div>
  <button type="button" id="btnTampil" class="btn btn-primary">Tampilkan</button>
  <button type="button" id="btnSemua" class="btn btn-gray">Semua</button>
</form>

<table class="tbl tbl-laporan">
  <colgroup><col style="width:6.3%"><col style="width:20.6%"><col style="width:33.2%"><col style="width:20.6%"><col></colgroup>
  <thead><tr><th>No</th><th>ID Transaksi</th><th>Tanggal &amp; Waktu</th><th>Total</th><th>Aksi</th></tr></thead>
  <tbody id="tbody"></tbody>
</table>
<p class="total-tampil" id="totalTampil">Total ditampilkan: Rp 0</p>

<!-- Dialog edit transaksi -->
<div class="overlay" id="dlgEdit" hidden>
  <div class="dialog">
    <h3 id="editJudul">Edit Transaksi</h3>
    <input type="hidden" id="eId">
    <div class="fg"><label for="eWaktu">Tanggal &amp; waktu</label><input type="datetime-local" id="eWaktu" class="input"></div>
    <div class="fg"><label for="eJenis">Jenis pesanan</label>
      <select id="eJenis" class="input"><option>Makan di tempat</option><option>Dibungkus</option></select></div>
    <div class="fg"><label for="eBayar">Jenis pembayaran</label>
      <select id="eBayar" class="input"><option>Tunai</option><option>QRIS</option></select></div>
    <p class="form-error" id="eError" hidden></p>
    <div class="actions">
      <button type="button" class="btn btn-primary" id="eSimpan">Simpan</button>
      <button type="button" class="btn btn-gray" id="eBatal">Batal</button>
    </div>
  </div>
</div>
<script src="laporan.js"></script>
<?php include 'footer.php'; ?>
