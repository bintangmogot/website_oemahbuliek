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
    {{-- Select Advanced --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">

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
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
  @auth
<script>
    // Pastikan Echo sudah diinisialisasi di bootstrap.js
    window.Echo.private('App.Models.User.{{ auth()->id() }}')
        .notification((notification) => {
            console.log(notification); // Untuk debugging

            // 1. Tampilkan Toast Notification
            // Anda bisa menggunakan library toast seperti Toastr.js atau membuat elemen Bootstrap Toast sendiri
            const toastContainer = document.getElementById('toast-container'); // Pastikan ada elemen ini di layout Anda
            const toastHTML = `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                  <div class="toast-header bg-warning">
                    <strong class="me-auto">${notification.title}</strong>
                    <small>Baru saja</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                  </div>
                  <div class="toast-body">
                    ${notification.body}
                    <div class="mt-2 pt-2 border-top">
                        <a href="${notification.url}" class="btn btn-primary btn-sm">Lihat Detail</a>
                    </div>
                  </div>
                </div>
            `;
            toastContainer.innerHTML += toastHTML;
            const newToast = toastContainer.lastElementChild;
            new bootstrap.Toast(newToast).show();


            // 2. Update angka notifikasi di navbar
            const notifCountElement = document.getElementById('notification-count'); // Pastikan ada elemen ini di navbar
            if(notifCountElement) {
                let currentCount = parseInt(notifCountElement.innerText) || 0;
                notifCountElement.innerText = currentCount + 1;
                notifCountElement.style.display = 'inline-block';
            }
        });
</script>
@endauth
@stack('scripts')
</body>

</html>