@props(['class'=>''])
<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm p-4 ' . $class]) }}>
    {{ $slot }}
</div>
