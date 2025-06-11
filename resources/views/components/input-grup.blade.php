@props([
  'name',
  'label',
  'value' => '',
  'type' => 'text',
  'required' => false,
])

<div class="mb-3">
  <label for="{{ $name }}" class="form-label">{{ $label }}</label>
  <input 
    type="{{ $type }}" 
    name="{{ $name }}" 
    id="{{ $name }}" 
    value="{{ old($name, $value) }}" 
    {{ $required ? 'required' : '' }}
    class="form-control @error($name) is-invalid @enderror">

  @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
