@props(['title' => null, 'back' => null, 'wide' => false])

@php
    $user = auth()->user();
    $tabs = [
        ['route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'home', 'label' => 'Inicio'],
        ['route' => 'team', 'match' => 'team', 'icon' => 'users', 'label' => 'Equipo'],
        ['route' => 'board', 'match' => 'board', 'icon' => 'board', 'label' => 'Tablero'],
        ['route' => 'history', 'match' => 'history', 'icon' => 'calendar', 'label' => 'Historial'],
        ['route' => 'more', 'match' => ['more', 'admin.*', 'help'], 'icon' => 'menu', 'label' => 'Más'],
    ];
    $adminLinks = [
        ['route' => 'admin.reports', 'match' => 'admin.reports*', 'label' => 'Reportes'],
        ['route' => 'admin.employees.index', 'match' => 'admin.employees.*', 'label' => 'Empleados'],
        ['route' => 'admin.projects.index', 'match' => 'admin.projects.*', 'label' => 'Proyectos'],
    ];
@endphp

<!DOCTYPE html>
<html lang="es-MX" class="h-full [color-scheme:light_dark]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f4f4f5" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#09090b" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Checador">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-zinc-100 font-sans text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    @auth
        {{-- Top bar: compact app bar on phones, full navigation on desktop. --}}
        <header class="sticky top-0 z-30 border-b border-zinc-200/70 bg-zinc-100/80 pt-[env(safe-area-inset-top)] backdrop-blur-xl dark:border-zinc-800/70 dark:bg-zinc-950/80">
            <div @class(['mx-auto flex h-14 items-center gap-3 px-4', 'max-w-6xl' => $wide, 'max-w-5xl' => ! $wide])>
                @if ($back)
                    <a href="{{ $back }}" class="-ml-2 grid size-9 place-items-center rounded-full text-zinc-600 hover:bg-zinc-200 md:hidden dark:text-zinc-300 dark:hover:bg-zinc-800" aria-label="Regresar">
                        <x-icon name="chevron-left" class="size-6" />
                    </a>
                @endif
                <a href="{{ route('dashboard') }}" @class(['flex items-center gap-2.5', 'max-md:hidden' => $back])>
                    <x-logo class="size-8" />
                    <span class="text-[17px] font-extrabold tracking-tight max-md:hidden">Checador</span>
                </a>
                @if ($title)
                    <span class="truncate text-[17px] font-bold md:hidden">{{ $title }}</span>
                @endif

                <nav class="ml-4 hidden items-center gap-1 text-sm font-medium md:flex">
                    @foreach (array_slice($tabs, 0, 4) as $tab)
                        <x-nav-link :href="route($tab['route'])" :active="request()->routeIs($tab['match'])">{{ $tab['label'] }}</x-nav-link>
                    @endforeach
                    @if ($user->isAdmin())
                        <span class="mx-2 h-5 w-px bg-zinc-300 dark:bg-zinc-700"></span>
                        @foreach ($adminLinks as $link)
                            <x-nav-link :href="route($link['route'])" :active="request()->routeIs($link['match'])">{{ $link['label'] }}</x-nav-link>
                        @endforeach
                    @endif
                </nav>

                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('help') }}" class="hidden size-9 place-items-center rounded-full text-zinc-500 hover:bg-zinc-200 md:grid dark:hover:bg-zinc-800" title="Cómo funciona">
                        <x-icon name="help" />
                    </a>
                    <a href="{{ route('more') }}" class="rounded-full">
                        <x-avatar :user="$user" size="sm" />
                    </a>
                </div>
            </div>
        </header>
    @endauth

    <main @class([
        'mx-auto px-4 pt-5 md:pt-8',
        'max-w-6xl' => $wide,
        'max-w-5xl' => ! $wide,
        'pb-[calc(6rem+env(safe-area-inset-bottom))] md:pb-12' => auth()->check(),
        'pb-12' => ! auth()->check(),
    ])>
        @if (session('status'))
            <div data-toast role="status" class="mb-5 flex items-start gap-3 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-medium text-white shadow-lg shadow-emerald-600/20 transition duration-500">
                <x-icon name="check" class="mt-0.5 size-4 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="mb-5 flex items-start gap-3 rounded-2xl bg-red-600 px-4 py-3 text-sm font-medium text-white shadow-lg shadow-red-600/20">
                <x-icon name="alert" class="mt-0.5 size-4 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{ $slot }}
    </main>

    @auth
        {{-- Bottom tab bar (phones only), like a native app. --}}
        <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-zinc-200/70 bg-white/85 pb-[env(safe-area-inset-bottom)] backdrop-blur-xl md:hidden dark:border-zinc-800/70 dark:bg-zinc-900/85" aria-label="Principal">
            <div class="mx-auto grid max-w-md grid-cols-5">
                @foreach ($tabs as $tab)
                    @php($active = request()->routeIs(...(array) $tab['match']))
                    <a href="{{ route($tab['route']) }}" @class([
                        'flex flex-col items-center gap-1 pt-2.5 pb-2 text-[11px] font-semibold transition active:scale-95',
                        'text-brand-600 dark:text-brand-400' => $active,
                        'text-zinc-500 dark:text-zinc-400' => ! $active,
                    ]) @if ($active) aria-current="page" @endif>
                        <span @class(['grid h-7 w-12 place-items-center rounded-full transition', 'bg-brand-100 dark:bg-brand-500/20' => $active])>
                            <x-icon :name="$tab['icon']" class="size-[22px]" />
                        </span>
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
        </nav>
    @endauth
</body>
</html>
