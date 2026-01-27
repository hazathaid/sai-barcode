@props(['type' => 'button', 'class' => ''])
<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 ' . $class ]) }}>
    {{ $slot }}
</button>
