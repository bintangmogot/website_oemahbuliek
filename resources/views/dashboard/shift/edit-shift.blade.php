@extends('layouts.app')

@section('content')
  <x-form-layout 
    title="⏰ Edit Shift" 
    :backRoute="route('shift.index')"
    :submitRoute="route('shift.update', $shift->id)"        
    submitMethod="PUT"                       
    formId="shift-form"                        
    submitLabel="Simpan Perubahan"
    >                 
    @include('dashboard.shift.form-shift', ['shift' => $shift])
  </x-form-layout>
@endsection
