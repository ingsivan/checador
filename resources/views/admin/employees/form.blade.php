@php($editing = $employee->exists)

<x-app-layout :title="$editing ? 'Editar empleado' : 'Agregar empleado'" :back="route('admin.employees.index')">
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.employees.index') }}" class="text-sm font-semibold text-zinc-500 hover:text-zinc-900 max-md:hidden dark:hover:text-white">← Empleados</a>
        <h1 class="mt-1 mb-5 text-[26px] font-extrabold tracking-tight">{{ $editing ? 'Editar a '.$employee->name : 'Agregar empleado' }}</h1>

        <form method="POST" action="{{ $editing ? route('admin.employees.update', $employee) : route('admin.employees.store') }}" class="card space-y-5 p-6">
            @csrf
            @if ($editing) @method('PUT') @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="label">Nombre completo</label>
                    <input id="name" name="name" value="{{ old('name', $employee->name) }}" class="input" required>
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="label">Correo</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $employee->email) }}" class="input" required>
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="label">Teléfono <span class="font-normal text-zinc-400">(opcional)</span></label>
                    <input id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" class="input">
                    @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="position" class="label">Puesto</label>
                    <input id="position" name="position" value="{{ old('position', $employee->position) }}" placeholder="Backend, Frontend, QA…" class="input">
                    @error('position') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="role" class="label">Rol</label>
                    <select id="role" name="role" class="input" @disabled($employee->is(auth()->user()))>
                        <option value="developer" @selected(old('role', $employee->role) === 'developer')>Developer</option>
                        <option value="admin" @selected(old('role', $employee->role) === 'admin')>Administrador</option>
                    </select>
                    @if ($employee->is(auth()->user()))
                        <input type="hidden" name="role" value="admin">
                    @endif
                    @error('role') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="work_start" class="label">Hora de entrada</label>
                    <input id="work_start" name="work_start" type="time" value="{{ old('work_start', substr($employee->work_start, 0, 5)) }}" class="input" required>
                    @error('work_start') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="work_end" class="label">Hora de salida</label>
                    <input id="work_end" name="work_end" type="time" value="{{ old('work_end', substr($employee->work_end, 0, 5)) }}" class="input" required>
                    @error('work_end') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="label">{{ $editing ? 'Nueva contraseña' : 'Contraseña' }}</label>
                    <input id="password" name="password" type="password" class="input" autocomplete="new-password" @required(! $editing)>
                    @if ($editing) <p class="mt-1.5 text-xs text-zinc-500">Déjala vacía para no cambiarla.</p> @endif
                    @error('password') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="label">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="input" autocomplete="new-password" @required(! $editing)>
                </div>
            </div>

            @unless ($employee->is(auth()->user()))
                <label class="flex items-center gap-2 text-sm">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="size-4 accent-brand-600" @checked(old('is_active', $employee->is_active))>
                    Activo (puede entrar y registrar su día)
                </label>
            @endunless

            <div class="flex justify-end gap-3 border-t border-zinc-200 pt-5 dark:border-zinc-800">
                <a href="{{ route('admin.employees.index') }}" class="btn-secondary">Cancelar</a>
                <button class="btn-primary">{{ $editing ? 'Guardar cambios' : 'Agregar' }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
