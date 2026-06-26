<!DOCTYPE html>
<html lang="fr" class="m-0 p-0 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <title>Tableau de bord – {{ config('app.name') }}</title>
    <x-vite-tailwind />
    @include('presence.partials.portal-theme')
</head>
<body class="flex flex-col min-h-screen min-h-dvh m-0 antialiased overflow-x-hidden">

    <x-presence.agent-header :user="$user" current="dashboard" />

    <main class="flex-1 max-w-4xl mx-auto w-full min-w-0 px-3 sm:px-6 pt-2 pb-8 space-y-4 safe-pb">

        @include('presence.partials.annonces')

        <div class="glass rounded-2xl p-4 sm:p-5">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-[0.18em]">Aperçu du mois</h2>
                    <p class="text-white text-base font-bold mt-0.5">
                        {{ \Carbon\Carbon::parse($moisCourant . '-01')->translatedFormat('F Y') }}
                    </p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full bg-blue-500/15 text-blue-200 px-2.5 py-1 text-[11px] font-medium whitespace-nowrap">
                    Portail agent
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2 sm:gap-3">
                <div class="rounded-xl min-h-[76px] p-2 sm:p-3 bg-green-500/12 border border-green-400/20 ring-1 ring-green-300/10 text-center flex flex-col items-center justify-center">
                    <p class="text-[22px] sm:text-2xl font-extrabold text-green-300 leading-none">{{ $statsMois['presents'] }}</p>
                    <p class="text-[9px] sm:text-xs text-green-100/95 font-semibold mt-1 leading-tight">Présents</p>
                </div>
                <div class="rounded-xl min-h-[76px] p-2 sm:p-3 bg-amber-500/12 border border-amber-400/20 ring-1 ring-amber-300/10 text-center flex flex-col items-center justify-center">
                    <p class="text-[22px] sm:text-2xl font-extrabold text-amber-300 leading-none">{{ $statsMois['retards'] }}</p>
                    <p class="text-[9px] sm:text-xs text-amber-100/95 font-semibold mt-1 leading-tight">Retards</p>
                </div>
                <div class="rounded-xl min-h-[76px] p-2 sm:p-3 bg-red-500/12 border border-red-400/20 ring-1 ring-red-300/10 text-center flex flex-col items-center justify-center">
                    <p class="text-[22px] sm:text-2xl font-extrabold text-red-300 leading-none">{{ $statsMois['absences'] }}</p>
                    <p class="text-[9px] sm:text-xs text-red-100/95 font-semibold mt-1 leading-tight">Absences</p>
                </div>
            </div>
            @php
                $total = $statsMois['presents'] + $statsMois['retards'] + $statsMois['absences'];
                $taux  = $total > 0 ? round(($statsMois['presents'] + $statsMois['retards']) / $total * 100) : 0;
                $color = $taux >= 80 ? '#22c55e' : ($taux >= 50 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="mt-4">
                <div class="flex justify-between text-[11px] text-slate-400 mb-1">
                    <span>Taux de présence</span>
                    <span class="font-semibold text-white">{{ $taux }}%</span>
                </div>
                <div class="w-full rounded-full h-2 bg-slate-800 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-700" style="width: {{ $taux }}%; background: {{ $color }};"></div>
                </div>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <a href="{{ route('presence.sign') }}"
               class="glass rounded-2xl p-4 flex flex-col items-start gap-2 border transition group {{ $sessionOuverte ? 'border-blue-500/20 hover:border-blue-400/40' : 'border-slate-600/30 opacity-60 pointer-events-none' }}">
                <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-300 group-hover:bg-blue-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" /></svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Arrivée</p>
                    <p class="text-slate-400 text-xs mt-0.5">{{ $sessionOuverte ? 'Signer l\'entrée (session ouverte)' : 'Disponible quand la session du jour est ouverte' }}</p>
                </div>
            </a>
            <a href="{{ route('presence.sign') }}"
               class="glass rounded-2xl p-4 flex flex-col items-start gap-2 border transition group {{ $besoinPointerDepart ? 'border-amber-500/25 hover:border-amber-400/45' : 'border-slate-600/30 opacity-60 pointer-events-none' }}">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-200 group-hover:bg-amber-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Départ</p>
                    <p class="text-slate-400 text-xs mt-0.5">{{ $besoinPointerDepart ? 'Enregistrer votre heure de sortie' : 'Après l\'arrivée, quand le départ n\'est pas encore pointé' }}</p>
                </div>
            </a>
            <a href="{{ route('presence.historique') }}"
               class="glass rounded-2xl p-4 flex flex-col items-start gap-2 border border-slate-500/20 hover:border-slate-400/35 transition group">
                <div class="w-10 h-10 rounded-xl bg-slate-500/20 flex items-center justify-center text-slate-300 group-hover:bg-slate-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Historique</p>
                    <p class="text-slate-400 text-xs mt-0.5">Liste filtrable par mois et statut</p>
                </div>
            </a>
        </div>

        @if($sessionOuverte)
        <a href="{{ route('presence.sign') }}"
            class="flex items-center justify-center gap-2 w-full py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-semibold rounded-2xl shadow-lg transition text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
            Session ouverte — signer l'arrivée
        </a>
        @elseif($besoinPointerDepart)
        <a href="{{ route('presence.sign') }}"
            class="flex items-center justify-center gap-2 w-full py-3.5 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-500 hover:to-amber-600 text-white font-semibold rounded-2xl shadow-lg transition text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
            Pointer mon départ
        </a>
        @endif
    </main>
</body>
</html>
