<x-app-layout title="Iniciar sesión">
    <div class="mx-auto mt-6 max-w-sm sm:mt-16">
        <div class="mb-8 text-center">
            <span class="mx-auto mb-4 grid size-12 place-items-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2M9 2h6"/></svg>
            </span>
            <h1 class="text-2xl font-bold tracking-tight">{{ config('app.name') }}</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Entra para registrar tu día.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="card space-y-5 p-6">
            @csrf
            <div>
                <label for="email" class="label">Correo</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="input" autocomplete="username" required autofocus>
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="label">Contraseña</label>
                <input id="password" name="password" type="password" class="input" autocomplete="current-password" required>
                @error('password') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                <input type="checkbox" name="remember" class="size-4 rounded border-zinc-300 accent-brand-600">
                Mantener la sesión abierta
            </label>
            <button class="btn-primary w-full py-3">Entrar</button>
        </form>
    </div>
</x-app-layout>
