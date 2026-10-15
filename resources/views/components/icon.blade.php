@props(['name'])

@php($sized = str_contains($attributes->get('class', ''), 'size-'))

{{-- Line icons (Lucide style, 24px grid). --}}
<svg {{ $attributes->class(['size-5' => ! $sized]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('home') <path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z"/> @break
        @case('users') <circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M16 3.5a4 4 0 0 1 0 9M22 21a7 7 0 0 0-4-6.3"/> @break
        @case('board') <rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 3v18M15 3v18"/> @break
        @case('calendar') <rect x="3" y="4" width="18" height="17" rx="3"/><path d="M8 2v4M16 2v4M3 10h18"/> @break
        @case('menu') <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/> @break
        @case('login') <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/> @break
        @case('logout') <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/> @break
        @case('utensils') <path d="M3 2v7a3 3 0 0 0 3 3v10M9 2v7a3 3 0 0 1-3 3M6 2v5M18 22V2c-2.5 1-4 4-4 8v3h4"/> @break
        @case('coffee-back') <path d="M17 8h1a4 4 0 0 1 0 8h-1M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4zM6 2v3M10 2v3M14 2v3"/> @break
        @case('clock') <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/> @break
        @case('check') <path d="M20 6 9 17l-5-5"/> @break
        @case('plus') <path d="M12 5v14M5 12h14"/> @break
        @case('x') <path d="M18 6 6 18M6 6l12 12"/> @break
        @case('chart') <path d="M3 3v18h18M8 17v-5M13 17V8M18 17v-9"/> @break
        @case('folder') <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/> @break
        @case('help') <circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6v.6M12 17h.01"/> @break
        @case('chevron-left') <path d="m15 18-6-6 6-6"/> @break
        @case('chevron-right') <path d="m9 18 6-6-6-6"/> @break
        @case('alert') <path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/> @break
        @case('download') <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/> @break
        @case('trophy') <path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0zM17 5h3v2a3 3 0 0 1-3 3M7 5H4v2a3 3 0 0 0 3 3"/> @break
        @case('smartphone') <rect x="6" y="2" width="12" height="20" rx="3"/><path d="M11 18h2"/> @break
        @case('flag') <path d="M4 22V4M4 4h13l-2 4 2 4H4"/> @break
        @case('search') <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/> @break
        @case('user') <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/> @break
        @case('briefcase') <rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"/> @break
        @case('edit') <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/> @break
        @case('trash') <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/> @break
    @endswitch
</svg>
