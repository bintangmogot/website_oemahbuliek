@props([
  'title',
  // default backRoute → index
  'backRoute'   => route('jadwal-shift.index'),

  // **NEW** props untuk tombol Simpan
  // default action kosong (harus dioverride), method POST
  'submitRoute' => '',          
  'submitMethod'=> 'POST',      
  'submitLabel' => 'Simpan',    
  'formId'      => 'main-form',  
])

@php
  // Kalau method spoofing (PUT/PATCH/DELETE), form harus POST
  $httpMethod = strtoupper($submitMethod) === 'GET' ? 'GET' : 'POST';
@endphp

<div class="px-3 py-5" style="background-color: #fcecec; min-height:100vh;">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card shadow-sm border-0">

        {{-- buka FORM di layout, pakai prop --}}
        <form 
          id="{{ $formId }}" 
          action="{{ $submitRoute }}" 
          method="{{ $httpMethod }}" 
          enctype="multipart/form-data"
        >

          @if($httpMethod === 'POST')
            @csrf
          @endif

          {{-- spoofing untuk PUT/PATCH/DELETE --}}
          @if(in_array(strtoupper($submitMethod), ['PUT','PATCH','DELETE']))
            @method($submitMethod)
          @endif

          <div class="card-body py-4 px-4 px-lg-5 gap-3">
            <x-session-status class="mb-2"/>
            <div class="card-theme py-3 mb-3">
              <h3 class="text-center fw-bold">{{ $title }}</h3>
            </div>

            {{-- konten form --}}
            {{ $slot }}
          </div>

          {{-- FOOTER --}}
          <div class="card-footer d-flex justify-content-between py-3 px-3 p-sm-3 px-lg-5 gap-3">
            {{-- tombol Kembali --}}
            <a href="{{ $backRoute }}" class="btn btn-theme info">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>

            {{-- tombol Simpan, terikat ke formId --}}
            <button 
              type="submit" 
              form="{{ $formId }}" 
              class="btn btn-theme success"
            >
              <i class="fas fa-save"></i> {{ $submitLabel }}
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
