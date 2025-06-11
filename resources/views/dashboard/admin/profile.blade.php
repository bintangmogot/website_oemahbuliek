@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Profil Saya</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Email: </strong>{{ $admin->email }}</p>
            <p><strong>role: </strong> {{ $admin->role }}</p>

        </div>
    </div>
</div>
@endsection
