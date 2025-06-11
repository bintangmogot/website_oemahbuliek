
@extends('layouts.app')
@section('title','Dashboard Pegawai')
@section('content')
@if(session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

<h1>Halo {{ session('nama_user') }}</h1>
<h4>{{ Auth::user()->role }}</h4>
<p>Ini halaman dashboard khusus pegawai.</p>
@endsection
