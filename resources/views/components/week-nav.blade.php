@props(['week', 'route', 'params' => []])

<div {{ $attributes->class(['card flex items-center gap-2 p-1.5']) }}>
    <a href="{{ route($route, $params + ['semana' => $week->previous()]) }}" class="btn-ghost size-10 p-0" aria-label="Semana anterior">
        <x-icon name="chevron-left" />
    </a>
    <div class="flex-1 text-center">
        <p class="text-sm font-bold">{{ $week->label() }}</p>
        @if ($week->isCurrent()) <p class="text-[11px] font-semibold text-brand-600 dark:text-brand-400">Esta semana</p> @endif
    </div>
    @if ($week->next())
        <a href="{{ route($route, $params + ['semana' => $week->next()]) }}" class="btn-ghost size-10 p-0" aria-label="Semana siguiente">
            <x-icon name="chevron-right" />
        </a>
    @else
        <span class="size-10"></span>
    @endif
</div>
