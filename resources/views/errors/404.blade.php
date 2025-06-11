{{-- resources/views/errors/404.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Halaman Tidak Ditemukan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      background: #f8f0f0;
      color: #8b0000;
      font-family: 'Segoe UI', sans-serif;
      text-align: center;
      padding: 50px;
    }
    .logo {
      margin-bottom: 30px;
    }
    .logo img {
      max-width: 150px;
    }
    h1 {
      font-size: 48px;
      margin-bottom: 10px;
    }
    p {
      font-size: 18px;
      margin-bottom: 30px;
    }
    a.btn {
      display: inline-block;
      padding: 10px 20px;
      background: #8b0000;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      transition: background .3s;
    }
    a.btn:hover {
      background: #a30000;
    }
  </style>
</head>
<body>
  <div class="logo">
    {{-- Ganti path-nya sesuai lokasi logo-mu --}}
    <img src="{{ asset('img/obl.png') }}" alt="Logo Restoran Oemah Bu Liek">
  </div>

  <h1>404</h1>
  <p>Maaf, halaman yang Anda cari tidak ditemukan.</p>
  <a href="{{ url('/') }}" class="btn">Kembali ke Beranda</a>
</body>
</html>
