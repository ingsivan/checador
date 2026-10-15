<x-app-layout title="Empleados">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Empleados</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $employees->where('is_active', true)->count() }} activos de {{ $employees->count() }}</p>
        </div>
        <a href="{{ route('admin.employees.create') }}" class="btn-primary">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Agregar empleado
        </a>
    </div>

    <form method="GET" class="mb-4">
        <label for="q" class="sr-only">Buscar</label>
        <input id="q" name="q" value="{{ $search }}" type="search" placeholder="Buscar por nombre o correo" class="input max-w-sm">
    </form>

    <div class="card divide-y divide-zinc-200 overflow-hidden dark:divide-zinc-800">
        @forelse ($employees as $employee)
            <div @class(['flex flex-wrap items-center gap-4 p-4 sm:flex-nowrap', 'opacity-60' => ! $employee->is_active])>
                <span class="grid size-10 shrink-0 place-items-center rounded-full bg-zinc-100 text-sm font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                    {{ $employee->initials() }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="flex flex-wrap items-center gap-2 font-medium">
                        {{ $employee->name }}
                        @if ($employee->isAdmin())
                            <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700 dark:bg-brand-600/20 dark:text-brand-100">Admin</span>
                        @endif
                        @unless ($employee->is_active)
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Inactivo</span>
                        @endunless
                    </p>
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $employee->position ?? 'Sin puesto' }} · {{ $employee->email }}</p>
                </div>
                <p class="text-sm text-zinc-500 tabular-nums dark:text-zinc-400">{{ substr($employee->work_start, 0, 5) }}–{{ substr($employee->work_end, 0, 5) }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-secondary px-3 py-1.5">Editar</a>
                    @unless ($employee->is(auth()->user()))
                        <form method="POST" action="{{ route('admin.employees.toggle', $employee) }}">
                            @csrf @method('PATCH')
                            <button class="btn-secondary px-3 py-1.5">{{ $employee->is_active ? 'Desactivar' : 'Activar' }}</button>
                        </form>
                    @endunless
                </div>
            </div>
        @empty
            <p class="p-8 text-center text-sm text-zinc-500">No se encontraron empleados.</p>
        @endforelse
    </div>
</x-app-layout>
