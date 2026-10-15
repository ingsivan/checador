@props(['color'])

@php
    $colors = [
        'indigo' => 'bg-indigo-500', 'emerald' => 'bg-emerald-500', 'amber' => 'bg-amber-500', 'rose' => 'bg-rose-500',
        'sky' => 'bg-sky-500', 'violet' => 'bg-violet-500', 'orange' => 'bg-orange-500', 'teal' => 'bg-teal-500',
    ];
@endphp

<span {{ $attributes->class(['inline-block size-2.5 shrink-0 rounded-full', $colors[$color] ?? $colors['indigo']]) }}></span>
