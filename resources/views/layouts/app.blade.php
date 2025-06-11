<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom Theme CSS -->
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
  {{-- icon bootstrap  --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')

<title>@yield('title') | Oemah Bu Liek</title>

</head>
<body class="d-flex flex-column min-vh-100">
  @include('partials.nav')
  <main class="flex-fill">@yield('content')</main>

  {{-- Footer (opsional) --}}
  <footer class="bg-light text-center py-3 mt-auto">
    &copy; {{ date('Y') }} {{ config('app.name') }}
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>

</html>