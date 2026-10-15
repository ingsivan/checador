<x-app-layout title="Inicio">
    <div class="space-y-6">
        <div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ ucfirst(now()->translatedFormat('l j \d\e F')) }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Hola, {{ str($user->name)->before(' ') }}</h1>
        </div>

        <section class="card p-6">
            <div class="flex items-start gap-4">
                <span class="grid size-14 shrink-0 place-items-center rounded-full bg-brand-100 text-lg font-semibold text-brand-700 dark:bg-brand-600/20 dark:text-brand-100">
                    {{ $user->initials() }}
                </span>
                <div class="min-w-0">
                    <p class="font-semibold">{{ $user->name }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->position ?? 'Sin puesto' }} · {{ $user->email }}</p>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                        Horario: {{ substr($user->work_start, 0, 5) }} a {{ substr($user->work_end, 0, 5) }}
                    </p>
                </div>
            </div>
        </section>

        <section class="card border-dashed p-6 text-center">
            <p class="font-semibold">El checador llega en el siguiente paso</p>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Aquí aparecerán los botones de Llegada, Salgo a comer, Regreso y Salida, con el timer de comida.</p>
        </section>
    </div>
</x-app-layout>
