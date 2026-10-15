@php($user = auth()->user())

<x-app-layout title="Más">
    <section class="card mb-5 flex items-center gap-4 p-5">
        <x-avatar :user="$user" size="lg" />
        <div class="min-w-0">
            <p class="truncate text-lg font-extrabold">{{ $user->name }}</p>
            <p class="truncate text-sm text-zinc-500">{{ $user->position ?? 'Sin puesto' }}</p>
            <p class="mt-1 text-xs text-zinc-500">Horario {{ substr($user->work_start, 0, 5) }} – {{ substr($user->work_end, 0, 5) }}</p>
        </div>
    </section>

    @if ($user->isAdmin())
        <h2 class="mb-2 px-1 text-xs font-bold tracking-wide text-zinc-500 uppercase">Administración</h2>
        <div class="card mb-5 divide-y divide-zinc-100 overflow-hidden dark:divide-zinc-800">
            @foreach ([
                ['admin.reports', 'chart', 'Reportes', 'Comida por semana, ranking y llegadas tarde', 'bg-violet-100 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300'],
                ['admin.employees.index', 'users', 'Empleados', 'Altas, horarios y accesos', 'bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300'],
                ['admin.projects.index', 'folder', 'Proyectos', 'Proyectos del tablero', 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300'],
            ] as [$route, $icon, $label, $hint, $color])
                <a href="{{ route($route) }}" class="flex items-center gap-4 px-4 py-3.5 transition hover:bg-zinc-50 active:bg-zinc-100 dark:hover:bg-zinc-800/50">
                    <span class="grid size-10 place-items-center rounded-xl {{ $color }}"><x-icon :name="$icon" /></span>
                    <span class="min-w-0 flex-1">
                        <span class="block font-semibold">{{ $label }}</span>
                        <span class="block truncate text-sm text-zinc-500">{{ $hint }}</span>
                    </span>
                    <x-icon name="chevron-right" class="text-zinc-400" />
                </a>
            @endforeach
        </div>
    @endif

    <h2 class="mb-2 px-1 text-xs font-bold tracking-wide text-zinc-500 uppercase">General</h2>
    <div class="card divide-y divide-zinc-100 overflow-hidden dark:divide-zinc-800">
        <a href="{{ route('help') }}" class="flex items-center gap-4 px-4 py-3.5 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
            <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300"><x-icon name="help" /></span>
            <span class="min-w-0 flex-1">
                <span class="block font-semibold">Cómo funciona</span>
                <span class="block truncate text-sm text-zinc-500">Guía rápida e instalar en tu celular</span>
            </span>
            <x-icon name="chevron-right" class="text-zinc-400" />
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex w-full items-center gap-4 px-4 py-3.5 text-left transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                <span class="grid size-10 place-items-center rounded-xl bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-300"><x-icon name="logout" /></span>
                <span class="flex-1 font-semibold text-red-600 dark:text-red-400">Cerrar sesión</span>
            </button>
        </form>
    </div>
</x-app-layout>
