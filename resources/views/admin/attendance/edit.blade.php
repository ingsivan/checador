<x-app-layout :title="'Registros de '.$employee->name" :back="route('admin.reports', ['semana' => $week->start->toDateString()])">
    <div class="mb-5 flex items-center gap-4">
        <x-avatar :user="$employee" size="lg" />
        <div class="min-w-0">
            <h1 class="truncate text-[26px] font-extrabold tracking-tight">{{ $employee->name }}</h1>
            <p class="text-sm text-zinc-500">Corrige las horas si alguien olvidó registrar o se equivocó. Deja vacío para borrar.</p>
        </div>
    </div>

    <x-week-nav :week="$week" route="admin.attendance.edit" class="mb-4" :params="['user' => $employee->id]" />

    @if ($errors->any())
        <div role="alert" class="mb-4 rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:bg-red-500/10 dark:text-red-300">{{ $errors->first() }}</div>
    @endif

    <div class="space-y-3">
        @foreach ($days as ['date' => $date, 'record' => $record])
            <form method="POST" action="{{ route('admin.attendance.update', [$employee, $date->toDateString()]) }}" class="card p-4">
                @csrf @method('PUT')
                <div class="mb-3 flex items-center justify-between gap-2">
                    <p class="font-bold">{{ ucfirst($date->translatedFormat('l j \d\e F')) }}</p>
                    <div class="flex gap-1.5">
                        @if ($record?->lateMinutes())
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-500/15 dark:text-amber-300">{{ $record->lateMinutes() }} min tarde</span>
                        @endif
                        @if ($record && $record->check_in_at && ! $record->check_out_at && ! $date->isToday())
                            <span class="rounded-full bg-zinc-200 px-2 py-0.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">Sin salida</span>
                        @endif
                        @unless ($record)
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-semibold text-zinc-500 dark:bg-zinc-800">Sin registro</span>
                        @endunless
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach (\App\Models\Attendance::STEPS as $field => $label)
                        <label class="block">
                            <span class="mb-1 block text-xs font-semibold text-zinc-500">{{ $label }}</span>
                            <input type="time" name="{{ $field }}" value="{{ $record?->{$field}?->format('H:i') }}" class="input py-2.5">
                        </label>
                    @endforeach
                </div>
                <div class="mt-3 flex justify-end">
                    <button class="btn-secondary py-2">Guardar día</button>
                </div>
            </form>
        @endforeach
    </div>
</x-app-layout>
