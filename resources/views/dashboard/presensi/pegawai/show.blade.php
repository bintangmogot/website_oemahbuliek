@extends('layouts.app')

@section('title', 'Form Presensi')

@section('content')
<x-session-status/>
<!-- Container untuk alert messages -->
<div id="alertContainer"></div>

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
            <div>
                <h3 class="fw-bold mb-0">Form Presensi</h3>
                <p class="text-white mb-0">{{ Carbon\Carbon::parse($jadwalShift->tanggal)->format('d F Y') }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('pegawai.presensi.index') }}" class="btn btn-theme secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            {{-- Left Column: Jadwal Info --}}
            <div class="col-lg-6 mb-4">
                {{-- Jadwal Shift Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Informasi Jadwal</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <strong>Nama Shift:</strong>
                                <p>{{ $jadwalShift->shift->nama_shift }}</p>
                            </div>
                            <div class="col-6">
                            </div>
                            <div class="col-6">
                                <strong>Jam Mulai:</strong>
                                <p class="text-success">{{ Carbon\Carbon::parse($jadwalShift->shift->jam_mulai)->format('H:i') }}</p>
                            </div>
                            <div class="col-6">
                                <strong>Jam Selesai:</strong>
                                <p class="text-danger">{{ Carbon\Carbon::parse($jadwalShift->shift->jam_selesai)->format('H:i') }}</p>
                            </div>
                            <div class="col-6">
                                <strong>Toleransi Terlambat:</strong>
                                <p>{{ $jadwalShift->shift->toleransi_terlambat }} menit</p>
                            </div>
                            <div class="col-6">
                                <strong>Batas Lembur Min:</strong>
                                <p>{{ $jadwalShift->shift->batas_lembur_min }} menit</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Presensi Card --}}
                @php
                    $statusClass = match($presensi->status_approval ?? 0) {
                        0 => 'pending',
                        1 => 'approved', 
                        2 => 'rejected',
                        default => 'pending'
                    };
                @endphp
                <div class="card border-0 shadow-sm status-card {{ $statusClass }}">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Status Presensi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <strong>Status Kehadiran:</strong>
                                @php
                                    $kehadiranClass = match($presensi->status_kehadiran ?? 0) {
                                        0 => 'bg-danger',
                                        1 => 'bg-success',
                                        2 => 'bg-warning',
                                        3 => 'bg-info',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $kehadiranClass }} ms-2">
                                    {{ $presensi->status_kehadiran_label ?? 'Belum Presensi' }}
                                </span>
                            </div>
                            
                            @if($presensi->status_lembur > 0)
                            <div class="col-12 mb-3">
                                <strong>Status Lembur:</strong>
                                @php
                                    $lemburClass = match($presensi->status_lembur) {
                                        1 => 'bg-warning',
                                        2 => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $lemburClass }} ms-2">
                                    {{ $presensi->status_lembur_label }}
                                </span>
                            </div>
                            @endif
                            
                            <div class="col-12 mb-3">
                                <strong>Status Approval:</strong>
                                @php
                                    $approvalClass = match($presensi->status_approval ?? 0) {
                                        0 => 'bg-warning',
                                        1 => 'bg-success',
                                        2 => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $approvalClass }} ms-2">
                                    {{ $presensi->status_approval_label ?? 'Belum Ada' }}
                                </span>
                            </div>

                            @if($presensi->menit_terlambat > 0)
                            <div class="col-12 mb-3">
                                <strong>Keterlambatan:</strong>
                                <span class="text-danger">{{ $presensi->menit_terlambat }} menit</span>
                            </div>
                            @endif
                            @if($presensi->jam_keluar)
                                @php
                                    $overtime = $presensi->calculateOvertime();
                                @endphp
                                @if($overtime > 0)
                                    <span class="badge bg-info ml-2">
                                        Lembur {{ $overtime }} menit
                                    </span>
                                @endif
                            @endif

                            @if($presensi->catatan_admin)
                            <div class="col-12">
                                <strong>Catatan Admin:</strong>
                                <p class="text-muted">{{ $presensi->catatan_admin }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Presensi Actions --}}
            <div class="col-lg-6">
                {{-- Current Time --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <div class="time-display" id="currentTime">{{ Carbon\Carbon::now()->format('H:i:s') }}</div>
                        <p class="text-muted">{{ Carbon\Carbon::now()->format('d F Y') }}</p>
                    </div>
                </div>

                {{-- Presensi Data --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Data Presensi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-center">
                                <strong>Check In</strong>
                                <div class="mt-2">
                                    @if($presensi->jam_masuk)
                                        <div class="time-display text-success">
                                            {{ Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}
                                        </div>
                                        @if($presensi->foto_masuk)
                                            <img src="{{ Storage::url($presensi->foto_masuk) }}" 
                                                 class="img-thumbnail mt-2" 
                                                 style="width: 100px; height: 100px; object-fit: cover;"
                                                 alt="Foto Check In">
                                        @endif
                                    @else
                                        <div class="text-muted">Belum Check In</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <strong>Check Out</strong>
                                <div class="mt-2">
                                    @if($presensi->jam_keluar)
                                        <div class="time-display text-danger">
                                            {{ Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') }}
                                        </div>
                                        @if($presensi->foto_keluar)
                                            <img src="{{ Storage::url($presensi->foto_keluar) }}" 
                                                 class="img-thumbnail mt-2" 
                                                 style="width: 100px; height: 100px; object-fit: cover;"
                                                 alt="Foto Check Out">
                                        @endif
                                    @else
                                        <div class="text-muted">Belum Check Out</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                @if($isToday && !$isPastDate)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-camera"></i> Aksi Presensi</h5>
                        </div>
                        <div class="card-body">
                            @if(!$presensi->isCheckedIn())
                                {{-- Check In Button --}}
                                <button type="button" class="btn btn-success btn-lg w-100 mb-3" 
                                        id="checkInBtn" onclick="openCamera('checkin')">
                                    <i class="fas fa-sign-in-alt"></i> Check In
                                </button>
                            @elseif(!$presensi->isCheckedOut())
                                {{-- Check Out Button --}}
                                <button type="button" class="btn btn-danger btn-lg w-100 mb-3" 
                                        id="checkOutBtn" onclick="openCamera('checkout')">
                                    <i class="fas fa-sign-out-alt"></i> Check Out
                                </button>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-check-circle"></i>
                                    Presensi hari ini sudah lengkap
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($isPastDate)
                    <div class="alert alert-secondary text-center">
                        <i class="fas fa-calendar-times"></i>
                        Tanggal presensi sudah berlalu
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-clock"></i>
                        Belum waktunya presensi
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Camera Modal --}}
<div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalTitle">Ambil Foto Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="camera-container">
                    <video id="camera" autoplay playsinline class="hidden"></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    <img id="preview" class="hidden" alt="Preview">
                    <button type="button" class="btn btn-secondary btn-lg capture-btn" id="captureBtn">
                        <i class="fas fa-camera"></i> Ambil Foto
                    </button>
                </div>
                
                <div class="mt-3 hidden" id="photoActions">
                    <button type="button" class="btn btn-secondary me-2" onclick="retakePhoto()">
                        <i class="fas fa-redo"></i> Ulangi
                    </button>
                    <button type="button" class="btn btn-success" id="submitPhoto">
                        <i class="fas fa-check"></i> Gunakan Foto
                    </button>
                </div>
                
                <div id="loading" class="hidden">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Sedang memproses...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let stream = null;
let currentAction = '';
let capturedImage = null;

// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    document.getElementById('currentTime').textContent = timeString;
}

// Fungsi untuk mengecek dan menampilkan peringatan sebelum submit
function checkAndShowWarnings(action, callback) {
    const now = new Date();
    const currentTime = now.toTimeString().slice(0, 5); // HH:MM format
    
    // Data jadwal dari server (perlu dipass dari backend)
    const jamMulai = '{{ Carbon\Carbon::parse($jadwalShift->shift->jam_mulai)->format("H:i") }}';
    const jamSelesai = '{{ Carbon\Carbon::parse($jadwalShift->shift->jam_selesai)->format("H:i") }}';
    const toleransiTerlambat = {{ $jadwalShift->shift->toleransi_terlambat }};
    const batasLemburMin = {{ $jadwalShift->shift->batas_lembur_min }};
    
    let warnings = [];
    
    if (action === 'checkin') {
        // Cek keterlambatan
        const [jamMulaiHour, jamMulaiMin] = jamMulai.split(':').map(Number);
        const [currentHour, currentMin] = currentTime.split(':').map(Number);
        
        const jadwalMulaiMinutes = jamMulaiHour * 60 + jamMulaiMin;
        const currentMinutes = currentHour * 60 + currentMin;
        const terlambatMinutes = currentMinutes - jadwalMulaiMinutes;
        const totalTerlambatMinutes = terlambatMinutes - toleransiTerlambat
        
        if (terlambatMinutes > toleransiTerlambat) {
            warnings.push({
                type: 'late',
                title: '⚠️ Peringatan: Anda Akan Terlambat!',
                message: `Anda <strong>terlambat ${totalTerlambatMinutes} menit</strong> dari jadwal masuk (${jamMulai}) [sudah dipotong toleransi ${toleransiTerlambat} menit]. Keterlambatan akan dicatat dalam sistem dan mempengaruhi perhitungan kehadiran Anda.`,
                severity: 'danger'
            });
        }
        
    } else if (action === 'checkout') {
        // Cek pulang lebih awal
        const [jamSelesaiHour, jamSelesaiMin] = jamSelesai.split(':').map(Number);
        const [currentHour, currentMin] = currentTime.split(':').map(Number);
        
        const jadwalSelesaiMinutes = jamSelesaiHour * 60 + jamSelesaiMin;
        const currentMinutes = currentHour * 60 + currentMin;
        const selisihMinutes = jadwalSelesaiMinutes - currentMinutes;
        
        if (selisihMinutes > 0) {
            warnings.push({
                type: 'early_checkout',
                title: '⚠️ Peringatan: Anda Akan Pulang Lebih Awal!',
                message: `Anda akan <strong>pulang ${selisihMinutes} menit lebih awal</strong> dari jadwal selesai (${jamSelesai}). Ini akan dicatat sebagai pulang awal dan dapat mempengaruhi perhitungan gaji.`,
                severity: 'warning'
            });
        }
        
        // Cek potensi lembur
        const lemburMinutes = currentMinutes - jadwalSelesaiMinutes;
        const totalLemburMinutes = lemburMinutes - batasLemburMin;
        if (lemburMinutes >= batasLemburMin) {
            warnings.push({
                type: 'overtime',
                title: '📋 Informasi: Lembur Terdeteksi',
                message: `Anda akan <strong>lembur</strong> selama <strong>${totalLemburMinutes} menit</strong>. [sudah dikurangi batas minimal lembur ${batasLemburMin} menit]. 
                Lembur akan dicatat dan perlu persetujuan admin untuk perhitungan upah lembur.`,
                severity: 'info'
            });
        } else if (lemburMinutes > 0 && lemburMinutes < batasLemburMin) {
            warnings.push({
                type: 'overtime_minimal',
                title: '📋 Informasi: Waktu Tambahan Minimal',
                message: `Anda <strong>bekerja ${lemburMinutes} menit lebih lama</strong>, namun belum mencapai batas minimal lembur (${batasLemburMin} menit). Waktu ini tidak dihitung sebagai lembur.`,
                severity: 'secondary'
            });
        }
    }
    
    // Jika ada peringatan, tampilkan modal konfirmasi
    if (warnings.length > 0) {
        showConfirmationModal(warnings, action, callback);
    } else {
        // Langsung lanjutkan jika tidak ada peringatan
        callback();
    }
}

// Modal konfirmasi dengan peringatan
function showConfirmationModal(warnings, action, callback) {
    const actionText = action === 'checkin' ? 'Check In' : 'Check Out';
    const actionIcon = action === 'checkin' ? 'fas fa-sign-in-alt' : 'fas fa-sign-out-alt';
    
    let warningsHtml = '';
    warnings.forEach(warning => {
        let alertClass = warning.severity;
        let iconClass = 'fas fa-exclamation-triangle';
        
        if (warning.type === 'late') {
            iconClass = 'fas fa-clock text-danger';
        } else if (warning.type === 'early_checkout') {
            iconClass = 'fas fa-sign-out-alt text-warning';
        } else if (warning.type === 'overtime') {
            iconClass = 'fas fa-business-time text-info';
        } else if (warning.type === 'overtime_minimal') {
            iconClass = 'fas fa-info-circle text-secondary';
        }
        
        warningsHtml += `
            <div class="alert alert-${alertClass} d-flex align-items-start mb-3">
                <i class="${iconClass} me-3 mt-1" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-2">${warning.title}</h6>
                    <p class="mb-0">${warning.message}</p>
                </div>
            </div>
        `;
    });
    
    // Tambahan informasi penting
    const importantNote = `
        <div class="alert alert-light border mt-3">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                <strong>Catatan Penting:</strong><br>
                • Semua aktivitas presensi akan dicatat dalam sistem<br>
                • Data presensi akan ditinjau oleh admin<br>
                • Keterlambatan dan pulang awal dapat mempengaruhi perhitungan gaji<br>
                • Lembur hanya dihitung jika disetujui oleh admin
            </small>
        </div>
    `;
    
    const modal = `
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="confirmationModalLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi ${actionText}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <h6 class="text-muted">Terdapat hal-hal yang perlu diperhatikan untuk presensi Anda:</h6>
                        </div>
                        ${warningsHtml}
                        ${importantNote}
                        <div class="mt-4 p-3 bg-light rounded">
                            <p class="mb-2"><strong>Apakah Anda yakin ingin melanjutkan ${actionText}?</strong></p>
                            <small class="text-muted">Dengan melanjutkan, Anda menyetujui bahwa data presensi ini akan dicatat sesuai dengan kondisi di atas.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-theme primary p-2 px-3 px-md-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batalkan
                        </button>
                        <button type="button" class="btn btn-theme info p-2 px-3 px-md-4" id="confirmSubmit">
                            <i class="${actionIcon} me-1"></i>Ya, Lanjutkan ${actionText}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Hapus modal lama jika ada
    const existingModal = document.getElementById('confirmationModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Tambahkan modal baru
    document.body.insertAdjacentHTML('beforeend', modal);
    
    // Setup event listeners
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'), {
        backdrop: 'static',
        keyboard: false
    });
    
    document.getElementById('confirmSubmit').addEventListener('click', function() {
        confirmationModal.hide();
        // Lanjutkan proses presensi setelah modal ditutup
        setTimeout(() => {
            callback();
        }, 300);
    });
    
    confirmationModal.show();
    
    // Focus ke tombol konfirmasi
    document.getElementById('confirmationModal').addEventListener('shown.bs.modal', function () {
        document.getElementById('confirmSubmit').focus();
    });
}


// Update time every second
setInterval(updateTime, 1000);

// Validasi waktu sebelum buka kamera
function validateTimeBeforeCamera(action, callback) {
    const now = new Date();
    const currentTime = now.getHours() * 60 + now.getMinutes(); // dalam menit
    
    // Data jadwal dari server
    const jamMulai = '{{ Carbon\Carbon::parse($jadwalShift->shift->jam_mulai)->format("H:i") }}';
    const jamSelesai = '{{ Carbon\Carbon::parse($jadwalShift->shift->jam_selesai)->format("H:i") }}';
    
    const [jamMulaiHour, jamMulaiMin] = jamMulai.split(':').map(Number);
    const [jamSelesaiHour, jamSelesaiMin] = jamSelesai.split(':').map(Number);
    
    const jadwalMulaiMinutes = jamMulaiHour * 60 + jamMulaiMin;
    const jadwalSelesaiMinutes = jamSelesaiHour * 60 + jamSelesaiMin;
    
    if (action === 'checkin') {
        // Check in: minimal 1 jam sebelum jam kerja
        const batasCheckinMinutes = jadwalMulaiMinutes - 60; // 1 jam sebelum
        
        if (currentTime < batasCheckinMinutes) {
            const jamBatas = Math.floor(batasCheckinMinutes / 60).toString().padStart(2, '0') + ':' + 
                           (batasCheckinMinutes % 60).toString().padStart(2, '0');
            
            showBlockingMessage(
                'Belum Waktunya Check In',
                `Check in hanya bisa dilakukan mulai pukul ${jamBatas} (1 jam sebelum jam kerja dimulai).`,
                'warning'
            );
            return;
        }
    } else if (action === 'checkout') {
        // Check out: tidak boleh sebelum jam kerja dimulai
        if (currentTime < jadwalMulaiMinutes) {
            showBlockingMessage(
                'Tidak Bisa Check Out',
                `Check out tidak bisa dilakukan sebelum jam kerja dimulai (${jamMulai}).`,
                'danger'
            );
            return;
        }
    }
    
    // Jika validasi lolos, lanjutkan
    callback();
}

// Modal untuk blocking message
function showBlockingMessage(title, message, type = 'warning') {
    const iconClass = type === 'danger' ? 'fas fa-times-circle' : 'fas fa-clock';
    const modalClass = type === 'danger' ? 'danger' : 'warning';
    
    const modal = `
        <div class="modal fade" id="blockingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-${modalClass} text-white">
                        <h5 class="modal-title">
                            <i class="${iconClass} me-2"></i>${title}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mb-3">${message}</p>
                        <div class="alert alert-light">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Waktu saat ini: <strong>${new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</strong>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-check me-1"></i>Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal
    const existingModal = document.getElementById('blockingModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add and show modal
    document.body.insertAdjacentHTML('beforeend', modal);
    const blockingModal = new bootstrap.Modal(document.getElementById('blockingModal'));
    blockingModal.show();
}

// fungsi openCamera untuk gunakan validasi baru
async function openCamera(action) {
    // 1. Validasi waktu dulu (apakah sudah boleh presensi)
    validateTimeBeforeCamera(action, function() {
        // 2. Setelah validasi waktu lolos, cek peringatan terlambat/lembur/pulang awal
        checkAndShowWarnings(action, async function() {
            // 3. Setelah user konfirmasi peringatan, baru buka kamera
            currentAction = action;
            const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
            const title = action === 'checkin' ? 'Check In - Ambil Foto' : 'Check Out - Ambil Foto';
            document.getElementById('cameraModalTitle').textContent = title;
            
            modal.show();
            
            try {
                // Reset elements
                resetCamera();
                
                // Get camera stream
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    } 
                });
                
                const video = document.getElementById('camera');
                video.srcObject = stream;
                video.classList.remove('hidden');
                
            } catch (error) {
                console.error('Error accessing camera:', error);
                alert('Tidak dapat mengakses kamera. Pastikan browser memiliki izin kamera.');
            }
        });
    });
}

// Reset camera elements
function resetCamera() {
    document.getElementById('camera').classList.add('hidden');
    document.getElementById('preview').classList.add('hidden');
    document.getElementById('photoActions').classList.add('hidden');
    document.getElementById('captureBtn').classList.remove('hidden');
    document.getElementById('loading').classList.add('hidden');
    capturedImage = null;
}

// Capture photo
document.getElementById('captureBtn').addEventListener('click', function() {
    const video = document.getElementById('camera');
    const canvas = document.getElementById('canvas');
    const preview = document.getElementById('preview');
    const context = canvas.getContext('2d');
    
    // Set canvas size
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    context.drawImage(video, 0, 0);
    
    // Convert to blob
    canvas.toBlob(function(blob) {
        capturedImage = blob;
        
        // Show preview
        const url = URL.createObjectURL(blob);
        preview.src = url;
        preview.classList.remove('hidden');
        
        // Hide camera and capture button
        video.classList.add('hidden');
        document.getElementById('captureBtn').classList.add('hidden');
        document.getElementById('photoActions').classList.remove('hidden');
    }, 'image/jpeg', 0.8);
});

// Retake photo
function retakePhoto() {
    resetCamera();
    document.getElementById('camera').classList.remove('hidden');
    if (capturedImage) {
        URL.revokeObjectURL(document.getElementById('preview').src);
        capturedImage = null;
    }
}

// Submit photo
document.getElementById('submitPhoto').addEventListener('click', function() {
    if (!capturedImage) {
        alert('Belum ada foto yang diambil');
        return;
    }
    
    // Show loading
    document.getElementById('photoActions').classList.add('hidden');
    document.getElementById('loading').classList.remove('hidden');
    
    // Create form data
    const formData = new FormData();
    formData.append('foto', capturedImage, 'presensi.jpg');
    formData.append('presensi_id', '{{ $presensi->id }}');
    formData.append('_token', '{{ csrf_token() }}');
    
    // Determine endpoint
    const endpoint = currentAction === 'checkin' 
        ? '{{ route("pegawai.presensi.checkin") }}'
        : '{{ route("pegawai.presensi.checkout") }}';
    
    // Submit via fetch
    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('cameraModal')).hide();
            
            // Show success message
            showSuccessMessage(data.message || 'Presensi berhasil dicatat!');
            
            // Reload halaman setelah 2 detik
            setTimeout(() => {
                window.location.reload();
            }, 2000);
            
        } else {
            throw new Error(data.error || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('photoActions').classList.remove('hidden');
    });
});

// Clean up when modal is closed
document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    
    if (capturedImage) {
        URL.revokeObjectURL(document.getElementById('preview').src);
        capturedImage = null;
    }
    
    resetCamera();
});

