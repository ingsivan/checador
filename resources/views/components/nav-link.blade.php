@props(['active' => false])

<a {{ $attributes->class([
    'rounded-full px-3.5 py-2 transition',
    'bg-white text-zinc-900 shadow-sm dark:bg-zinc-800 dark:text-white' => $active,
    'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' => ! $active,
]) }} @if ($active) aria-current="page" @endif>
    {{ $slot }}
</a>
