@props(['label', 'name', 'type' => 'text'])

<div class="space-y-2">
    <label for="{{ $name }}" class="label">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" class="input" id="{{ $name }}" {{ $attributes }} value="{{ old($name, '') }}">

    @error($name)
    <p class="error">{{ $message }}</p>
    @enderror
</div>
