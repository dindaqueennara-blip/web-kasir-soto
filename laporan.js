(function () {
  const $ = s => document.querySelector(s);
  const tbody = $('#tbody');


  async function muat(filter) {
    const q = new URLSearchParams(filter || {}).toString();
    const d = await (await fetch('transaksi-proses.php?aksi=list&' + q)).json();
    tbody.innerHTML = '';
    if (!d.rows.length) {
      tbody.innerHTML = '<tr><td colspan="5" class="empty">Tidak ada transaksi.</td></tr>';
    }
    d.rows.forEach((r, i) => {
      const tr = document.createElement('tr');
      tr.innerHTML = '<td></td><td></td><td class="pre"></td><td></td><td class="aksi"><button class="ico" title="Edit">✏️</button><button class="ico" title="Hapus">🗑️</button></td>';
      tr.children[0].textContent = i + 1;
      tr.children[1].textContent = r.kode;
      tr.children[2].textContent = r.waktu_label;
      tr.children[3].textContent = r.total_label;
      const [btnEdit, btnDel] = tr.querySelectorAll('button');
      btnEdit.onclick = () => bukaEdit(r);
      btnDel.onclick = () => hapus(r);
      tbody.appendChild(tr);
    });
    $('#totalTampil').textContent = 'Total ditampilkan: ' + d.total_label;
  }

  function filterSaatIni() {
    const f = {};
    if ($('#fTanggal').value) f.tanggal = $('#fTanggal').value;
    if ($('#fCari').value.trim()) f.cari = $('#fCari').value.trim();
    return f;
  }

  $('#btnTampil').onclick = () => muat(filterSaatIni());
  $('#btnSemua').onclick = () => {
    $('#fTanggal').value = $('#fCari').value = '';
    muat();
  };
  $('#fCari').addEventListener('keydown', e => { if (e.key === 'Enter') muat(filterSaatIni()); });

  // ----- edit -----
  const dlg = $('#dlgEdit');
  function bukaEdit(r) {
    $('#eId').value = r.id;
    $('#editJudul').textContent = 'Edit Transaksi ' + r.kode;
    $('#eWaktu').value = r.waktu_input;
    $('#eJenis').value = r.jenis_pesanan;
    $('#eBayar').value = r.pembayaran;
    $('#eError').hidden = true;
    dlg.hidden = false;
  }
  $('#eBatal').onclick = () => dlg.hidden = true;
  $('#eSimpan').onclick = async () => {
    const d = await (await fetch('transaksi-proses.php?aksi=update', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: +$('#eId').value, waktu: $('#eWaktu').value, jenis_pesanan: $('#eJenis').value, pembayaran: $('#eBayar').value })
    })).json();
    if (!d.ok) { $('#eError').textContent = d.msg; $('#eError').hidden = false; return; }
    dlg.hidden = true;
    muat(filterSaatIni());
  };

  async function hapus(r) {
    if (!confirm('Hapus transaksi ' + r.kode + '?')) return;
    await fetch('transaksi-proses.php?aksi=delete', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: r.id })
    });
    muat(filterSaatIni());
  }

  muat();
})();
