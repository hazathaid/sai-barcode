@props(['type' => 'default'])

@php
    $base = 'p-4 rounded-xl text-sm';
    $variants = [
        'success' => 'bg-emerald-50 text-emerald-800',
        'warning' => 'bg-amber-50 text-amber-800',
        'error' => 'bg-rose-50 text-rose-800',
        'info' => 'bg-indigo-50 text-indigo-800',
        'default' => 'bg-gray-50 text-gray-800',
    ];
    $classes = $variants[$type] ?? $variants['default'];
@endphp

<div {{ $attributes->merge(['class' => $base . ' ' . $classes ]) }}>
    {{ $slot }}
</div>
