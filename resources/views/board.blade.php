<x-app-layout title="Tablero" wide>
    <x-page-header title="Tablero" subtitle="Arrastra las tarjetas entre columnas o tócalas para editarlas.">
        <x-slot:actions>
            {{-- Floating action button on phones, regular button on desktop. --}}
            <button type="button" data-task-new class="btn-primary max-md:fixed max-md:right-4 max-md:bottom-[calc(5.5rem+env(safe-area-inset-bottom))] max-md:z-20 max-md:size-14 max-md:rounded-full max-md:p-0 max-md:shadow-xl max-md:shadow-brand-600/40">
                <x-icon name="plus" class="size-4 max-md:size-6" /> <span class="max-md:sr-only">Nueva tarea</span>
            </button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filters --}}
    <div class="no-scrollbar -mx-4 mb-4 flex gap-2 overflow-x-auto px-4 pb-1">
        <a href="{{ route('board', array_filter(['mias' => $mine ? 1 : null])) }}" @class([
            'inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-2 text-sm font-semibold transition',
            'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' => ! $projectId,
            'bg-white text-zinc-600 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:ring-zinc-800' => $projectId,
        ])>Todos</a>
        @foreach ($projects as $project)
            <a href="{{ route('board', array_filter(['proyecto' => $project->id, 'mias' => $mine ? 1 : null])) }}" @class([
                'inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-2 text-sm font-semibold transition',
                'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' => $projectId === $project->id,
                'bg-white text-zinc-600 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:ring-zinc-800' => $projectId !== $project->id,
            ])>
                <x-project-dot :color="$project->color" /> {{ $project->name }}
            </a>
        @endforeach
        <span class="mx-1 w-px shrink-0 bg-zinc-300 dark:bg-zinc-700"></span>
        <a href="{{ route('board', array_filter(['proyecto' => $projectId, 'mias' => $mine ? null : 1])) }}" @class([
            'inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-2 text-sm font-semibold transition',
            'bg-brand-600 text-white' => $mine,
            'bg-white text-zinc-600 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:ring-zinc-800' => ! $mine,
        ])>
            <x-icon name="user" class="size-4" /> Mis tareas
        </a>
    </div>

    @if ($projects->isEmpty())
        <div class="card p-10 text-center">
            <x-icon name="folder" class="mx-auto size-8 text-zinc-400" />
            <p class="mt-2 font-semibold">Aún no hay proyectos</p>
            <p class="text-sm text-zinc-500">El administrador debe crear al menos un proyecto en Más → Proyectos.</p>
        </div>
    @else
        {{-- Columns: swipe sideways on phones, four across on desktop. --}}
        <div data-board class="no-scrollbar -mx-4 flex snap-x snap-mandatory gap-3 overflow-x-auto px-4 pb-4 lg:mx-0 lg:grid lg:grid-cols-4 lg:overflow-visible lg:px-0">
            @php
                $accents = ['todo' => 'bg-zinc-400', 'doing' => 'bg-brand-500', 'review' => 'bg-amber-500', 'done' => 'bg-emerald-500'];
            @endphp
            @foreach ($columns as $column)
                <section class="flex w-[82vw] max-w-sm shrink-0 snap-center flex-col rounded-3xl bg-zinc-200/60 p-2 sm:w-80 lg:w-auto lg:max-w-none dark:bg-zinc-900/60">
                    <header class="flex items-center gap-2 px-2 pt-1.5 pb-2.5">
                        <span class="size-2.5 rounded-full {{ $accents[$column['key']] }}"></span>
                        <h2 class="text-sm font-bold">{{ $column['label'] }}</h2>
                        <span data-count class="ml-auto rounded-full bg-white px-2 py-0.5 text-xs font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $column['tasks']->count() }}</span>
                    </header>
                    <div data-column="{{ $column['key'] }}" class="flex min-h-28 flex-1 flex-col gap-2">
                        @foreach ($column['tasks'] as $task)
                            <article data-task="{{ $task->id }}"
                                data-json="{{ json_encode(['id' => $task->id, 'title' => $task->title, 'description' => $task->description, 'project_id' => $task->project_id, 'user_id' => $task->user_id, 'status' => $task->status, 'due_date' => $task->due_date?->toDateString()]) }}"
                                tabindex="0" role="button"
                                class="cursor-grab rounded-2xl border border-zinc-200/70 bg-white p-3.5 shadow-[0_1px_2px_rgb(0_0_0/0.05)] transition select-none hover:border-brand-300 focus-visible:outline-2 focus-visible:outline-brand-500 active:cursor-grabbing dark:border-zinc-800 dark:bg-zinc-900">
                                <x-project-chip :project="$task->project" />
                                <p @class(['mt-2 text-[15px] leading-snug font-semibold', 'text-zinc-400 line-through dark:text-zinc-500' => $task->status === 'done'])>{{ $task->title }}</p>
                                @if ($task->description)
                                    <p class="mt-1 line-clamp-2 text-xs text-zinc-500">{{ $task->description }}</p>
                                @endif
                                <div class="mt-3 flex items-center justify-between gap-2">
                                    @if ($task->due_date && $task->status !== 'done')
                                        <span @class([
                                            'inline-flex items-center gap-1 text-xs font-semibold',
                                            'text-red-600 dark:text-red-400' => $task->due_date->isPast() && ! $task->due_date->isToday(),
                                            'text-zinc-500' => ! ($task->due_date->isPast() && ! $task->due_date->isToday()),
                                        ])>
                                            <x-icon name="calendar" class="size-3.5" /> {{ $task->due_date->translatedFormat('j M') }}
                                        </span>
                                    @else
                                        <span></span>
                                    @endif
                                    @if ($task->user)
                                        <x-avatar :user="$task->user" size="xs" />
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    {{-- Create / edit sheet. Opens as a bottom sheet on phones. --}}
    <dialog id="task-dialog" data-store="{{ route('tasks.store') }}" data-update="{{ url('/tareas') }}"
        class="m-0 mt-auto w-full max-w-none rounded-t-[2rem] bg-white p-0 text-zinc-900 shadow-2xl backdrop:bg-zinc-950/50 sm:m-auto sm:max-w-lg sm:rounded-[2rem] dark:bg-zinc-900 dark:text-zinc-100">
        <form method="POST" class="space-y-4 p-5 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:p-6">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="mx-auto -mt-1 mb-1 h-1.5 w-10 rounded-full bg-zinc-200 sm:hidden dark:bg-zinc-700"></div>
            <div class="flex items-center justify-between">
                <h2 data-dialog-title class="text-lg font-extrabold">Nueva tarea</h2>
                <button type="button" data-close class="btn-ghost size-9 p-0" aria-label="Cerrar"><x-icon name="x" /></button>
            </div>
            <div>
                <label for="t-title" class="label">Título</label>
                <input id="t-title" name="title" class="input" required maxlength="255" placeholder="¿Qué hay que hacer?">
            </div>
            <div>
                <label for="t-description" class="label">Detalle <span class="font-normal text-zinc-400">(opcional)</span></label>
                <textarea id="t-description" name="description" rows="2" class="input" maxlength="2000"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2 sm:col-span-1">
                    <label for="t-project" class="label">Proyecto</label>
                    <select id="t-project" name="project_id" class="input" required>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected($projectId === $project->id)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label for="t-user" class="label">Responsable</label>
                    <select id="t-user" name="user_id" class="input">
                        <option value="">Sin asignar</option>
                        @foreach ($people as $person)
                            <option value="{{ $person->id }}" @selected($person->is(auth()->user()))>{{ $person->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="t-status" class="label">Columna</label>
                    <select id="t-status" name="status" class="input">
                        @foreach (\App\Models\Task::STATUSES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="t-due" class="label">Fecha límite</label>
                    <input id="t-due" name="due_date" type="date" class="input">
                </div>
            </div>
            <div class="flex items-center gap-2 pt-2">
                <button type="submit" form="task-delete" data-delete class="btn-ghost hidden text-red-600 dark:text-red-400">
                    <x-icon name="trash" class="size-4" /> Eliminar
                </button>
                <button type="button" data-close class="btn-secondary ml-auto">Cancelar</button>
                <button class="btn-primary">Guardar</button>
            </div>
        </form>
        <form id="task-delete" method="POST" data-confirm="¿Eliminar esta tarea?">
            @csrf
            @method('DELETE')
        </form>
    </dialog>
</x-app-layout>
