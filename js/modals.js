// Modal handlers untuk edit data (pakai event delegation agar kompatibel dengan Simple-DataTables)
document.addEventListener('DOMContentLoaded', function() {
    // Delegasi klik umumnya pada dokumen
    document.addEventListener('click', function(e) {
        const btnEditMasuk = e.target.closest('.btn-edit');
        if (btnEditMasuk) {
            const id = btnEditMasuk.dataset.id;
            const tanggal = btnEditMasuk.dataset.tanggal;
            const eventName = btnEditMasuk.dataset.event;
            const keterangan = btnEditMasuk.dataset.keterangan;
            const nominal = btnEditMasuk.dataset.nominal;

            const idEl = document.getElementById('edit-id');
            const tglEl = document.getElementById('edit-tanggal');
            const evtEl = document.getElementById('edit-event');
            const ketEl = document.getElementById('edit-keterangan');
            const nomEl = document.getElementById('edit-nominal');
            if (idEl && tglEl && evtEl && ketEl && nomEl) {
                idEl.value = id ?? '';
                tglEl.value = tanggal ?? '';
                evtEl.value = eventName ?? '';
                ketEl.value = keterangan ?? '';
                nomEl.value = nominal ?? '';
            }

            const modalEl = document.getElementById('editKasMasukModal');
            if (modalEl) new bootstrap.Modal(modalEl).show();
            return;
        }

        const btnEditKeluar = e.target.closest('.btn-edit-pengeluaran');
        if (btnEditKeluar) {
            const id = btnEditKeluar.dataset.id;
            const tanggal = btnEditKeluar.dataset.tanggal;
            const eventName = btnEditKeluar.dataset.event;
            const keterangan = btnEditKeluar.dataset.keterangan;
            const namaAkun = btnEditKeluar.dataset.nama_akun;
            const nominal = btnEditKeluar.dataset.nominal;

            const idEl = document.getElementById('edit-id-pengeluaran');
            const tglEl = document.getElementById('edit-tanggal-pengeluaran');
            const evtEl = document.getElementById('edit-event-pengeluaran');
            const ketEl = document.getElementById('edit-keterangan-pengeluaran');
            const akunEl = document.getElementById('edit-nama-akun-pengeluaran');
            const nomEl = document.getElementById('edit-nominal-pengeluaran');
            if (idEl && tglEl && evtEl && ketEl && akunEl && nomEl) {
                idEl.value = id ?? '';
                tglEl.value = tanggal ?? '';
                evtEl.value = eventName ?? '';
                ketEl.value = keterangan ?? '';
                akunEl.value = namaAkun ?? '';
                nomEl.value = nominal ?? '';
            }

            const modalEl = document.getElementById('editKasKeluarModal');
            if (modalEl) new bootstrap.Modal(modalEl).show();
            return;
        }

        const btnEditBooking = e.target.closest('.btn-edit-booking');
        if (btnEditBooking) {
            const id = btnEditBooking.dataset.id;
            const tanggal = btnEditBooking.dataset.tanggal;
            const eventName = btnEditBooking.dataset.event;
            const paket = btnEditBooking.dataset.paket;

            const idEl = document.getElementById('edit-id-booking');
            const tglEl = document.getElementById('edit-tanggal-booking');
            const evtEl = document.getElementById('edit-event-booking');
            const paketEl = document.getElementById('edit-paket-booking');
            if (idEl && tglEl && evtEl && paketEl) {
                idEl.value = id ?? '';
                tglEl.value = tanggal ?? '';
                evtEl.value = eventName ?? '';
                paketEl.value = paket ?? '';
            }

            const modalEl = document.getElementById('editBookingModal');
            if (modalEl) new bootstrap.Modal(modalEl).show();
        }
    });

    // Aktifkan datatable
    document.querySelectorAll('.datatable').forEach(table => {
        try {
            new simpleDatatables.DataTable(table);
        } catch (e) {
            console.warn('DataTable init skipped:', e);
        }
    });
});
