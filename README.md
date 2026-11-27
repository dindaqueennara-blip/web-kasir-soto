# Sistem Kasir – Soto Semarang Tan Nyana

HTML + CSS + JavaScript + PHP (PDO) + MySQL.

## Cara menjalankan (XAMPP / Laragon)
1. Salin folder `soto-tan-nyana` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Jalankan Apache dan MySQL.
3. Buka phpMyAdmin → tab **Import** → pilih `soto-tan-nyana.sql` → Go.
4. Sesuaikan kredensial MySQL di `config.php` bila perlu (default: root, tanpa password).
5. Buka `http://localhost/soto_tan_nyana/`
6. Login: **kasir** / **kasir123**

Gambar menu disimpan di database (kolom `gambar`), jadi tidak perlu folder upload.
Bila upload gambar gagal, naikkan `max_allowed_packet` MySQL (minimal 16M).

## Struktur (semua file di satu folder)
- Halaman: `index.php` (login), `dashboard.php`, `kasir.php`, `laporan.php`, `menu.php`, `logout.php`
- Tampilan bersama: `header.php`, `footer.php`, `style.css`, gambar SVG (`latar-soto.svg`, `logo-soto.svg`, `mangkuk-header.svg`, `gelombang-header.svg`)
- Skrip halaman: `kasir.js`, `laporan.js`, `menu.js`
- Pemrosesan data: `checkout-proses.php`, `transaksi-proses.php`, `menu-proses.php`, `gambar.php`
- Konfigurasi & database: `config.php`, `soto-tan-nyana.sql`