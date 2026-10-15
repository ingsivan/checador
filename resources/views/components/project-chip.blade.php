@props(['project'])

@php
    $colors = [
        'indigo' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300',
        'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
        'amber' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300',
        'rose' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
        'sky' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300',
        'violet' => 'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300',
        'orange' => 'bg-orange-100 text-orange-800 dark:bg-orange-500/15 dark:text-orange-300',
        'teal' => 'bg-teal-100 text-teal-700 dark:bg-teal-500/15 dark:text-teal-300',
    ];
@endphp

<span {{ $attributes->class(['inline-flex max-w-full items-center truncate rounded-md px-2 py-0.5 text-[11px] font-semibold', $colors[$project->color] ?? $colors['indigo']]) }}>
    {{ $project->name }}
</span>
