@php
    $withLunch = $rows->filter(fn ($r) => $r['avg_lunch'] > 0);
    $teamAvg = $withLunch->count() ? (int) round($withLunch->avg('avg_lunch')) : 0;
    // Scale so the limit line always fits with some room for whoever goes over.
    $scaleMax = max($lunchLimit * 1.25, (int) $ranking->max('avg_lunch'));
@endphp

<x-app-layout title="Reportes" :back="route('more')" wide>
    <x-page-header title="Reportes" subtitle="Resumen semanal del equipo.">
        <x-slot:actions>
            <a href="{{ route('admin.reports.export', ['semana' => $week->start->toDateString()]) }}" class="btn-secondary">
                <x-icon name="download" class="size-4" /> Excel (CSV)
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-week-nav :week="$week" route="admin.reports" class="mb-4 md:max-w-sm" />

    <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-stat label="Comida del equipo" :value="minutes_label($totals['lunch'])" hint="Suma de la semana" />
        <x-stat label="Promedio por comida" :value="$teamAvg.' min'" :hint="'Límite '.$lunchLimit.' min'" :tone="$teamAvg > $lunchLimit ? 'bad' : null" />
        <x-stat label="Comidas pasadas" :value="$totals['over']" hint="Veces que se pasaron del límite" :tone="$totals['over'] ? 'bad' : null" />
        <x-stat label="Llegadas tarde" :value="$totals['late']" hint="Después de la tolerancia" :tone="$totals['late'] ? 'warn' : null" />
    </div>

    <div class="grid gap-6 lg:grid-cols-5">
        {{-- Ranking: average lunch per person, with the limit as a reference line. --}}
        <section class="card p-5 lg:col-span-3">
            <div class="mb-1 flex items-center gap-2">
                <x-icon name="trophy" class="text-amber-500" />
                <h2 class="text-lg font-extrabold">¿Quién tarda más en comer?</h2>
            </div>
            <p class="mb-5 text-sm text-zinc-500">Promedio de minutos por comida. La línea punteada es el límite de {{ $lunchLimit }} min.</p>

            @forelse ($ranking as $i => $row)
                @php
                    $over = $row['avg_lunch'] > $lunchLimit;
                    $width = $scaleMax ? $row['avg_lunch'] / $scaleMax * 100 : 0;
                @endphp
                <a href="{{ route('admin.attendance.edit', ['user' => $row['user'], 'semana' => $week->start->toDateString()]) }}"
                    class="group -mx-2 flex items-center gap-3 rounded-2xl px-2 py-2 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                    title="{{ $row['user']->name }}: promedio {{ $row['avg_lunch'] }} min, máximo {{ $row['max_lunch'] }} min, {{ $row['over_days'] }} {{ $row['over_days'] === 1 ? 'día pasado' : 'días pasados' }}">
                    <span @class([
                        'w-5 text-center text-sm font-extrabold tabular-nums',
                        'text-amber-500' => $i === 0,
                        'text-zinc-400' => $i > 0,
                    ])>{{ $i + 1 }}</span>
                    <x-avatar :user="$row['user']" size="sm" />
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline justify-between gap-2">
                            <p class="truncate text-sm font-semibold">{{ $row['user']->name }}</p>
                            <p class="shrink-0 text-sm font-bold tabular-nums">
                                @if ($over) <x-icon name="alert" class="inline size-3.5 -translate-y-px text-red-500" /> @endif
                                {{ $row['avg_lunch'] }} min
                            </p>
                        </div>
                        <div class="relative mt-1.5 h-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div @class(['h-full rounded-full transition-all', 'bg-red-500' => $over, 'bg-brand-500' => ! $over]) style="width: {{ $width }}%"></div>
                            <span class="absolute -top-1 -bottom-1 border-l-2 border-dashed border-zinc-400 dark:border-zinc-500" style="left: {{ $lunchLimit / $scaleMax * 100 }}%"></span>
                        </div>
                        <p class="mt-1 text-xs text-zinc-500">
                            Máx. {{ $row['max_lunch'] }} min · {{ minutes_label($row['lunch']) }} en la semana
                            @if ($row['over_days']) · <span class="font-semibold text-red-600 dark:text-red-400">{{ $row['over_days'] }} {{ $row['over_days'] === 1 ? 'vez' : 'veces' }} pasado</span> @endif
                        </p>
                    </div>
                </a>
            @empty
                <p class="py-8 text-center text-sm text-zinc-500">Sin comidas registradas esta semana.</p>
            @endforelse
        </section>

        {{-- Per person detail. Tap to fix their marks. --}}
        <section class="lg:col-span-2">
            <h2 class="mb-3 text-lg font-extrabold">Detalle por persona</h2>
            <div class="card divide-y divide-zinc-100 overflow-hidden dark:divide-zinc-800">
                @foreach ($rows as $row)
                    <a href="{{ route('admin.attendance.edit', ['user' => $row['user'], 'semana' => $week->start->toDateString()]) }}" class="flex items-center gap-3 px-4 py-3 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <x-avatar :user="$row['user']" size="sm" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $row['user']->name }}</p>
                            <p class="text-xs text-zinc-500">{{ $row['days'] }} {{ $row['days'] === 1 ? 'día' : 'días' }} · {{ minutes_label($row['worked']) }} trabajadas</p>
                        </div>
                        <div class="flex flex-wrap justify-end gap-1">
                            @if ($row['late_days'])
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800 dark:bg-amber-500/15 dark:text-amber-300">{{ $row['late_days'] }} tarde</span>
                            @endif
                            @if ($row['open_days'])
                                <span class="rounded-full bg-zinc-200 px-2 py-0.5 text-[11px] font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $row['open_days'] }} sin salida</span>
                            @endif
                        </div>
                        <x-icon name="chevron-right" class="size-4 text-zinc-400" />
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
