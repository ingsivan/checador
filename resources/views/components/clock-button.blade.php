@props(['step', 'variant' => 'primary'])

@php
    $config = [
        'check_in_at' => ['Registrar llegada', 'login', 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/30', null],
        'lunch_out_at' => ['Salgo a comer', 'utensils', 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/30', null],
        'lunch_in_at' => ['Ya regresé de comer', 'coffee-back', 'bg-brand-600 hover:bg-brand-700 shadow-brand-600/30', null],
        'check_out_at' => ['Registrar salida', 'logout', 'bg-zinc-900 hover:bg-zinc-800 shadow-zinc-900/20 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200', '¿Terminar tu jornada? Ya no podrás registrar nada más hoy.'],
    ];
    [$label, $icon, $colors, $confirm] = $config[$step];
@endphp

<form method="POST" action="{{ route('clock') }}" data-once @if ($confirm) data-confirm="{{ $confirm }}" @endif>
    @csrf
    <input type="hidden" name="step" value="{{ $step }}">
    @if ($variant === 'primary')
        <button class="flex w-full items-center justify-center gap-3 rounded-2xl px-5 py-4 text-base font-bold text-white shadow-lg transition active:scale-[0.98] disabled:opacity-60 {{ $colors }}">
            <x-icon :name="$icon" class="size-6" />
            {{ $label }}
        </button>
    @else
        <button class="btn-secondary w-full py-3.5">
            <x-icon :name="$icon" class="size-5" />
            {{ $label }}
        </button>
    @endif
</form>
