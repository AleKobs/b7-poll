@props([
    'variant' => 'secondary',
    'href' => null,
    'icon' => null,
    'type' => 'button',
])

@php($classes = ['button', 'focus-ring', $variant])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <img src="{{ asset('assets/premium-home/'.$icon.'.svg') }}" alt="">
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <img src="{{ asset('assets/premium-home/'.$icon.'.svg') }}" alt="">
        @endif
        {{ $slot }}
    </button>
@endif
