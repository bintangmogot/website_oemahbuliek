@extends('layouts.app')
@section('title', 'Pesan & Notifikasi')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">📬 Semua Notifikasi</h3>
        </div>

        <div class="list-group">
            @forelse($notifications as $notification)
                {{-- Tautan sekarang dinamis dari data notifikasi --}}
                <a href="{{ $notification->data['url'] ?? '#' }}" class="list-group-item list-group-item-action {{ !$notification->read_at ? 'list-group-item-info' : '' }}" aria-current="true">
                    <div class="d-flex w-100 justify-content-between">
                        {{-- Judul dan Ikon sekarang dinamis --}}
                        <h5 class="mb-1 fw-bold">
                            <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} me-2"></i>
                            {{ $notification->data['title'] ?? 'Notifikasi Baru' }}
                        </h5>
                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    {{-- PERBAIKAN: Ganti 'message' menjadi 'body' --}}
                    <p class="mb-1">{{ $notification->data['body'] ?? 'Anda memiliki notifikasi baru.' }}</p>
                    <small class="text-muted">Klik untuk melihat detail.</small>
                </a>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted"></i>
                    <p class="mt-3 text-muted">Tidak ada notifikasi.</p>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-end mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
