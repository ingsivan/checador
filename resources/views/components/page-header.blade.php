@props(['title', 'subtitle' => null])

<div {{ $attributes->class(['mb-5 flex flex-wrap items-end justify-between gap-3']) }}>
    <div class="min-w-0">
        <h1 class="text-[26px] leading-tight font-extrabold tracking-tight md:text-3xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
