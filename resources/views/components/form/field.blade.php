@props([
    'label' => false,
    'name', 'type' => 'text',
    'value' => null
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="label">{{ $label }}</label>
    @endif

    @if($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            class="textarea"
            {{ $attributes }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input type="{{ $type }}" name="{{ $name }}" class="input" id="{{ $name }}"
               {{ $attributes }} value="{{ old($name, $value) }}">
    @endif

    @error($name)
    <p class="error">{{ $message }}</p>
    @enderror
</div>
