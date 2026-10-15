@props(['active' => false])

<a {{ $attributes->class([
    'rounded-lg px-3 py-2 transition',
    'bg-brand-50 text-brand-700 dark:bg-brand-600/20 dark:text-brand-100' => $active,
    'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white' => ! $active,
]) }} @if ($active) aria-current="page" @endif>
    {{ $slot }}
</a>
