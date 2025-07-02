@php
  $item = $item ?? null;
@endphp

@csrf
{{-- For edit, send PUT method --}}
@if($item)
  @method('PUT')
@endif

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Bahan Baku</label>
            <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', optional($item)->nama) }}" required>
            @error('nama')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="kategori" class="form-label">Kategori</label>
            <select name="kategori" id="kategori" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Bahan Makanan" {{ old('kategori', optional($item)->kategori) == 'Bahan Makanan' ? 'selected' : '' }}>Bahan Makanan</option>
                <option value="Bumbu" {{ old('kategori', optional($item)->kategori) == 'Bumbu' ? 'selected' : '' }}>Bumbu</option>
                <option value="Bahan Minuman" {{ old('kategori', optional($item)->kategori) == 'Bahan Minuman' ? 'selected' : '' }}>Bahan Minuman</option>
            </select>
            @error('kategori')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="satuan" class="form-label">Satuan</label>
            <select name="satuan" id="satuan" class="form-select" required>
                <option value="">-- Pilih Satuan --</option>
                <option value="0" {{ old('satuan', optional($item)->satuan) == '0' ? 'selected' : '' }}>Gram (gr)</option>
                <option value="1" {{ old('satuan', optional($item)->satuan) == '1' ? 'selected' : '' }}>Pcs</option>
            </select>
            @error('satuan')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="stok_minimum" class="form-label">Stok Minimum Peringatan</label>
            <input type="number" name="stok_minimum" id="stok_minimum" class="form-control" value="{{ old('stok_minimum', optional($item)->stok_minimum) }}" required>
            @error('stok_minimum')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
    </div>
</div>