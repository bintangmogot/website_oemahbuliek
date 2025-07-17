 @csrf

    {{-- 1. Tambahkan elemen untuk menampilkan pesan dan Modal Loading --}}
    <div id="form-message-container"></div>

    <div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered border-0">
            <div class="modal-content" style="background-color: transparent; border: none;">
                <div class="modal-body text-center">
                    <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-white fw-bold">Memproses...</p>
                </div>
            </div>
        </div>
    </div>
    {{-- Akhir dari elemen baru --}}


    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Gunakan form ini untuk mencatat semua jenis transaksi stok: barang masuk dari supplier, penggunaan untuk produksi, atau barang yang rusak.
    </div>

    {{-- ... sisa kode HTML form Anda tidak perlu diubah ... --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="tipe_mutasi" class="form-label">Tipe Transaksi</label>
            <select name="tipe_mutasi" id="tipe_mutasi" class="form-select" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="masuk" {{ old('tipe_mutasi') == 'masuk' ? 'selected' : '' }}>Stok Masuk (Pembelian)</option>
                <option value="produksi" {{ old('tipe_mutasi') == 'produksi' ? 'selected' : '' }}>Stok Keluar (Produksi)</option>
                <option value="rusak" {{ old('tipe_mutasi') == 'rusak' ? 'selected' : '' }}>Stok Keluar (Rusak)</option>
                <option value="penyesuaian" {{ old('tipe_mutasi') == 'penyesuaian' ? 'selected' : '' }}>Stok Keluar (Penyesuaian)</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="tanggal" class="form-label">Tanggal Transaksi</label>
           <input type="datetime-local" name="tanggal" id="tanggal" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" max="{{ now()->format('Y-m-d\TH:i') }}" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 mb-3">
            <label for="bahan_baku_id" class="form-label">Bahan Baku</label>
            <select name="bahan_baku_id" id="bahan_baku_id" class="form-select" required>
                <option value="">-- Pilih Bahan Baku --</option>
                @foreach($bahanBakus as $bahan)
                    <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan_label }}" {{ old('bahan_baku_id') == $bahan->id ? 'selected' : '' }}>
                        {{ $bahan->nama }} (Stok: {{ $bahan->stok_terkini }} {{ $bahan->satuan_label }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label for="kuantitas" class="form-label">Kuantitas</label>
            <div class="input-group">
                <input type="number" name="kuantitas" id="kuantitas" class="form-control" value="{{ old('kuantitas') }}" required min="1">
                <span class="input-group-text" id="satuan-label">satuan</span>
            </div>
        </div>
    </div>
    <div class="row" id="harga-field" style="display: none;">
        <div class="col-md-12 mb-3">
            <label for="total_harga" class="form-label">Total Harga Pembelian (dari Struk)</label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="total_harga" id="total_harga" class="form-control" value="{{ old('total_harga') }}">
            </div>
            <small class="form-text text-muted">Hanya diisi untuk transaksi 'Stok Masuk'.</small>
        </div>
    </div>
    <div class="mb-3">
        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
        <textarea name="keterangan" id="keterangan" rows="3" class="form-control">{{ old('keterangan') }}</textarea>
        <small class="form-text text-muted">Contoh: 'Dari Supplier A', 'Untuk pesanan besar', 'Pecah saat pengiriman'.</small>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('riwayat-stok-form');
        const messageContainer = document.getElementById('form-message-container');
        const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
        
        const tipeMutasiSelect = document.getElementById('tipe_mutasi');
        const hargaField = document.getElementById('harga-field');
        const bahanBakuSelect = document.getElementById('bahan_baku_id');
        const satuanLabel = document.getElementById('satuan-label');
        const tomSelect = new TomSelect("#bahan_baku_id",{
            create: false,
            sortField: { field: "text", direction: "asc" }
        });

        function toggleHargaField() {
            hargaField.style.display = (tipeMutasiSelect.value === 'masuk') ? 'block' : 'none';
        }

        function updateSatuanLabel() {
            const selectedOption = bahanBakuSelect.options[bahanBakuSelect.selectedIndex];
            satuanLabel.textContent = (selectedOption && selectedOption.value) ? selectedOption.getAttribute('data-satuan') : 'satuan';
        }

        tipeMutasiSelect.addEventListener('change', toggleHargaField);
        bahanBakuSelect.addEventListener('change', updateSatuanLabel);
        toggleHargaField();
        updateSatuanLabel();

        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit cara biasa
            
            loadingModal.show(); // Tampilkan loading
            messageContainer.innerHTML = ''; // Kosongkan pesan lama

            const formData = new FormData(form);
            const actionUrl = form.getAttribute('action');

            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                // Selalu coba parse response sebagai JSON, lalu periksa status 'ok'
                return response.json().then(data => {
                    if (!response.ok) {
                        // Jika status tidak ok (misal: 422, 500), lempar error agar ditangkap .catch()
                        // Kita sertakan 'data' agar pesan error dari server bisa ditampilkan
                        return Promise.reject(data);
                    }
                    // Jika ok, teruskan data ke .then() berikutnya
                    return data;
                });
            })
            .then(data => {
                // Blok ini sekarang HANYA akan berjalan untuk respons yang sukses (status 2xx)
                messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                form.reset(); // Reset isi form
                tomSelect.clear(); // Reset pilihan TomSelect
                updateSatuanLabel(); // Update label satuan ke default
            })
            .catch(errorData => {
                // Blok ini sekarang menangani SEMUA jenis error: network, validasi, server error
                console.error('Error:', errorData);
                let errorHtml = '<div class="alert alert-danger"><ul>';
                if (errorData && errorData.errors) {
                    // Menangani error validasi dari Laravel (422)
                    Object.values(errorData.errors).forEach(error => {
                        errorHtml += `<li>${error[0]}</li>`;
                    });
                } else if (errorData && errorData.message) {
                    // Menangani error umum dari server (500)
                    errorHtml += `<li>${errorData.message}</li>`;
                } else {
                    // Menangani error network atau error tak terduga lainnya
                    errorHtml += `<li>Terjadi kesalahan tidak dikenal. Silakan cek konsol.</li>`;
                }
                errorHtml += '</ul></div>';
                messageContainer.innerHTML = errorHtml;
            })
            .finally(() => {
                // Blok ini DIJAMIN akan selalu berjalan, baik setelah .then() maupun .catch()
                loadingModal.hide();
                // Scroll ke atas agar user melihat pesan
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    });
    </script>
    @endpush