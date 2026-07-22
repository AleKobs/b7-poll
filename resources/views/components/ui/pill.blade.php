@props([
    'tone' => null,
    'icon' => null,
])

<span {{ $attributes->class(['pill', $tone]) }}>
    @if ($icon)
        <img src="{{ asset('assets/premium-home/'.$icon.'.svg') }}" alt="">
    @endif
    {{ $slot }}
</span>
