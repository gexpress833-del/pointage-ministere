<!DOCTYPE html>
<html lang="fr" class="m-0 p-0 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <title>Historique des présences – {{ config('app.name') }}</title>
    <x-vite-tailwind />
    @include('presence.partials.portal-theme')
</head>
<body class="flex flex-col min-h-screen min-h-dvh m-0 antialiased overflow-x-hidden">

    <x-presence.agent-header :user="$user" current="historique" />

    <main class="flex-1 max-w-4xl mx-auto w-full min-w-0 px-3 sm:px-6 pt-2 {{ ($sessionOuverte || ($besoinPointerDepart ?? false)) ? 'pb-32' : 'pb-8' }} space-y-4 safe-pb">

        @include('presence.partials.annonces')

        {{-- Résumé + filtres regroupés (moins de blocs empilés) --}}
        <div class="glass rounded-2xl p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-[0.18em]">Historique</h2>
                    <p class="text-white text-lg font-bold mt-0.5">{{ \Carbon\Carbon::parse($moisSelectionne . '-01')->translatedFormat('F Y') }}</p>
                </div>
                <div class="grid grid-cols-3 gap-2 sm:max-w-xs w-full sm:w-auto">
                    <div class="rounded-xl min-h-[70px] p-2 bg-green-500/12 border border-green-400/20 ring-1 ring-green-300/10 text-center flex flex-col items-center justify-center">
                        <p class="text-lg font-extrabold text-green-300 leading-none">{{ $statsMois['presents'] }}</p>
                        <p class="text-[9px] sm:text-[10px] text-green-100/95 font-semibold mt-1 leading-tight">Présents</p>
                    </div>
                    <div class="rounded-xl min-h-[70px] p-2 bg-amber-500/12 border border-amber-400/20 ring-1 ring-amber-300/10 text-center flex flex-col items-center justify-center">
                        <p class="text-lg font-extrabold text-amber-300 leading-none">{{ $statsMois['retards'] }}</p>
                        <p class="text-[9px] sm:text-[10px] text-amber-100/95 font-semibold mt-1 leading-tight">Retards</p>
                    </div>
                    <div class="rounded-xl min-h-[70px] p-2 bg-red-500/12 border border-red-400/20 ring-1 ring-red-300/10 text-center flex flex-col items-center justify-center">
                        <p class="text-lg font-extrabold text-red-300 leading-none">{{ $statsMois['absences'] }}</p>
                        <p class="text-[9px] sm:text-[10px] text-red-100/95 font-semibold mt-1 leading-tight">Absences</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 pt-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Filtres</p>
                <form method="GET" action="{{ route('presence.historique') }}" class="flex flex-col sm:flex-row gap-4">
                    <label class="flex-1 flex flex-col gap-1.5 min-w-0">
                        <span class="text-[11px] text-slate-400">Mois</span>
                        <select name="month" onchange="this.form.submit()" class="portal-select">
                            @foreach($moisDisponibles as $m)
                                <option value="{{ $m }}" {{ $moisSelectionne === $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($m . '-01')->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label class="flex-1 flex flex-col gap-1.5 min-w-0">
                        <span class="text-[11px] text-slate-400">Statut</span>
                        <select name="statut" onchange="this.form.submit()" class="portal-select">
                            <option value="" {{ $statutFiltre === '' ? 'selected' : '' }}>Tous les enregistrements</option>
                            <option value="present" {{ $statutFiltre === 'present' ? 'selected' : '' }}>À l'heure uniquement</option>
                            <option value="retard" {{ $statutFiltre === 'retard' ? 'selected' : '' }}>Retards uniquement</option>
                        </select>
                    </label>
                </form>
            </div>
        </div>

        <div class="space-y-2">
            @forelse($presences as $presence)
            @php
                $date       = $presence->session->date;
                $jour       = $date->translatedFormat('l');
                $dateStr    = $date->translatedFormat('d F Y');
                $estPresent = $presence->statut === \App\Models\Presence::STATUT_PRESENT;
                $estRetard  = $presence->statut === \App\Models\Presence::STATUT_RETARD;
                $colors = $estPresent
                    ? ['bg-green-500/10 border-green-400/20', 'bg-green-500/15 text-green-300']
                    : ($estRetard
                        ? ['bg-amber-500/10 border-amber-400/20', 'bg-amber-500/15 text-amber-300']
                        : ['bg-red-500/10 border-red-400/20', 'bg-red-500/15 text-red-300']);
            @endphp
            <div class="glass rounded-2xl border {{ $colors[0] }} p-4 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
                <div class="w-10 h-10 rounded-full {{ $colors[1] }} flex items-center justify-center flex-shrink-0">
                    @if($estPresent)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    @elseif($estRetard)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white text-sm capitalize">{{ $jour }}</p>
                    <p class="text-xs text-slate-400">{{ $dateStr }}</p>
                </div>
                </div>

                <div class="sm:text-right flex-shrink-0 pl-[3.25rem] sm:pl-0 border-t border-white/5 sm:border-0 pt-2 sm:pt-0">
                    <p class="font-mono text-xs text-slate-300 leading-snug">
                        <span class="text-slate-400">Arr.</span>
                        <span class="font-semibold text-slate-100">{{ \Carbon\Carbon::parse($presence->heure_arrivee)->format('H:i') }}</span>
                        @if($presence->heure_depart)
                            <span class="text-slate-500 mx-0.5">·</span>
                            <span class="text-slate-400">Dép.</span>
                            <span class="font-semibold text-slate-100">{{ \Carbon\Carbon::parse($presence->heure_depart)->format('H:i') }}</span>
                        @else
                            <span class="text-slate-500 mx-0.5">·</span>
                            <span class="text-amber-400/90">Départ —</span>
                        @endif
                    </p>
                    <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full {{ $colors[1] }} mt-0.5">
                        {{ $estPresent ? 'À l\'heure' : ($estRetard ? 'Retard' : 'Absent') }}
                    </span>
                </div>
            </div>
            @empty
            <div class="glass rounded-2xl p-8 text-center">
                <p class="text-slate-200 font-medium text-sm">Aucune ligne ne correspond à ces filtres</p>
                <p class="text-slate-400 text-xs mt-2">Changez le mois ou le statut, ou revenez au <a href="{{ route('presence.dashboard') }}" class="text-blue-400 hover:underline">tableau de bord</a>.</p>
            </div>
            @endforelse
        </div>

        @if($sessionOuverte)
        <div class="fixed bottom-0 left-0 right-0 p-4 pt-2 bg-gradient-to-t from-[#0b1120] via-[#0b1120]/95 to-transparent z-30 max-w-4xl mx-auto w-full px-4 sm:px-6" style="padding-bottom: max(1rem, env(safe-area-inset-bottom, 1rem));">
            <a href="{{ route('presence.sign') }}"
                class="flex items-center justify-center gap-2 w-full py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-semibold rounded-2xl shadow-lg transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                </svg>
                Signer l'arrivée (session ouverte)
            </a>
        </div>
        @elseif($besoinPointerDepart ?? false)
        <div class="fixed bottom-0 left-0 right-0 p-4 pt-2 bg-gradient-to-t from-[#0b1120] via-[#0b1120]/95 to-transparent z-30 max-w-4xl mx-auto w-full px-4 sm:px-6" style="padding-bottom: max(1rem, env(safe-area-inset-bottom, 1rem));">
            <a href="{{ route('presence.sign') }}"
                class="flex items-center justify-center gap-2 w-full py-3.5 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-500 hover:to-amber-600 text-white font-semibold rounded-2xl shadow-lg transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                Pointer mon départ
            </a>
        </div>
        @endif

    </main>

</body>
</html>
