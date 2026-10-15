<x-app-layout title="Iniciar sesión">
    <div class="mx-auto flex min-h-[calc(100dvh-6rem)] max-w-sm flex-col justify-center">
        <div class="mb-8 text-center">
            <x-logo class="mx-auto size-20 drop-shadow-xl" />
            <h1 class="mt-5 text-3xl font-extrabold tracking-tight">Checador</h1>
            <p class="mt-1.5 text-zinc-500 dark:text-zinc-400">Llegadas, comida y proyectos del equipo.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="card space-y-4 p-6">
            @csrf
            <div>
                <label for="email" class="label">Correo</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="input" autocomplete="username" inputmode="email" placeholder="tu@empresa.com" required autofocus>
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="label">Contraseña</label>
                <input id="password" name="password" type="password" class="input" autocomplete="current-password" required>
                @error('password') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2.5 text-sm text-zinc-600 dark:text-zinc-400">
                <input type="checkbox" name="remember" class="size-4 rounded accent-brand-600" checked>
                Mantener la sesión abierta
            </label>
            <button class="btn-primary w-full py-3.5 text-base">Entrar</button>
        </form>

        <a href="{{ route('help') }}" class="mt-6 inline-flex items-center justify-center gap-1.5 text-sm font-semibold text-brand-600 dark:text-brand-400">
            <x-icon name="help" class="size-4" /> ¿Cómo funciona?
        </a>
    </div>
</x-app-layout>
