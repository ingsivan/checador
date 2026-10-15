@props(['user', 'size' => 'md', 'status' => null])

@php
    // Stable pastel per person so the team is easy to tell apart.
    $palette = [
        'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200',
        'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200',
        'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
        'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-200',
        'bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-200',
        'bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-200',
        'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-200',
        'bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-200',
    ];
    $color = $palette[$user->id % count($palette)];
    $sizes = ['xs' => 'size-6 text-[10px]', 'sm' => 'size-9 text-xs', 'md' => 'size-11 text-sm', 'lg' => 'size-16 text-xl'];
    $dots = [
        'working' => 'bg-emerald-500',
        'lunch' => 'bg-amber-500',
        'done' => 'bg-zinc-400',
        'out' => 'bg-zinc-300 dark:bg-zinc-600',
    ];
@endphp

<span {{ $attributes->class(['relative inline-grid shrink-0 place-items-center rounded-full font-bold', $sizes[$size], $color]) }} title="{{ $user->name }}">
    {{ $user->initials() }}
    @if ($status)
        <span class="absolute -right-0.5 -bottom-0.5 size-3.5 rounded-full border-2 border-white dark:border-zinc-900 {{ $dots[$status] }}"></span>
    @endif
</span>
