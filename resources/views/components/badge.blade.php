@props(['type' => 'secondary'])

@php
    $colors = [
        'primary' => 'bg-blue-100 text-blue-800',
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger'  => 'bg-red-100 text-red-800',
        'secondary' => 'bg-gray-100 text-gray-800',
    ];
    $colorClass = $colors[$type] ?? $colors['secondary'];
@endphp

<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
    {{ $slot }}
</span>