(function () {
  const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
  const $ = s => document.querySelector(s);
  const cart = {}; // id -> {nama, harga, qty}

  const cards = document.querySelectorAll('.menu-card');
  const elItems = $('#items'), elSub = $('#subtotal'), elTotal = $('#total');
  const elBayar = $('#bayar'), elKemb = $('#kembalian'), elErr = $('#pesanError');

  const metode = () => document.querySelector('input[name=metode]:checked').value;
  const total = () => Object.values(cart).reduce((s, i) => s + i.harga * i.qty, 0);
  const bayarVal = () => parseInt((elBayar.value || '').replace(/\D/g, ''), 10) || 0;

  function setQty(card, delta) {
    const id = card.dataset.id;
    const cur = cart[id] ? cart[id].qty : 0;
    const q = Math.max(0, cur + delta);
    if (q === 0) delete cart[id];
    else cart[id] = { nama: card.dataset.nama, harga: +card.dataset.harga, qty: q };
    card.querySelector('.qval').textContent = q;
    card.classList.toggle('picked', q > 0);
    render();
  }

  function render() {
    const ids = Object.keys(cart);
    elItems.innerHTML = ids.length ? '' : '<p class="muted">Belum ada pesanan</p>';
    ids.forEach(id => {
      const i = cart[id];
      const d = document.createElement('div');
      d.className = 'row';
      d.innerHTML = '<span></span><b></b>';
      d.children[0].textContent = i.nama + ' × ' + i.qty;
      d.children[1].textContent = rp(i.harga * i.qty);
      elItems.appendChild(d);
    });
    elSub.textContent = elTotal.textContent = rp(total());
    hitungKembali();
  }

  function hitungKembali() {
    const k = bayarVal() - total();
    elKemb.textContent = rp(k > 0 ? k : 0);
  }

  cards.forEach(c => {
    c.querySelector('.plus').onclick = () => setQty(c, 1);
    c.querySelector('.minus').onclick = () => setQty(c, -1);
  });

  document.querySelectorAll('input[name=metode]').forEach(r => r.onchange = () => {
    $('#tunaiBox').hidden = metode() === 'QRIS';
    elErr.hidden = true;
  });

  elBayar.addEventListener('input', () => {
    const n = bayarVal();
    elBayar.value = n ? rp(n) : '';
    hitungKembali();
  });

  function reset() {
    Object.keys(cart).forEach(k => delete cart[k]);
    cards.forEach(c => { c.querySelector('.qval').textContent = 0; c.classList.remove('picked'); });
    elBayar.value = '';
    $('#jenisPesanan').selectedIndex = 0;
    document.querySelector('input[name=metode][value=Tunai]').checked = true;
    $('#tunaiBox').hidden = false;
    render();
  }

  function error(msg) { elErr.textContent = msg; elErr.hidden = false; }

  $('#btnBayar').onclick = async () => {
    elErr.hidden = true;
    if (!Object.keys(cart).length) return error('Pilih minimal satu menu terlebih dahulu.');
    if (metode() === 'Tunai' && bayarVal() < total()) return error('Jumlah bayar kurang dari total.');

    const btn = $('#btnBayar'); btn.disabled = true;
    try {
      const res = await fetch('checkout-proses.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          items: Object.entries(cart).map(([id, i]) => ({ id: +id, qty: i.qty })),
          jenis_pesanan: $('#jenisPesanan').value,
          pembayaran: metode(),
          bayar: bayarVal()
        })
      });
      const d = await res.json();
      if (!d.ok) return error(d.msg || 'Pembayaran gagal.');

      $('#okInfo').textContent = d.kode + ' · ' + d.jenis_pesanan + ' · ' + d.pembayaran;
      $('#okTotal').textContent = rp(d.total);
      const tunai = d.pembayaran === 'Tunai';
      ['#okBayarRow', '#okHr', '#okKembaliRow'].forEach(s => $(s).hidden = !tunai);
      $('#okBayar').textContent = rp(d.bayar);
      $('#okKembali').textContent = rp(d.kembalian);
      $('#dlgSukses').hidden = false;
    } catch (e) {
      error('Tidak dapat terhubung ke server.');
    } finally { btn.disabled = false; }
  };

  $('#btnBaru').onclick = () => { $('#dlgSukses').hidden = true; reset(); };
  render();
})();
