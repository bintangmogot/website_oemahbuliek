@extends('layouts.app')

@section('title', 'Selamat Datang')

@section('content')

{{-- SECTION 1: Hero dengan offset navbar --}}
<section 
  class="d-flex align-items-start justify-content-center"
  style="background: linear-gradient(135deg, var(--primary-color) 20%, var(--secondary-color) 80%);">
  <div class=" px-4 py-5">
    <div class="p-2 row align-items-center">
      {{-- Teks & Tombol --}}
      <div class="col-lg-6 text-light text-lg-start text-justify">
        <h1 class="hero-title mb-3" style="font-family: 'Abhaya Libre', serif;">
          Sistem Manajemen Restoran
        </h1>
        <h2 class="mb-4" style="font-family: 'Josefin Sans', sans-serif;">
          Oemah Bu Liek Gwalk
        </h2>
        <p class="lead mb-4" style="text-align: justify;">
          Platform terintegrasi untuk inventaris, absensi, dan gaji. mudah, cepat, dan akurat!
        </p>
        <div class="d-flex justify-content-start gap-3 flex-wrap">
          <a href="{{ route('login') }}" 
             class="btn-theme primary btn-lg underline-hover ">
            Masuk
          </a>
          <a href="https://wa.me/62895353811311" target="_blank"
             class="btn-theme secondary outline-light btn-lg">
            <i class="bi bi-whatsapp me-2"></i>WhatsApp
          </a>
        </div>
      </div>

        {{-- Ilustrasi dengan aspect ratio terjaga --}}
        <div class=" p-2 col-lg-6 text-center mt-4 mt-lg-0">
          <img 
            src="{{ asset('img/soto kudus.jpg') }}" 
            alt="Ilustrasi Restoran" 
            class="img-fluid rounded-3 shadow-lg"
            style="max-height: 450px; width: auto;"/>
      </div>
    </div>
  </div>
</section>

{{-- SECTION 2: Tentang Sistem  --}}
<section class="d-flex section-alt mx-auto">
  <div class="container-md py-5 px-3">
    <h2 class="text-center mb-4" style="font-family: 'Abhaya Libre', serif;">Tentang Sistem</h2>
    <div class="row text-center gy-4">
      
      {{-- Logika memunculkan beberapa Card --}}
      @foreach ([
        ['icon'=>'bi-box-seam','title'=>'Manajemen Stok','desc'=>'Pantau ketersediaan bahan real-time dan notifikasi restock.'],
        ['icon'=>'bi-person-check','title'=>'Absensi Karyawan','desc'=>'Absensi cepat dengan tablet terdaftar, lengkap bukti foto.'],
        ['icon'=>'bi-currency-dollar','title'=>'Perhitungan Gaji','desc'=>'Hitung gaji otomatis berdasarkan jam kerja & jabatan.'],
        ['icon'=>'bi-bar-chart','title'=>'Laporan Real-Time','desc'=>'Akses laporan operasional kapan saja, di mana saja.'],
      ] as $item)
        <div class="col-6 col-md-3">
          <div class="card-theme p-4 h-100">
            <i class="bi {{ $item['icon'] }} mb-3" 
               style="font-size:2rem; color: var(--accent-color);"></i>
            <h5 class="mb-2">{{ $item['title'] }}</h5>
            <p style="text-align: justify; font-size:0.9rem;">{{ $item['desc'] }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endsection
