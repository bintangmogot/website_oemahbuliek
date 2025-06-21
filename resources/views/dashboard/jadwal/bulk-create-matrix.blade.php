{{-- resources/views/dashboard/jadwal-shift/bulk-create-matrix.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Jadwal Pegawai - {{ \Carbon\Carbon::now()->format('F Y') }}</h3>
                    <div>
                        <button type="button" class="btn btn-sm btn-info" onclick="selectAllShift('pagi')">
                            <i class="fas fa-sun"></i> Pilih Semua Pagi
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" onclick="selectAllShift('sore')">
                            <i class="fas fa-moon"></i> Pilih Semua Sore
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="clearAll()">
                            <i class="fas fa-times"></i> Clear All
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <form action="{{ route('jadwal-shift.bulk-store-matrix') }}" method="POST" id="matrixForm">
                        @csrf
                        
                        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="thead-dark sticky-top">
                                    <tr>
                                        <th style="min-width: 150px; position: sticky; left: 0; z-index: 3; background-color: #343a40;">
                                            Pegawai
                                        </th>
                                        @php
                                            $startDate = \Carbon\Carbon::now()->startOfMonth();
                                            $endDate = \Carbon\Carbon::now()->endOfMonth();
                                        @endphp
                                        
                                        @for($date = $startDate->copy(); $date->lte($endDate); $date->addDay())
                                            <th class="text-center" style="min-width: 120px;">
                                                <div class="d-flex flex-column">
                                                    <small class="font-weight-bold">
                                                        {{ $date->format('d') }}
                                                    </small>
                                                    <small class="text-muted">
                                                        {{ $date->format('D') }}
                                                    </small>
                                                </div>
                                            </th>
                                        @endfor
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @foreach($pegawais as $pegawai)
                                    <tr>
                                        <td class="font-weight-bold bg-light" 
                                            style="position: sticky; left: 0; z-index: 2; background-color: #f8f9fa !important;">
                                            {{ $pegawai->nama }}
                                            <br>
                                            <small class="text-muted">{{ $pegawai->jabatan }}</small>
                                        </td>
                                        
                                        @for($date = $startDate->copy(); $date->lte($endDate); $date->addDay())
                                            @php
                                                $isWeekend = in_array($date->dayOfWeek, [0, 6]); // Sunday = 0, Saturday = 6
                                                $dateString = $date->format('d-m-Y');
                                                
                                                // Check existing jadwal
                                                $existingPagi = $existingJadwals->where('pegawai_id', $pegawai->id)
                                                                              ->where('tanggal', $dateString)
                                                                              ->where('shift.nama_shift', 'Pagi')
                                                                              ->first();
                                                                              
                                                $existingSore = $existingJadwals->where('pegawai_id', $pegawai->id)
                                                                              ->where('tanggal', $dateString)
                                                                              ->where('shift.nama_shift', 'Sore')
                                                                              ->first();
                                            @endphp
                                            
                                            <td class="text-center p-1 {{ $isWeekend ? 'bg-light' : '' }}" 
                                                style="{{ $isWeekend ? 'background-color: #f1f3f4 !important;' : '' }}">
                                                
                                                {{-- Shift Pagi --}}
                                                <div class="form-check form-check-inline mb-1">
                                                    <input type="checkbox" 
                                                           name="jadwal[{{ $pegawai->id }}][{{ $dateString }}][pagi]" 
                                                           value="1"
                                                           id="pagi_{{ $pegawai->id }}_{{ $dateString }}"
                                                           class="form-check-input shift-checkbox pagi-shift"
                                                           data-pegawai="{{ $pegawai->id }}"
                                                           data-date="{{ $dateString }}"
                                                           data-shift="pagi"
                                                           {{ $existingPagi ? 'checked' : '' }}
                                                           {{ $isWeekend ? 'disabled' : '' }}>
                                                    <label for="pagi_{{ $pegawai->id }}_{{ $dateString }}" 
                                                           class="form-check-label" 
                                                           title="Shift Pagi">
                                                        <i class="fas fa-sun text-warning"></i>
                                                    </label>
                                                </div>
                                                
                                                {{-- Shift Sore --}}
                                                <div class="form-check form-check-inline mb-1">
                                                    <input type="checkbox" 
                                                           name="jadwal[{{ $pegawai->id }}][{{ $dateString }}][sore]" 
                                                           value="1"
                                                           id="sore_{{ $pegawai->id }}_{{ $dateString }}"
                                                           class="form-check-input shift-checkbox sore-shift"
                                                           data-pegawai="{{ $pegawai->id }}"
                                                           data-date="{{ $dateString }}"
                                                           data-shift="sore"
                                                           {{ $existingSore ? 'checked' : '' }}
                                                           {{ $isWeekend ? 'disabled' : '' }}>
                                                    <label for="sore_{{ $pegawai->id }}_{{ $dateString }}" 
                                                           class="form-check-label" 
                                                           title="Shift Sore">
                                                        <i class="fas fa-moon text-primary"></i>
                                                    </label>
                                                </div>
                                                
                                                {{-- Show existing assignment --}}
                                                @if($existingPagi || $existingSore)
                                                    <div class="mt-1">
                                                        @if($existingPagi)
                                                            <span class="badge badge-warning badge-sm">P</span>
                                                        @endif
                                                        @if($existingSore)
                                                            <span class="badge badge-primary badge-sm">S</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="overwrite_existing" id="overwrite_existing" 
                                               class="form-check-input" value="1">
                                        <label for="overwrite_existing" class="form-check-label">
                                            Timpa jadwal yang sudah ada
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Jadwal
                                    </button>
                                    <a href="{{ route('jadwal-shift.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Legend --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-2">
                <div class="d-flex justify-content-center align-items-center">
                    <span class="mr-3">
                        <i class="fas fa-sun text-warning"></i> Shift Pagi
                    </span>
                    <span class="mr-3">
                        <i class="fas fa-moon text-primary"></i> Shift Sore
                    </span>
                    <span class="mr-3">
                        <span class="badge badge-warning">P</span> Sudah Ada Pagi
                    </span>
                    <span class="mr-3">
                        <span class="badge badge-primary">S</span> Sudah Ada Sore
                    </span>
                    <span class="text-muted">
                        <i class="fas fa-info-circle"></i> Weekend otomatis dinonaktifkan
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAllShift(shiftType) {
    const checkboxes = document.querySelectorAll(`.${shiftType}-shift:not(:disabled)`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function clearAll() {
    const checkboxes = document.querySelectorAll('.shift-checkbox:not(:disabled)');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Double click to select all shifts for a pegawai
document.addEventListener('DOMContentLoaded', function() {
    const pegawaiCells = document.querySelectorAll('td[style*="sticky"]');
    
    pegawaiCells.forEach(cell => {
        cell.addEventListener('dblclick', function() {
            const row = this.closest('tr');
            const checkboxes = row.querySelectorAll('.shift-checkbox:not(:disabled)');
            
            // Toggle all checkboxes in this row
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
        });
    });
});

// Form validation before submit
document.getElementById('matrixForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.shift-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Pilih minimal satu shift untuk satu pegawai!');
        return false;
    }
    
    if (!confirm(`Anda akan menyimpan ${checkedBoxes.length} jadwal shift. Lanjutkan?`)) {
        e.preventDefault();
        return false;
    }
});
</script>

<style>
.table-responsive {
    border: 1px solid #dee2e6;
}

.shift-checkbox {
    cursor: pointer;
}

.shift-checkbox:disabled {
    cursor: not-allowed;
}

td[style*="sticky"] {
    cursor: pointer;
}

td[style*="sticky"]:hover {
    background-color: #e9ecef !important;
}

.badge-sm {
    font-size: 0.7rem;
}

/* Custom scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection