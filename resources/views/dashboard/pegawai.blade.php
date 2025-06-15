@extends('layouts.app')
@section('title','Dashboard Pegawai')
@section('content')
<x-session-status/>

<div class="container py-3">
  <h1 class="mb-3">Halo, {{ session('nama_user') }}</h1>
  <h4 class="text-muted mb-4">Role: {{ Auth::user()->role }}</h4>
  <p>Ini halaman dashboard khusus pegawai.</p>
</div>
@endsection
