@props(['status'])

@php
    $styles = [
        'working' => ['En la oficina', 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300', 'bg-emerald-500'],
        'lunch' => ['Comiendo', 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300', 'bg-amber-500'],
        'done' => ['Ya salió', 'bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300', 'bg-zinc-400'],
        'out' => ['Sin llegar', 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800/60 dark:text-zinc-400', 'bg-zinc-300 dark:bg-zinc-600'],
    ];
    [$label, $classes, $dot] = $styles[$status];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold', $classes]) }}>
    <span @class(['size-1.5 rounded-full', $dot, 'animate-pulse' => in_array($status, ['working', 'lunch'])])></span>
    {{ $slot->isEmpty() ? $label : $slot }}
</span>
