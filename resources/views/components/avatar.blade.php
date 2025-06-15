@props([
    'src' => null,
    'size' => 100,
])

@php
    $width = $size;
    $height = $size;
@endphp

@if ($src && file_exists(public_path('storage/' . $src)))
    <img src="{{ asset('storage/' . $src) }}"
         class="rounded-circle mb-3"
         width="{{ $width }}" height="{{ $height }}"
         style="object-fit: cover"
         alt="Foto Profil">
@else
    <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center mb-3"
         style="width: {{ $width }}px; height: {{ $height }}px;">
        <i class="bi bi-person text-white" style="font-size: {{ $width / 2 }}px;"></i>
    </div>
@endif
