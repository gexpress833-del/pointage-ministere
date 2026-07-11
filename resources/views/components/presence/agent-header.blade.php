@props([
    'user',
    'current' => 'dashboard',
])

@php
    $adminHome = $user->isAdministrateur() || $user->isSecretaire() || $user->isCoordinateur() || $user->isChefBureau();
    $panelUrl = match ($user->role) {
        'administrateur' => url('/admin'),
        'secretaire' => url('/secretaire'),
        'coordinateur' => url('/coordinateur'),
        'chef_bureau' => url('/chef'),
        default => null,
    };
    $panelLabel = match ($user->role) {
        'administrateur' => 'Panel admin',
        'secretaire' => 'Panel secrétaire',
        'coordinateur' => 'Panel coordinateur',
        'chef_bureau' => 'Panel chef',
        default => 'Panel',
    };
    $dashUrl = route('presence.dashboard');
    $signUrl = route('presence.sign');
    $histUrl = route('presence.historique');
    $profilUrl = route('presence.profile');

    $linkBase = 'inline-flex shrink-0 items-center justify-center min-h-[44px] rounded-xl px-3 py-2 text-xs font-medium transition border sm:text-[13px] ';
    $linkIdle = 'border-transparent text-slate-400 hover:border-white/10 hover:bg-white/5 hover:text-white';
    $linkActive = 'border-blue-400/40 bg-blue-500/20 text-white shadow-sm';
    $profilePhotoUrl = $user->photo_reference
        ? route('users.photo-reference', $user).'?v='.(string) (optional($user->updated_at)->timestamp ?? time())
        : null;
@endphp

<header class="glass sticky top-0 z-20 border-x-0 border-t-0 rounded-none" style="padding-top: env(safe-area-inset-top, 0px);">
    {{-- Ligne 1 : identité + déconnexion (jamais écrasée par la nav) --}}
    <div class="flex items-center justify-between gap-3 px-4 py-2.5 border-b border-white/5">
        <a href="{{ $profilUrl }}" class="flex items-center gap-3 min-w-0 flex-1 group" title="Mon profil">
            @if($profilePhotoUrl)
                <img src="{{ $profilePhotoUrl }}"
                     alt="Photo de profil"
                     class="w-10 h-10 rounded-full object-cover object-center flex-shrink-0 border border-blue-300/35 group-hover:ring-2 group-hover:ring-blue-400/40 transition">
            @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center flex-shrink-0 font-bold text-sm uppercase select-none text-white group-hover:ring-2 group-hover:ring-blue-400/40 transition">
                    {{ mb_substr($user->getDisplayName(), 0, 2) }}
                </div>
            @endif
            <div class="min-w-0">
                <p class="text-sm font-semibold leading-tight truncate text-white group-hover:text-blue-200 transition">{{ $user->getDisplayName() }}</p>
                <p class="text-[11px] text-slate-400 leading-tight truncate">{{ $user->matricule ?? $user->email }} · {{ now()->translatedFormat('d M Y') }}</p>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] text-slate-300 hover:text-white transition p-2 rounded-xl hover:bg-white/10 touch-manipulation" title="Se déconnecter">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
            </button>
        </form>
    </div>

    {{-- Ligne 2 : onglets — défilement horizontal sur petit écran (évite liens écrasés) --}}
    <nav class="presence-nav px-3 sm:px-4 py-2.5 flex flex-nowrap gap-2 overflow-x-auto overscroll-x-contain scroll-smooth" aria-label="Navigation portail">
        <a href="{{ route('home') }}"
           class="{{ $linkBase }} {{ $linkIdle }}">
            Accueil
        </a>
        <a href="{{ $dashUrl }}"
           class="{{ $linkBase }} {{ $current === 'dashboard' ? $linkActive : $linkIdle }}">
            {{ $user->isExemptePointage() ? 'Tableau de bord' : 'Mon pointage' }}
        </a>
        @if(! $user->isExemptePointage())
        <a href="{{ $signUrl }}"
           class="{{ $linkBase }} {{ $current === 'sign' ? $linkActive : $linkIdle }}">
            Signature
        </a>
        @endif
        <a href="{{ $histUrl }}"
           class="{{ $linkBase }} {{ $current === 'historique' ? $linkActive : $linkIdle }}">
            Historique
        </a>
        <a href="{{ $profilUrl }}"
           class="{{ $linkBase }} {{ $current === 'profile' ? $linkActive : $linkIdle }}"
           title="Mon profil">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5 hidden sm:inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
            Profil
        </a>
        @if($adminHome && $panelUrl)
            <a href="{{ $panelUrl }}"
               class="{{ $linkBase }} {{ $linkIdle }}">
                {{ $panelLabel }}
            </a>
        @endif
    </nav>
</header>
