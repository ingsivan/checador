@php($editing = $project->exists)

<x-app-layout :title="$editing ? 'Editar proyecto' : 'Nuevo proyecto'" :back="route('admin.projects.index')">
    <div class="mx-auto max-w-xl">
        <h1 class="mb-5 text-[26px] font-extrabold tracking-tight">{{ $editing ? $project->name : 'Nuevo proyecto' }}</h1>

        <form method="POST" action="{{ $editing ? route('admin.projects.update', $project) : route('admin.projects.store') }}" class="card space-y-5 p-6">
            @csrf
            @if ($editing) @method('PUT') @endif

            <div>
                <label for="name" class="label">Nombre</label>
                <input id="name" name="name" value="{{ old('name', $project->name) }}" class="input" required>
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="client" class="label">Cliente <span class="font-normal text-zinc-400">(opcional)</span></label>
                <input id="client" name="client" value="{{ old('client', $project->client) }}" class="input">
            </div>
            <div>
                <label for="description" class="label">Descripción <span class="font-normal text-zinc-400">(opcional)</span></label>
                <textarea id="description" name="description" rows="3" class="input">{{ old('description', $project->description) }}</textarea>
            </div>
            <fieldset>
                <legend class="label">Color</legend>
                <div class="flex flex-wrap gap-3">
                    @foreach (\App\Models\Project::COLORS as $key => $label)
                        <label class="cursor-pointer" title="{{ $label }}">
                            <input type="radio" name="color" value="{{ $key }}" class="peer sr-only" @checked(old('color', $project->color) === $key)>
                            <span class="grid size-10 place-items-center rounded-full ring-offset-2 transition peer-checked:ring-2 peer-checked:ring-zinc-900 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 dark:ring-offset-zinc-900 dark:peer-checked:ring-white">
                                <x-project-dot :color="$key" class="size-8" />
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
            <label class="flex items-center gap-2.5 text-sm">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="size-4 accent-brand-600" @checked(old('is_active', $project->is_active))>
                Activo (si lo desmarcas se archiva y sale del tablero)
            </label>
            <div class="flex justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
                <a href="{{ route('admin.projects.index') }}" class="btn-secondary">Cancelar</a>
                <button class="btn-primary">{{ $editing ? 'Guardar' : 'Crear proyecto' }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
