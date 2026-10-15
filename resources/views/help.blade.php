@php
    $lunch = config('checador.lunch_minutes');
    $tolerance = config('checador.late_tolerance_minutes');
@endphp

<x-app-layout title="Cómo funciona" :back="auth()->check() ? route('more') : route('login')">
    {{-- Hero --}}
    <section class="relative mb-8 overflow-hidden rounded-[2rem] bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 px-6 py-10 text-center text-white shadow-xl shadow-indigo-600/20 md:py-14">
        <div class="absolute -top-16 -right-16 size-56 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-20 -left-10 size-56 rounded-full bg-violet-400/30 blur-2xl"></div>
        <div class="relative">
            <x-logo class="mx-auto size-16 rounded-2xl ring-4 ring-white/20" />
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight md:text-4xl">Así funciona Checador</h1>
            <p class="mx-auto mt-2 max-w-md text-indigo-100">Registra tu día con cuatro toques, lleva el tiempo de tu comida y comparte en qué proyecto estás.</p>
            @guest
                <a href="{{ route('login') }}" class="btn mt-6 bg-white px-6 py-3 text-indigo-700 hover:bg-indigo-50">Entrar</a>
            @endguest
        </div>
    </section>

    {{-- The day --}}
    <h2 class="mb-1 text-xl font-extrabold tracking-tight">Tu día en 4 toques</h2>
    <p class="mb-4 text-sm text-zinc-500">En <strong>Inicio</strong> siempre verás un solo botón grande con lo que sigue.</p>
    <ol class="mb-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['login', 'Llegada', 'Al llegar a la oficina toca “Registrar llegada”. Si llegas después de tu hora (con '.$tolerance.' min de tolerancia) se marca como tarde.', 'bg-emerald-500'],
            ['utensils', 'Salgo a comer', 'Toca al salir a comer y arranca tu timer. Comer es opcional: si no sales, puedes registrar tu salida directo.', 'bg-amber-500'],
            ['coffee-back', 'Ya regresé', 'Al volver toca “Ya regresé de comer”. El tiempo de comida se guarda en tu historial.', 'bg-indigo-500'],
            ['logout', 'Salida', 'Al terminar tu jornada toca “Registrar salida”. Ya no se puede registrar nada más ese día.', 'bg-zinc-800 dark:bg-zinc-600'],
        ] as $i => [$icon, $title, $text, $color])
            <li class="card relative p-5">
                <span class="absolute top-4 right-5 text-4xl font-black text-zinc-100 dark:text-zinc-800">{{ $i + 1 }}</span>
                <span class="relative grid size-12 place-items-center rounded-2xl text-white shadow-lg {{ $color }}">
                    <x-icon :name="$icon" class="size-6" />
                </span>
                <p class="relative mt-3 font-bold">{{ $title }}</p>
                <p class="relative mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $text }}</p>
            </li>
        @endforeach
    </ol>

    {{-- Lunch timer --}}
    <section class="card mb-10 grid items-center gap-6 p-6 md:grid-cols-2">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight">El timer de comida</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Mientras comes, Inicio muestra un círculo que se va llenando. Tienes <strong>{{ $lunch }} minutos</strong>.</p>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li class="flex items-center gap-3"><span class="size-3 rounded-full bg-amber-500"></span> Vas a tiempo.</li>
                <li class="flex items-center gap-3"><span class="size-3 rounded-full bg-orange-500"></span> Te quedan pocos minutos (último 20%).</li>
                <li class="flex items-center gap-3"><span class="size-3 rounded-full bg-red-500"></span> Te pasaste. Se marca en tu historial y en el reporte semanal.</li>
            </ul>
        </div>
        <div class="flex justify-center gap-5">
            @foreach ([[35, 'stroke-amber-500', '21:04'], [85, 'stroke-orange-500', '51:12'], [100, 'stroke-red-500', '1:07:40']] as [$pct, $stroke, $label])
                <div class="relative size-24">
                    <svg viewBox="0 0 120 120" class="size-full -rotate-90">
                        <circle cx="60" cy="60" r="52" fill="none" stroke-width="12" class="stroke-zinc-100 dark:stroke-zinc-800" />
                        <circle cx="60" cy="60" r="52" fill="none" stroke-width="12" stroke-linecap="round" pathLength="100" stroke-dasharray="100" stroke-dashoffset="{{ 100 - $pct }}" class="{{ $stroke }}" />
                    </svg>
                    <span class="absolute inset-0 grid place-items-center text-xs font-extrabold tabular-nums">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Sections of the app --}}
    <h2 class="mb-4 text-xl font-extrabold tracking-tight">Las secciones de la app</h2>
    <div class="mb-10 grid gap-3 sm:grid-cols-2">
        @foreach ([
            ['home', 'Inicio', 'Tu estado de hoy, el botón del siguiente paso, tus horas y comida de la semana y tus tareas en progreso.'],
            ['users', 'Equipo', 'Quién está en la oficina, quién está comiendo (con su timer), quién ya salió y en qué está trabajando cada quien.'],
            ['board', 'Tablero', 'Kanban con las columnas Pendiente, En progreso, En revisión y Terminado. Arrastra las tarjetas o tócalas para editarlas.'],
            ['calendar', 'Historial', 'Tus registros de cada día por semana: llegadas tarde, comidas largas y días sin salida.'],
        ] as [$icon, $title, $text])
            <div class="card flex gap-4 p-5">
                <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300"><x-icon :name="$icon" /></span>
                <div>
                    <p class="font-bold">{{ $title }}</p>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $text }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Admin --}}
    <section class="card mb-10 p-6">
        <div class="flex items-center gap-3">
            <span class="grid size-11 place-items-center rounded-2xl bg-violet-100 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300"><x-icon name="chart" /></span>
            <h2 class="text-xl font-extrabold tracking-tight">Para el administrador</h2>
        </div>
        <ul class="mt-4 grid gap-3 text-sm text-zinc-600 sm:grid-cols-3 dark:text-zinc-400">
            <li><strong class="block text-zinc-900 dark:text-white">Reportes</strong> Horas de comida por semana, ranking de quién tarda más, llegadas tarde y descarga a Excel (CSV).</li>
            <li><strong class="block text-zinc-900 dark:text-white">Empleados</strong> Da de alta a cada persona con su correo, contraseña y horario. Desactiva a quien ya no está.</li>
            <li><strong class="block text-zinc-900 dark:text-white">Proyectos</strong> Crea los proyectos con un color. Sus tareas aparecen en el tablero.</li>
        </ul>
    </section>

    {{-- Install --}}
    <section class="mb-10 grid gap-3 md:grid-cols-2">
        <div class="md:col-span-2">
            <h2 class="text-xl font-extrabold tracking-tight">Instálala en tu celular</h2>
            <p class="mt-1 text-sm text-zinc-500">Queda como una app más en tu pantalla de inicio, sin tienda de apps.</p>
        </div>
        <div class="card p-5">
            <p class="flex items-center gap-2 font-bold"><x-icon name="smartphone" /> iPhone (Safari)</p>
            <ol class="mt-3 list-decimal space-y-1.5 pl-5 text-sm text-zinc-600 dark:text-zinc-400">
                <li>Abre la página en Safari.</li>
                <li>Toca el botón <strong>Compartir</strong> (cuadro con flecha).</li>
                <li>Elige <strong>Agregar a inicio</strong> y confirma.</li>
            </ol>
        </div>
        <div class="card p-5">
            <p class="flex items-center gap-2 font-bold"><x-icon name="smartphone" /> Android (Chrome)</p>
            <ol class="mt-3 list-decimal space-y-1.5 pl-5 text-sm text-zinc-600 dark:text-zinc-400">
                <li>Abre la página en Chrome.</li>
                <li>Toca el menú <strong>⋮</strong> arriba a la derecha.</li>
                <li>Elige <strong>Instalar app</strong> o <strong>Agregar a pantalla principal</strong>.</li>
            </ol>
        </div>
    </section>

    {{-- FAQ --}}
    <h2 class="mb-4 text-xl font-extrabold tracking-tight">Preguntas frecuentes</h2>
    <div class="card divide-y divide-zinc-100 dark:divide-zinc-800">
        @foreach ([
            ['Se me olvidó registrar mi salida, ¿qué pasa?', 'Ese día aparece como “Sin salida” en tu historial y en el reporte. Avísale al administrador.'],
            ['¿Tengo que salir a comer?', 'No. Después de tu llegada puedes elegir “Salgo a comer” o registrar tu salida directamente.'],
            ['¿Quién ve mis registros?', 'Todo el equipo ve el estado de hoy (en oficina, comiendo, salió). Los reportes semanales y el ranking solo los ve el administrador.'],
            ['¿Toqué un botón por error?', 'Los registros no se pueden borrar desde la app para que sean confiables. Pide al administrador que lo corrija.'],
        ] as [$q, $a])
            <details class="group p-5">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold">
                    {{ $q }}
                    <x-icon name="chevron-right" class="size-4 shrink-0 text-zinc-400 transition group-open:rotate-90" />
                </summary>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $a }}</p>
            </details>
        @endforeach
    </div>
</x-app-layout>