// Handle browser compatibility
if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    alert('Browser Anda tidak mendukung akses kamera');
}

// MODAL MESSAGES
    
    // Tambahkan informasi tambahan berdasarkan jenis peringatan
    let additionalInfo = '';
    const hasLateWarning = warnings.some(w => w.type === 'late');
    const hasEarlyCheckout = warnings.some(w => w.type === 'early_checkout');
    const hasOvertime = warnings.some(w => w.type === 'overtime');
    
    if (hasLateWarning || hasEarlyCheckout || hasOvertime) {
        additionalInfo = `
            <div class="alert alert-light border">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Catatan Penting:</strong><br>
                    • Presensi Anda telah dicatat dan akan ditinjau oleh admin<br>
                    • Anda akan mendapat notifikasi hasil review<br>
                    • ${hasOvertime ? 'Lembur hanya dihitung jika disetujui admin<br>' : ''}
                    • ${hasLateWarning || hasEarlyCheckout ? 'Keterlambatan/pulang awal dapat mempengaruhi perhitungan gaji' : ''}
                </small>
            </div>
        `;
    }
    
    // Buat modal
    const modal = `
        <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-${modalClass} text-white">
                        <h5 class="modal-title" id="warningModalLabel">
                            <i class="${iconClass} me-2"></i>${modalTitle}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${modalHtml}
                        ${additionalInfo}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            <i class="fas fa-check me-1"></i>Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Hapus modal lama jika ada
    const existingModal = document.getElementById('warningModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Tambahkan modal baru
    document.body.insertAdjacentHTML('beforeend', modal);
    
    // Tampilkan modal
    const warningModal = new bootstrap.Modal(document.getElementById('warningModal'), {
        backdrop: 'static', // Tidak bisa ditutup dengan klik backdrop
        keyboard: true      // Masih bisa ditutup dengan ESC
    });
    warningModal.show();
    
    // Auto-focus ke tombol "Mengerti" setelah modal terbuka
    document.getElementById('warningModal').addEventListener('shown.bs.modal', function () {
        this.querySelector('.btn-primary').focus();
    });



// Fungsi untuk menampilkan pesan error
function showErrorMessage(message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.innerHTML = alertHtml;
    }
    
    // Auto hide after 30 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert-danger');
        if (alert) {
            alert.remove();
        }
    }, 30000);
}

function showSuccessMessage(message) {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.innerHTML = alertHtml;
    }
    
    // Auto hide after 30 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            alert.remove();
        }
    }, 30000);
}
</script>
@endpush