@extends('layouts.app')
@section('title','Dashboard Admin')
@section('content')
@if(session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif
<h1>Halo {{ session('nama_user') }}</h1>
<p>{{ Auth::user()->role }}</p>
<p>Ini halaman dashboard khusus admin.</p>
@endsection
