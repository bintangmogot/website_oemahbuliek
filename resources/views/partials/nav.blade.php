{{-- css untuk navbar --}}
<link rel="stylesheet" href="/css/nav.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

@php
    use Illuminate\Support\Facades\Storage;
    $user = auth()->user();
@endphp

{{-- Navbar untuk Guest --}}
@guest
<nav class="navbar navbar-expand bg-light bg-white shadow-sm">
    <div class="container px-5">
        <a href="/" class="navbar-brand">Oemah Bu Liek</a>
        <ul class="navbar-nav ms-auto">
            <li><a class="nav-link" href="{{ route('login') }}">Login</a></li>
        </ul>
    </div>
</nav>
@endguest

{{-- Sidebar Navigation - Only show for authenticated users --}}
@auth
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/dashboard" style="text-decoration: none; color: inherit;">
            <div class="logo-section">
                <img src="/img/obl.png" alt="Oemah Bu Liek" class="logo-img">
                <h5 class="logo-text">Oemah Bu Liek</h5>
            </div>
        </a>
        <button class="btn-close-sidebar" id="closeSidebar"><i class="fas fa-times"></i></button>
    </div>

    <div class="sidebar-content">
        {{-- Profile Section --}}
        <div class="profile-section">
            <div class="profile-avatar">
                @if($user->foto_profil && Storage::disk('public')->exists($user->foto_profil))
                    <img src="{{ asset('storage/' . $user->foto_profil) }}" size="50" alt="Profile" class="avatar-img" />
                @else
                    <div class="avatar-placeholder"><i class="fas fa-user"></i></div>
                @endif
            </div>
            <a href="{{ route('profile.me') }}">
                <h6 class="profile-name">{{ session('nama_user') }}</h6>
                <span class="profile-role">{{ Auth::user()->role === 'admin' ? 'Admin' : Auth::user()->jabatan }}</span>
            </a>
        </div>

        {{-- Menu Navigation --}}
        <div class="sidebar-menu">
            {{-- ================= Menu untuk Admin ================= --}}
            @if(session('role') === 'admin')
                <h6 class="menu-header">Umum</h6>
                <a href="/dashboard" class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('notifications.index') }}" class="menu-item {{ request()->is('dashboard/messages*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>
                    <span>Pesan</span>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="badge bg-danger text-white" id="notification-count">{{ $unreadNotificationsCount }}</span>
                    @else
                        <span class="badge bg-danger text-white" id="notification-count" style="display: none;">0</span>
                    @endif
                </a>

                {{-- Grup Laporan --}}
                <h6 class="menu-header">Laporan</h6>
                <div class="divider"></div>  
                <span class="menu-header text-white">Inventaris</span>
                <div class="divider"></div>  
                <a href="{{ route('laporan.kerugian') }}" class="menu-item {{ request()->is('dashboard/laporan/kerugian-bahan-baku*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i><span>Kerugian Bahan Rusak</span>
                </a>
                <a href="{{ route('laporan.penggunaan') }}" class="menu-item {{ request()->is('dashboard/laporan/penggunaan-bahan*') ? 'active' : '' }}">
                    <i class="fas fa-rocket"></i><span>Bahan Paling Dibutuhkan</span>
                </a>
                <a href="{{ route('laporan.stok-mati') }}" class="menu-item {{ request()->is('dashboard/laporan/stok-mati*') ? 'active' : '' }}">
                    <i class="fas fa-bed"></i><span>Stok Belum Terpakai</span>
                </a>
                <a href="{{ route('laporan.penerimaan') }}" class="menu-item {{ request()->is('dashboard/laporan/penerimaan-bahan*') ? 'active' : '' }}">
                    <i class="fas fa-truck-loading"></i><span>Stok Masuk Terbanyak</span>
                </a>

                <div class="divider"></div>  
                <span class="menu-header text-white">Gaji</span>
                <div class="divider"></div>  

                <a href="{{ route('gaji-lembur.laporan') }}" class="menu-item {{ request()->is('dashboard/gaji-lembur/laporan*') ? 'active' : '' }}">
                    <i class="fas fa-truck-loading"></i><span>Gaji Lembur/Pegawai</span>
                </a>
                <a href="{{ route('admin.gaji-pokok.generated') }}" class="menu-item {{ request()->is('dashboard/gaji-pokok/generated*') ? 'active' : '' }}">
                    <i class="fas fa-truck-loading"></i><span>Gaji Pokok/Pegawai</span>
                </a>
                <a href="{{ route('admin.gaji-pokok.summary') }}" class="menu-item {{ request()->is('dashboard/gaji-pokok/summary*') ? 'active' : '' }}">
                    <i class="fas fa-truck-loading"></i><span>Pembayaran Gaji Pokok</span>
                </a>

                <h6 class="menu-header">Manajemen SDM</h6>
                <a href="{{ route('jadwal-shift.index') }}" class="menu-item {{ request()->is('dashboard/jadwal-shift*') ? 'active' : '' }}" >
                    <i class="fas fa-calendar-alt"></i><span>Jadwal Pegawai</span>
                </a>
                <a href="{{ route('admin.presensi.index') }}" class="menu-item {{ request()->is('dashboard/presensi*') ? 'active' : '' }}" >
                    <i class="fas fa-user-check"></i><span>Presensi Pegawai</span>
                </a>
                <a href="{{ route('gaji-lembur.laporan') }}" class="menu-item {{ request()->is('dashboard/gaji-lembur*') ? 'active' : '' }}" >
                    <i class="bi bi-person-workspace"></i><span>Lembur Pegawai</span>
                </a>
                <a href="{{ route('admin.gaji-pokok.index') }}" class="menu-item {{ request()->is('dashboard/gaji-pokok*') ? 'active' : '' }}" >
                    <i class="fas fa-wallet"></i><span>Gaji Pegawai</span>
                </a>

                <h6 class="menu-header">Manajemen Inventaris</h6>
                <a href="{{ route('bahan-baku.index') }}" class="menu-item {{ request()->is('dashboard/bahan-baku*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i><span>Bahan Baku</span>
                </a>
                <a href="{{ route('riwayat-stok.index') }}" class="menu-item {{ request()->is('dashboard/riwayat-stok*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i><span>Riwayat Stok</span>
                </a>

                <h6 class="menu-header">Pengaturan</h6>
                <a href="{{ route('user.index') }}" class="menu-item {{ request()->is('dashboard/user*') ? 'active' : '' }}" >
                    <i class="fas fa-users-cog"></i><span>Manajemen User</span>
                </a>
                <a href="{{ route('shift.index') }}" class="menu-item {{ request()->is('dashboard/shift*') ? 'active' : '' }}" >
                    <i class="fas fa-clock"></i><span>Shift</span>
                </a>
                <a href="{{ route('pengaturan_gaji.index') }}" class="menu-item {{ request()->is('dashboard/pengaturan-gaji*') ? 'active' : '' }}" >
                    <i class="fas fa-cogs"></i><span>Pengaturan Gaji</span>
                </a>

            {{-- ================= Menu untuk Pegawai ================= --}}
            @else
                <h6 class="menu-header">Umum</h6>
                <a href="/dashboard" class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('notifications.index') }}" class="menu-item {{ request()->is('dashboard/messages*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>
                    <span>Pesan</span>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="badge bg-danger text-white" id="notification-count">{{ $unreadNotificationsCount }}</span>
                    @else
                        <span class="badge bg-danger text-white" id="notification-count" style="display: none;">0</span>
                    @endif
                </a>

                <h6 class="menu-header">Laporan Saya</h6>
                <a href="{{ route('gaji-lembur.pegawai.index') }}" class="menu-item {{ request()->is('dashboard/gaji-lembur*') ? 'active' : '' }}" >
                    <i class="bi bi-person-workspace"></i><span>Laporan Lembur</span>
                </a>
                <a href="{{ route('pegawai.gaji-pokok.index') }}" class="menu-item {{ request()->is('dashboard/gaji-pokok*') ? 'active' : '' }}" >
                    <i class="fas fa-wallet"></i><span>Laporan Gaji Pokok</span>
                </a>
                <a href="{{ route('pegawai.presensi.index') }}" class="menu-item {{ request()->is('dashboard/presensi*') ? 'active' : '' }}">
                    <i class="fas fa-user-check"></i><span>Presensi</span>
                </a>
                <a href="{{ route('jadwal-shift.index') }}" class="menu-item {{ request()->is('dashboard/jadwal-shift*') ? 'active' : '' }}" >
                    <i class="fas fa-calendar-alt"></i><span>Jadwal Shift</span>
                </a>
                
                <h6 class="menu-header">Operasional</h6>
                <a href="{{ route('bahan-baku.index') }}" class="menu-item {{ request()->is('dashboard/bahan-baku*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i><span>Bahan Baku</span>
                </a>
                <a href="{{ route('riwayat-stok.index') }}" class="menu-item {{ request()->is('dashboard/riwayat-stok*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i><span>Riwayat Stok</span>
                </a>
            @endif
            
            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="menu-item logout-btn" style="background-color: var(--secondary-color);">
                    <i class="fas fa-sign-out-alt"></i><span>Log Out</span>
                </button>
            </form>
        </div>
    </div>
</nav>
@endauth

{{-- Main Navigation Bar - Only show for authenticated users --}}
@auth
<nav class="main-navbar">
    <div class="navbar-content">
        {{-- Hamburger Menu Button --}}
        <button class="hamburger-btn" id="hamburgerBtn">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        {{-- Page Title --}}
        <h4 class="page-title">@yield('title', 'Dashboard')</h4>
        
        {{-- Right Side Actions --}}
        <div class="navbar-actions">
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->nama_lengkap }}</span>
            </div>
        
        </div>
    </div>
</nav>
@endauth

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const closeSidebar = document.getElementById('closeSidebar');
        const body = document.body;

        // Toggle sidebar
        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            hamburgerBtn.classList.toggle('active');
            body.classList.toggle('sidebar-active');
        }

        // Close sidebar
        function closeSidebarFunc() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            hamburgerBtn.classList.remove('active');
            body.classList.remove('sidebar-active');
        }

        // Event listeners
        hamburgerBtn.addEventListener('click', toggleSidebar);
        closeSidebar.addEventListener('click', closeSidebarFunc);
        sidebarOverlay.addEventListener('click', closeSidebarFunc);

        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebarFunc();
            }
        });

        // Close sidebar when clicking on menu items (mobile)
        const menuItems = document.querySelectorAll('.menu-item:not(.logout-btn)');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeSidebarFunc();
                }
            });
        });
    });
</script>
