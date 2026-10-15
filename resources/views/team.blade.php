<x-app-layout title="Equipo">
    <x-page-header title="Equipo hoy" :subtitle="ucfirst(now()->translatedFormat('l j \d\e F'))" />

    <div class="no-scrollbar -mx-4 mb-5 flex gap-2 overflow-x-auto px-4">
        @foreach (['working' => 'En oficina', 'lunch' => 'Comiendo', 'done' => 'Ya salieron', 'out' => 'Sin llegar'] as $key => $label)
            <x-status-pill :status="$key" class="shrink-0 px-3 py-1.5 text-sm">{{ $label }} · {{ $counts->get($key, 0) }}</x-status-pill>
        @endforeach
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($members as $member)
            @php
                $day = $member->todayAttendance;
                $status = $day?->status() ?? 'out';
            @endphp
            <article @class(['card p-4', 'opacity-70' => $status === 'out'])>
                <div class="flex items-center gap-3">
                    <x-avatar :user="$member" :status="$status" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold">{{ $member->name }}</p>
                        <p class="truncate text-sm text-zinc-500">{{ $member->position ?? 'Sin puesto' }}</p>
                    </div>
                    @if ($status === 'lunch')
                        <div data-timer-tone data-tone="ok" class="group rounded-xl bg-amber-100 px-2.5 py-1.5 text-right data-[tone=over]:bg-red-100 dark:bg-amber-500/15 dark:data-[tone=over]:bg-red-500/15">
                            <p data-timer data-format="short" data-limit="{{ $lunchLimit }}" data-since="{{ $day->lunch_out_at->toIso8601String() }}"
                                class="text-sm font-extrabold tabular-nums text-amber-800 group-data-[tone=over]:text-red-700 dark:text-amber-300 dark:group-data-[tone=over]:text-red-300">{{ $day->lunchMinutes() }} min</p>
                            <p class="text-[10px] font-semibold text-amber-700/80 group-data-[tone=over]:text-red-700/80 dark:text-amber-300/80">comiendo</p>
                        </div>
                    @else
                        <x-status-pill :status="$status" />
                    @endif
                </div>

                <dl class="mt-3 grid grid-cols-3 gap-2 rounded-2xl bg-zinc-50 p-2.5 text-center dark:bg-zinc-950/60">
                    <div>
                        <dt class="text-[10px] font-semibold text-zinc-500 uppercase">Llegó</dt>
                        <dd @class(['text-sm font-bold tabular-nums', 'text-amber-600 dark:text-amber-400' => $day && $day->lateMinutes() > 0])>{{ $day?->check_in_at?->format('H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold text-zinc-500 uppercase">Comida</dt>
                        <dd @class(['text-sm font-bold tabular-nums', 'text-red-600 dark:text-red-400' => $day && $day->isOverLunch()])>{{ $day?->lunch_out_at ? $day->lunchMinutes().' min' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold text-zinc-500 uppercase">Salió</dt>
                        <dd class="text-sm font-bold tabular-nums">{{ $day?->check_out_at?->format('H:i') ?? '—' }}</dd>
                    </div>
                </dl>

                @if ($member->tasks->isNotEmpty())
                    <div class="mt-3 flex flex-wrap items-center gap-1.5">
                        <x-icon name="briefcase" class="size-3.5 text-zinc-400" />
                        @foreach ($member->tasks->take(2) as $task)
                            <span class="inline-flex max-w-full items-center gap-1.5 truncate text-xs text-zinc-600 dark:text-zinc-300">
                                <x-project-dot :color="$task->project->color" class="size-2" />{{ $task->title }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </article>
        @endforeach
    </div>
</x-app-layout>
