@props([
    'tone' => 'success',
])

<div {{ $attributes->class(['toast', $tone]) }} role="status" data-toast>
    {{ $slot }}
</div>
