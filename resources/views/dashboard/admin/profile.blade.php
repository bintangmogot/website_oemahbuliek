@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="fw-bold text-center">Profil Saya</h1>
    <div class="border-0 shadow bg-white rounded-3" style="max-width: 800px; margin: auto;">
        <div class="card-body">
            <p class="border-bottom pb-2"><strong>Email:</strong> {{ $admin->email }}</p>
            <p class="border-bottom pb-2"><strong>Role:</strong> {{ ucfirst($admin->role) }}</p>
        </div>
    </div>
</div>
@endsection
