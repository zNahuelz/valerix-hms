@props([
    'type' => 'info',
])

@php
$styles = [
    'info' => 'bg-sky-100 dark:bg-sky-900 dark:border-blue-700 border-l-4 border-blue-900',
    'success' => 'bg-emerald-100 dark:bg-emerald-900 dark:border-green-700 border-l-4 border-green-900',
    'error' => 'bg-red-300 dark:bg-red-500 dark:border-red-700 border-l-4 border-red-900',
    'warning' => 'bg-yellow-300 dark:bg-yellow-400 dark:border-amber-600 border-l-4 border-amber-600 text-black'
];

$icons = [
    'info' => 'information-circle',
    'success' => 'check-circle',
    'error' => 'exclamation-circle',
    'warning' => 'exclamation-triangle',
];

$classes = $styles[$type] ?? $styles['info'];
$icon = $icons[$type] ?? $icons['info'];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition 
    {{ $attributes->merge([
        'class' => "flex items-center gap-2 p-4 mb-4 text-sm rounded-e-lg $classes"
    ]) }}>
    <flux:icon name="{{ $icon }}" />
    <div class="flex-1">
        {{ $slot }}
    </div>
    <flux:button icon="x-mark" variant="subtle" size="xs" @click="show = false" />
</div>