(function () {
  const $ = s => document.querySelector(s);
  const tbody = $('#tbody');
  const dlg = $('#dlgMenu');
  const post = (aksi, body) => fetch('menu-proses.php?aksi=' + aksi, {
    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body)
  }).then(r => r.json());

  async function muat() {
    const d = await (await fetch('menu-proses.php?aksi=list')).json();
    tbody.innerHTML = '';
    d.rows.forEach(m => {
      const aktif = m.aktif == 1;
      const tr = document.createElement('tr');
      if (!aktif) tr.className = 'off';
      tr.innerHTML = '<td></td><td></td><td></td><td></td><td><div class="aksi">' +
        '<button class="ico" title="Update">✏️</button>' +
        '<button class="ico" title="Hapus">🗑️</button>' +
        '<button class="teks"></button></div></td>';
      if (m.ada_gambar == 1) {
        const img = document.createElement('img');
        img.className = 'thumb'; img.src = 'gambar.php?id=' + m.id + '&v=' + Date.now(); img.alt = m.nama;
        tr.children[0].appendChild(img);
      } else tr.children[0].textContent = m.ikon;
      tr.children[1].textContent = m.nama;
      tr.children[2].textContent = 'Rp ' + m.harga;
      tr.children[3].textContent = aktif ? 'Aktif' : 'Nonaktif';
      const [bEdit, bDel, bTog] = tr.querySelectorAll('button');
      bTog.textContent = aktif ? 'Nonaktifkan' : 'Aktifkan';
      bEdit.onclick = () => buka(m);
      bTog.onclick = async () => { await post('toggle', { id: m.id }); muat(); };
      bDel.onclick = async () => {
        if (!confirm('Hapus menu "' + m.nama + '"?')) return;
        await post('hapus', { id: m.id }); muat();
      };
      tbody.appendChild(tr);
    });
  }

  function buka(m) {
    $('#formMenu').reset();
    $('#mId').value = m ? m.id : '';
    $('#mJudul').textContent = m ? 'Edit Menu' : 'Tambah Menu Baru';
    $('#mNama').value = m ? m.nama : '';
    $('#mHarga').value = m ? m.harga : '';
    $('#mPreview').textContent = '🖼️';
    $('#mFileInfo').textContent = m ? 'Pilih gambar baru (kosongkan jika tidak diganti)' : 'Pilih gambar (jpg/png, maks 10 MB)';
    $('#mError').hidden = true;
    dlg.hidden = false;
    $('#mNama').focus();
  }

  $('#btnTambah').onclick = () => buka(null);
  $('#mBatal').onclick = () => dlg.hidden = true;

  $('#mGambar').onchange = e => {
    const f = e.target.files[0];
    if (!f) return;
    $('#mFileInfo').textContent = f.name;
    if (/^image\/(jpeg|png)$/.test(f.type)) {
      const img = document.createElement('img');
      img.src = URL.createObjectURL(f);
      $('#mPreview').innerHTML = ''; $('#mPreview').appendChild(img);
    }
  };

  $('#mSimpan').onclick = async () => {
    const err = $('#mError');
    err.hidden = true;
    const nama = $('#mNama').value.trim(), harga = $('#mHarga').value;
    const f = $('#mGambar').files[0];
    if (!nama) { err.textContent = 'Nama menu wajib diisi.'; err.hidden = false; return; }
    if (harga === '' || +harga < 0) { err.textContent = 'Harga harus diisi dengan angka.'; err.hidden = false; return; }
    if (f && f.size > 10 * 1024 * 1024) { err.textContent = 'Ukuran gambar maksimal 10 MB.'; err.hidden = false; return; }
    const res = await (await fetch('menu-proses.php?aksi=simpan', { method: 'POST', body: new FormData($('#formMenu')) })).json();
    if (!res.ok) { err.textContent = res.msg; err.hidden = false; return; }
    dlg.hidden = true;
    muat();
  };

  muat();
})();
