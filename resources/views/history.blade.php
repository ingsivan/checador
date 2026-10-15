<x-app-layout title="Historial">
    <x-page-header title="Mi historial" subtitle="Tus registros de cada día." />

    <x-week-nav :week="$week" route="history" class="mb-4" />

    <div class="mb-5 grid grid-cols-3 gap-3">
        <x-stat label="Trabajado" :value="minutes_label($totalWorked)" />
        <x-stat label="Comida" :value="minutes_label($totalLunch)" :hint="$days->whereNotNull('lunch_out_at')->count() ? 'Prom. '.intdiv($totalLunch, max(1, $days->whereNotNull('lunch_out_at')->count())).' min' : null" />
        <x-stat label="Tardes" :value="$lateCount" :tone="$lateCount ? 'warn' : null" />
    </div>

    <div class="space-y-3">
        @forelse ($days as $day)
            <article class="card p-4">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <div>
                        <p class="font-bold">{{ ucfirst($day->date->translatedFormat('l j')) }}</p>
                        <p class="text-xs text-zinc-500">{{ $day->date->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="flex flex-wrap justify-end gap-1.5">
                        @if ($day->lateMinutes() > 0)
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-500/15 dark:text-amber-300">{{ $day->lateMinutes() }} min tarde</span>
                        @endif
                        @if ($day->isOverLunch())
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-300">Comida +{{ $day->lunchMinutes() - $lunchLimit }} min</span>
                        @endif
                        @if ($day->check_in_at && ! $day->check_out_at && ! $day->date->isToday())
                            <span class="rounded-full bg-zinc-200 px-2 py-0.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">Sin salida</span>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-2 text-center">
                    @foreach (\App\Models\Attendance::STEPS as $field => $label)
                        <div class="rounded-xl bg-zinc-50 py-2 dark:bg-zinc-950/60">
                            <p class="text-[10px] leading-tight font-semibold text-zinc-500">{{ $label }}</p>
                            <p class="text-sm font-bold tabular-nums">{{ $day->{$field}?->format('H:i') ?? '—' }}</p>
                        </div>
                    @endforeach
                </div>
                <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">
                    Trabajaste <strong class="text-zinc-900 dark:text-white">{{ minutes_label($day->workedMinutes()) }}</strong>
                    @if ($day->lunch_out_at)
                        · comida <strong @class(['text-zinc-900 dark:text-white' => ! $day->isOverLunch(), 'text-red-600 dark:text-red-400' => $day->isOverLunch()])>{{ minutes_label($day->lunchMinutes()) }}</strong>
                    @endif
                </p>
            </article>
        @empty
            <div class="card p-10 text-center">
                <x-icon name="calendar" class="mx-auto size-8 text-zinc-400" />
                <p class="mt-2 font-semibold">Sin registros esta semana</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
