@extends('layouts.app')
@section('title','Dashboard Admin')
@section('content')
<x-session-status/>
  

<h1>Halo {{ auth()->user()->nama_lengkap }}</h1>
<p>{{ Auth::user()->role }}</p>
<p>Ini halaman dashboard khusus admin.</p>
@endsection
