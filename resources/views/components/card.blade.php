@props([
    'is' => 'a'
])

@php
    $classes = 'border border-border rounded-lg bg-card p-4 md:text-sm';
@endphp


<{{$is}} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{$is}}>

