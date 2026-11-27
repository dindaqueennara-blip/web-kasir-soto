<?php
require_once 'config.php';
require_login();
$halaman = 'menu'; $judul = 'Menu';
include 'header.php';
?>
<div class="title-row">
  <h1 class="page-title" style="margin:0">MANAJEMEN MENU</h1>
  <button type="button" class="btn btn-yellow" id="btnTambah">+ Tambah Menu Baru</button>
</div>

<table class="tbl tbl-menu">
  <colgroup><col style="width:7.9%"><col style="width:33.2%"><col style="width:17.4%"><col style="width:14.2%"><col></colgroup>
  <thead><tr><th>Gambar</th><th>Nama Menu</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
  <tbody id="tbody"></tbody>
</table>

<!-- Dialog tambah / edit menu -->
<div class="overlay" id="dlgMenu" hidden>
  <form class="dialog" id="formMenu" enctype="multipart/form-data" onsubmit="return false">
    <h3 id="mJudul">Tambah Menu Baru</h3>
    <input type="hidden" name="id" id="mId">
    <div class="fg"><label for="mNama">Nama menu</label><input type="text" name="nama" id="mNama" class="input" placeholder="Contoh: Sate Telur Puyuh" maxlength="100"></div>
    <div class="fg"><label for="mHarga">Harga (Rp)</label><input type="number" name="harga" id="mHarga" class="input" placeholder="3000" min="0" step="500"></div>
    <div class="fg"><span class="lbl">Gambar menu</span>
      <label class="upload" for="mGambar">
        <span class="upload-ico" id="mPreview">🖼️</span>
        <small id="mFileInfo">Pilih gambar (jpg/png, maks 10 MB)</small>
      </label>
      <input type="file" name="gambar" id="mGambar" accept="image/jpeg,image/png" hidden>
    </div>
    <p class="form-error" id="mError" hidden></p>
    <div class="actions">
      <button type="button" class="btn btn-primary" id="mSimpan">Simpan</button>
      <button type="button" class="btn btn-gray" id="mBatal">Batal</button>
    </div>
  </form>
</div>
<script src="menu.js"></script>
<?php include 'footer.php'; ?>
