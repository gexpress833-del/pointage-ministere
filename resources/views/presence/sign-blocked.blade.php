<!DOCTYPE html>
<html lang="fr" class="m-0 p-0 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1d4ed8">
    <title>{{ $titre }} – {{ config('app.name') }}</title>
    <x-vite-tailwind />
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body { margin: 0; min-height: 100dvh; overflow-x: hidden; }
        a, button { touch-action: manipulation; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen min-h-dvh flex flex-col antialiased overflow-x-hidden">

    @php
        $u = auth()->user();
        $retourUrl = ($u && ($u->isAdministrateur() || $u->isCoordinateur() || $u->isChefBureau()))
            ? url('/admin')
            : route('presence.dashboard');
    @endphp
    <header class="bg-blue-700 text-white px-4 py-3 flex items-center gap-3 shadow-md flex-shrink-0" style="padding-top: max(0.75rem, env(safe-area-inset-top, 0px));">
        <a href="{{ $retourUrl }}" class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] text-white opacity-75 hover:opacity-100 transition -ml-1 rounded-full" title="Retour">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-base font-semibold leading-tight">Signature de présence</h1>
            <p class="text-xs text-blue-200">{{ config('app.name') }}</p>
        </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center p-6 gap-4" style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom, 0px));">
        <div class="w-full max-w-lg">
            @include('presence.partials.annonces', ['annoncesVariant' => 'plain'])
        </div>
        <div class="max-w-sm w-full text-center">

            @if($raison === 'photo')
            <div class="w-20 h-20 mx-auto bg-orange-100 rounded-full flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </div>
            @elseif($raison === 'session')
            <div class="w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
            @elseif($raison === 'role')
            <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            @else
            <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
            @endif

            <h2 class="text-xl font-bold text-slate-800 mb-3">{{ $titre }}</h2>
            <p class="text-slate-500 text-sm leading-relaxed mb-8">{{ $message }}</p>

            <div class="flex flex-col gap-3">
                @if($raison === 'signe')
                <a href="{{ route('presence.historique') }}"
                   class="flex items-center justify-center min-h-[48px] w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold rounded-xl transition text-sm touch-manipulation">
                    Voir l'historique
                </a>
                @endif
                <a href="{{ $retourUrl }}"
                   class="flex items-center justify-center min-h-[48px] w-full py-3.5 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 font-semibold rounded-xl transition text-sm touch-manipulation">
                    @if(($u && ($u->isAdministrateur() || $u->isCoordinateur() || $u->isChefBureau())))
                        Retour au tableau de bord
                    @else
                        Retour au tableau de bord
                    @endif
                </a>
            </div>

        </div>
    </main>

</body>
</html>
