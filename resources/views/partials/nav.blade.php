<nav class="navbar navbar-expand bg-light">
  <div class="container">
    <a href="/" class="navbar-brand">Restoran</a>
    <ul class="navbar-nav ms-auto">
      @guest
        <li><a class="nav-link" href="{{ route('login') }}">Login</a></li>
      @else
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