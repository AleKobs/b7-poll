@props([
    'tone' => 'danger',
])

<div {{ $attributes->class(['alert', $tone]) }} role="alert">
    {{ $slot }}
</div>
