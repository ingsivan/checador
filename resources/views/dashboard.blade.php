@php
    $status = $attendance->status();
    $steps = $attendance->allowedSteps();
    $lunchLeft = $lunchLimit - $attendance->lunchMinutes();
@endphp

<x-app-layout title="Inicio">
    <div class="mb-5">
        <p class="text-sm font-semibold text-zinc-500 dark:text-zinc-400">{{ ucfirst(now()->translatedFormat('l j \d\e F')) }}</p>
        <h1 class="mt-0.5 text-[28px] leading-tight font-extrabold tracking-tight md:text-3xl">Hola, {{ str($user->name)->before(' ') }} 👋</h1>
    </div>

    <div class="grid gap-5 md:grid-cols-5">
        {{-- Today's status, timer and the one big button for the next step. --}}
        <section class="card overflow-hidden md:col-span-3">
            @if ($status === 'lunch')
                <div data-timer-tone data-tone="{{ $lunchLeft < 0 ? 'over' : ($lunchLeft < $lunchLimit * 0.2 ? 'near' : 'ok') }}" class="group bg-gradient-to-b from-amber-50 to-white px-6 pt-6 pb-5 text-center data-[tone=over]:from-red-50 dark:from-amber-500/10 dark:to-zinc-900 dark:data-[tone=over]:from-red-500/10">
                    <x-status-pill status="lunch" />
                    <div class="relative mx-auto mt-4 size-56">
                        <svg viewBox="0 0 120 120" class="size-full -rotate-90">
                            <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" class="stroke-amber-100 dark:stroke-zinc-800" />
                            <circle data-timer-ring cx="60" cy="60" r="52" fill="none" stroke-width="10" stroke-linecap="round" pathLength="100" stroke-dasharray="100" stroke-dashoffset="100"
                                class="stroke-amber-500 transition-[stroke-dashoffset] duration-1000 group-data-[tone=near]:stroke-orange-500 group-data-[tone=over]:stroke-red-500" />
                        </svg>
                        <div class="absolute inset-0 grid place-content-center">
                            <x-icon name="utensils" class="mx-auto mb-1 size-6 text-amber-500 group-data-[tone=over]:text-red-500" />
                            <span data-timer data-since="{{ $attendance->lunch_out_at->toIso8601String() }}" data-limit="{{ $lunchLimit }}"
                                class="text-5xl font-extrabold tabular-nums tracking-tight group-data-[tone=over]:text-red-600 dark:group-data-[tone=over]:text-red-400">--:--</span>
                            <span class="mt-1 text-xs font-semibold text-zinc-500">de {{ $lunchLimit }} min</span>
                        </div>
                    </div>
                    <p data-timer-left class="mt-2 text-sm font-semibold text-amber-700 group-data-[tone=over]:text-red-600 dark:text-amber-300 dark:group-data-[tone=over]:text-red-400">&nbsp;</p>
                    <p class="text-xs text-zinc-500">Saliste a las {{ $attendance->lunch_out_at->format('H:i') }}</p>
                </div>
            @elseif ($status === 'working')
                <div class="bg-gradient-to-b from-emerald-50 to-white px-6 pt-6 pb-5 text-center dark:from-emerald-500/10 dark:to-zinc-900">
                    <x-status-pill status="working" />
                    <p class="mt-5 text-xs font-semibold tracking-wide text-zinc-500 uppercase">Tiempo trabajado hoy</p>
                    <p data-timer data-since="{{ now()->subSeconds($attendance->workedSeconds())->toIso8601String() }}"
                        class="mt-1 text-6xl font-extrabold tabular-nums tracking-tight">--:--</p>
                    <p class="mt-2 text-sm text-zinc-500">
                        Llegaste a las {{ $attendance->check_in_at->format('H:i') }}
                        @if ($attendance->lateMinutes() > 0)
                            · <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $attendance->lateMinutes() }} min tarde</span>
                        @endif
                    </p>
                </div>
            @elseif ($status === 'done')
                <div class="px-6 pt-6 pb-5 text-center">
                    <span class="mx-auto grid size-16 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">
                        <x-icon name="check" class="size-8" />
                    </span>
                    <p class="mt-3 text-xl font-extrabold">Jornada terminada</p>
                    <p class="mt-1 text-sm text-zinc-500">Trabajaste {{ minutes_label($attendance->workedMinutes()) }} y comiste {{ minutes_label($attendance->lunchMinutes()) }}.</p>
                </div>
            @else
                <div class="px-6 pt-8 pb-5 text-center">
                    <span class="mx-auto grid size-16 place-items-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                        <x-icon name="clock" class="size-8" />
                    </span>
                    <p class="mt-3 text-xl font-extrabold">Aún no registras tu llegada</p>
                    <p class="mt-1 text-sm text-zinc-500">Tu horario empieza a las {{ substr($user->work_start, 0, 5) }}.</p>
                </div>
            @endif

            @if ($steps)
                <div class="space-y-2.5 px-5 pb-5">
                    <x-clock-button :step="$steps[0]" />
                    @foreach (array_slice($steps, 1) as $step)
                        <x-clock-button :step="$step" variant="secondary" />
                    @endforeach
                </div>
            @endif

            {{-- The four marks of the day. --}}
            <ol class="grid grid-cols-4 border-t border-zinc-100 dark:border-zinc-800">
                @foreach (\App\Models\Attendance::STEPS as $field => $label)
                    @php($time = $attendance->{$field})
                    <li class="px-1 py-3.5 text-center">
                        <span @class([
                            'mx-auto grid size-7 place-items-center rounded-full',
                            'bg-emerald-500 text-white' => $time,
                            'border-2 border-dashed border-zinc-300 dark:border-zinc-700' => ! $time,
                        ])>
                            @if ($time) <x-icon name="check" class="size-4" /> @endif
                        </span>
                        <p class="mt-1.5 text-[11px] leading-tight font-semibold text-zinc-500">{{ $label }}</p>
                        <p class="text-sm font-bold tabular-nums">{{ $time?->format('H:i') ?? '--:--' }}</p>
                    </li>
                @endforeach
            </ol>
        </section>

        <div class="space-y-5 md:col-span-2">
            <div class="grid grid-cols-2 gap-3">
                <x-stat label="Comida semana" :value="minutes_label($weekLunchMinutes)" hint="Lunes a hoy" />
                <x-stat label="Horas semana" :value="minutes_label($weekWorkedMinutes)" hint="Sin contar comida" />
            </div>

            <a href="{{ route('team') }}" class="card flex items-center gap-4 p-4 transition hover:border-brand-300 active:scale-[0.99]">
                <span class="grid size-11 place-items-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                    <x-icon name="users" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="font-bold">Equipo hoy</p>
                    <p class="text-sm text-zinc-500">
                        {{ $team->get('working', 0) }} en oficina · {{ $team->get('lunch', 0) }} comiendo · {{ $team->get('out', 0) }} sin llegar
                    </p>
                </div>
                <x-icon name="chevron-right" class="text-zinc-400" />
            </a>

            <section class="card p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="font-bold">En lo que estoy trabajando</h2>
                    <a href="{{ route('board', ['mias' => 1]) }}" class="text-sm font-semibold text-brand-600 dark:text-brand-400">Ver tablero</a>
                </div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($myTasks as $task)
                    <div class="flex items-center gap-3 py-2.5">
                        <x-project-dot :color="$task->project->color" />
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">{{ $task->title }}</p>
                            <p class="truncate text-xs text-zinc-500">{{ $task->project->name }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500">No tienes tareas en progreso. Mueve una tarjeta a “En progreso” en el tablero.</p>
                @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
