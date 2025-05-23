<nav class="navbar navbar-expand bg-light bg-white shadow-sm">
  <div class="container px-5">

    {{-- Logo Sekaligus Brand --}}
    <a href="/" class="navbar-brand">Oemah Bu Liek</a>
    <ul class="navbar-nav ms-auto">

      {{-- jika guest, tampilkan menu login saja --}}
      @guest
        <li><a class="nav-link" href="{{ route(name: 'login') }}">Login</a></li>
      @else
      {{-- kalau admin, tampilkan menu admin dan logout, begitu juga dengan pegawai --}}
        @if(auth()->user()->isAdmin)
          <li><a class="nav-link" href="/admin">Admin</a></li>
        @else
          <li><a class="nav-link" href="/pegawai">Pegawai</a></li>
        @endif
        <li>
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="btn btn-link nav-link">Logout</button>
          </form>
        </li>
      @endguest
    </ul>
  </div>
</nav>