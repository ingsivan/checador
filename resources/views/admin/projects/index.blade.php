<x-app-layout title="Proyectos" :back="route('more')">
    <x-page-header title="Proyectos" subtitle="Los proyectos activos aparecen en el tablero.">
        <x-slot:actions>
            <a href="{{ route('admin.projects.create') }}" class="btn-primary"><x-icon name="plus" class="size-4" /> Nuevo</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-3 sm:grid-cols-2">
        @forelse ($projects as $project)
            @php($progress = $project->tasks_count ? round($project->done_count / $project->tasks_count * 100) : 0)
            <a href="{{ route('admin.projects.edit', $project) }}" @class(['card block p-5 transition hover:border-brand-300 active:scale-[0.99]', 'opacity-60' => ! $project->is_active])>
                <div class="flex items-start gap-3">
                    <x-project-dot :color="$project->color" class="mt-1.5 size-3" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold">{{ $project->name }}</p>
                        <p class="truncate text-sm text-zinc-500">{{ $project->client ?? 'Sin cliente' }}</p>
                    </div>
                    @unless ($project->is_active)
                        <span class="rounded-full bg-zinc-200 px-2 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Archivado</span>
                    @endunless
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-semibold text-zinc-500">
                    <span>{{ $project->done_count }} de {{ $project->tasks_count }} tareas terminadas</span>
                    <span>{{ $progress }}%</span>
                </div>
                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $progress }}%"></div>
                </div>
            </a>
        @empty
            <div class="card p-10 text-center sm:col-span-2">
                <p class="font-semibold">Todavía no hay proyectos.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
