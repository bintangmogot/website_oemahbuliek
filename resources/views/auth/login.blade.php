{{-- template keseluruhan --}}
@extends('layouts.app')

{{-- untuk menampilkan title saja di tab --}}
@section('title', 'Login')


@section('content')

{{-- css untuk halaman login --}}
<link rel="stylesheet" href="/css/login.css">

@if(session('warning'))
  <div class="alert alert-warning text-center">
    {{ session('warning') }}
  </div>
@endif

{{-- container form --}}
<div class="row justify-content-center mt-3 px-3">
    <div class="col-md-5 login-container">
        <div class="text-center mb-2">
            <img src="/img/obl.png" alt="Logo Oemah Bu Liek" class="logo">
        </div>
        <h2 class="mb-1 text-center">Login</h2>
        <h5 class="mb-4 text-center">Oemah Bu Liek App</h5>

        {{-- jika ada error saat login, tampilkan alert --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Gagal login!</strong><br>
            </div>
        @endif

        {{-- membungkus dari email, password, remember, sampai button. jika berhasil maka lakukan login (ada kaitannya dengan LoginController)  --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Konten Login --}}
            {{-- email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- kata sandi --}}
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- checkbox ingat saya --}}
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Ingat Saya</label>
            </div>

            {{-- button login --}}
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>
</div>
@endsection
