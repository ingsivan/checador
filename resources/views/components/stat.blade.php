@props(['label', 'value', 'hint' => null, 'tone' => null])

<div {{ $attributes->class(['card p-4']) }}>
    <p class="text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">{{ $label }}</p>
    <p @class([
        'mt-1 text-2xl font-extrabold tabular-nums tracking-tight',
        'text-red-600 dark:text-red-400' => $tone === 'bad',
        'text-amber-600 dark:text-amber-400' => $tone === 'warn',
    ])>{{ $value }}</p>
    @if ($hint)
        <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif
</div>
