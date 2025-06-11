{{-- css untuk navbar --}}
<link rel="stylesheet" href="/css/nav.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


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
    {{-- Logo Restoran --}}
    <div class="logo-section">
      <img src="/img/obl.png" alt="Oemah Bu Liek" class="logo-img">
      <h5 class="logo-text">Oemah Bu Liek</h5>
    </div>
  </a>
    
    {{-- Close Button --}}
    <button class="btn-close-sidebar" id="closeSidebar">
      <i class="fas fa-times"></i>
    </button>
  </div>

  
<div class="sidebar-content">
    {{-- Profile Section --}}
    
    @auth
    <div class="profile-section">
      {{-- Profile Avatar --}}
      <div class="profile-avatar">
        @if(auth()->user()->avatar)
          <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile" class="avatar-img">
        @else
          <div class="avatar-placeholder">
            <i class="fas fa-user"></i>
          </div>
        @endif
      </div>

@if(session('role') === 'admin')
  <a href="{{ route('admin.profile') }}" style="text-decoration: none; color: inherit;" >
    <div class="profile-info">
        <h6 class="profile-name">{{ session('nama_user') }}</h6>
        <span class="profile-role">
          {{ session('role') }}
        </span>
      </div>
  </a>
@else
    <a href="{{ route('pegawai.profile') }}" style="text-decoration: none; color: inherit;" >
      <div class="profile-info">
        <h6 class="profile-name">{{ session('nama_user') }}</h6>
        <span class="profile-role">
          {{ session('jabatan') }}
        </span>
      </div>
    </a>
  
    @endif
  </div>
    @endauth

    {{-- Menu Navigation --}}
    <div class="sidebar-menu">
        {{-- Menu untuk Admin --}}
        @if(session('role') === 'admin')
          <a href="/dashboard" class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </a>
          
          <a href="#" class="menu-item {{ request()->is('admin/pesan*') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span>Pesan</span>
            @if(isset($unreadMessages) && $unreadMessages > 0)
              <span class="badge">{{ $unreadMessages }}</span>
            @endif
          </a>
          
          <a href="#" class="menu-item {{ request()->is('admin/pegawai*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Presensi Pegawai</span>
          </a>
          
          <a href="#" class="menu-item {{ request()->is('admin/inventaris*') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            <span>Manajemen Inventaris</span>
          </a>

          <a href="{{ route('admin.user.index') }}" class="menu-item {{ request()->is('dashboard/user*') ? 'active' : '' }}" >
          <i class="fas fa-user-tie"></i>
            <span>Manajemen User</span>
          </a>
          
          <a href="{{ route('admin.pegawai.index') }}" class="menu-item {{ request()->is('dashboard/pegawai*') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i>
            <span>Data Pegawai</span>
          </a>
          
          <a href="#" class="menu-item {{ request()->is('admin/kasir*') ? 'active' : '' }}">
            <i class="fas fa-cash-register"></i>
            <span>Kasir</span>
          </a>

        {{-- Menu untuk Pegawai --}}
        @else
          <a href="/dashboard" class="menu-item {{ request()->is('pegawai') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </a>
          
          <a href="#" class="menu-item {{ request()->is('pegawai/presensi*') ? 'active' : '' }}">
            <i class="fas fa-clock"></i>
            <span>Presensi</span>
          </a>
          
          <a href="#" class="menu-item {{ request()->is('pegawai/inventaris*') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            <span>Inventaris</span>
          </a>
          
          <a href="#" class="menu-item {{ request()->is('pegawai/kasir*') ? 'active' : '' }}">
            <i class="fas fa-cash-register"></i>
            <span>Kasir</span>
          </a>
        @endif
        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
          @csrf
          <button type="submit" class="menu-item logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Log Out</span>
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
    <h4 class="page-title">@yield('page-title', 'Dashboard')</h4>
    
    {{-- Right Side Actions --}}
    <div class="navbar-actions">
      <div class="user-info">
        <span class="user-name">{{ auth()->user()->name }}</span>
      </div>
      
      {{-- Settings Button --}}
      <button class="settings-btn">
        <i class="fas fa-cog"></i>
      </button>
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