<!DOCTYPE html>
<html lang="fr" class="m-0 p-0 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="icon" type="image/png" href="/logo3.png">
    @include('partials.pwa-head')
    @include('partials.spinner')
    <title>{{ config('app.name') }}</title>
    <x-vite-tailwind />
    <style>
        /* Évite la bande blanche (marges agent utilisateur) au-dessus de la barre fixe */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Responsive: ajustements petits écrans */
        @media (max-width: 380px) {
            .hero-title { font-size: 2rem !important; line-height: 1.1 !important; }
            .hero-subtitle { font-size: 0.95rem !important; }
            .nav-btn { padding: 0.5rem 0.75rem !important; font-size: 0.8rem !important; }
        }
        @media (max-width: 640px) {
            .hero-title { font-size: 2.25rem !important; }
            .hero-subtitle { font-size: 1rem !important; }
        }
        @media (min-width: 641px) and (max-width: 1023px) {
            .hero-title { font-size: 3rem !important; }
        }
        /* Safe area pour notch / dynamic island */
        body { padding-top: env(safe-area-inset-top); padding-bottom: env(safe-area-inset-bottom); }

        .gradient-bg {
            background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 50%, #1e40af 100%);
        }
        .card-hover {
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0,0,0,.12);
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .15;
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-20px) scale(1.05); }
        }
        .fade-in {
            animation: fadeIn .7s ease both;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .delay-1 { animation-delay: .1s; }
        .delay-2 { animation-delay: .2s; }
        .delay-3 { animation-delay: .3s; }
        .delay-4 { animation-delay: .4s; }
    </style>
</head>
<body class="m-0 p-0 bg-slate-50 min-h-screen min-h-dvh overflow-x-hidden antialiased">

    {{-- ── Navigation ──────────────────────────────────────── --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200" style="padding-top: env(safe-area-inset-top, 0px);">
        <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-6 h-14 sm:h-16 flex items-center justify-between gap-2 min-w-0">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                <img src="/logo3.png" alt="Logo" class="w-8 h-8 sm:w-9 sm:h-9 object-contain flex-shrink-0 rounded-lg">
                <span class="font-semibold text-slate-800 text-xs sm:text-sm lg:text-base truncate leading-tight">{{ config('app.name') }}</span>
            </div>
            @auth
                @php
                    $user = auth()->user();
                    $dashboardUrl = match($user->role) {
                        'administrateur' => '/admin',
                        'secretaire' => '/secretaire',
                        'coordinateur' => '/coordinateur',
                        'chef_bureau' => '/chef',
                        'agent' => route('presence.dashboard'),
                        default => '/login',
                    };
                @endphp
                <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                    <a href="{{ $dashboardUrl }}"
                       class="nav-btn inline-flex items-center justify-center gap-1.5 sm:gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium px-2.5 sm:px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span class="hidden sm:inline">Mon espace</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="nav-btn inline-flex items-center justify-center gap-1.5 sm:gap-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs sm:text-sm font-medium px-2.5 sm:px-4 py-2 rounded-lg transition-colors whitespace-nowrap"
                            aria-label="Se déconnecter" title="Se déconnecter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M6 18 18 6M6 6l12 12" />
                            </svg>
                            <span class="hidden sm:inline">Déconnexion</span>
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-1.5 sm:gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium px-2.5 sm:px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    <span class="hidden sm:inline">Se connecter</span>
                    <span class="sm:hidden">Connexion</span>
                </a>
            @endauth
        </div>
    </nav>

    {{-- En-tête institutionnel (Mines / coordination — aligné PDF) --}}
    <section class="pt-14 sm:pt-16 bg-white border-b border-slate-200 shadow-sm">
        <x-official-institutional-header />
    </section>

    {{-- ── Hero (sans min-h-screen + items-center : évite le vide bleu au-dessus du contenu) --}}
    <section class="gradient-bg relative pt-8 sm:pt-10 pb-10 sm:pb-14 overflow-hidden">
        {{-- Image d'arrière-plan: affiche du ministère --}}
        <div class="absolute inset-0 opacity-10 bg-cover bg-center" style="background-image: url('/affiche_ministère.jfif');"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 to-blue-700/20"></div>
        {{-- Blobs décoratifs --}}
        <div class="blob w-96 h-96 bg-blue-300 top-4 -left-20"></div>
        <div class="blob w-80 h-80 bg-indigo-400 bottom-4 right-0" style="animation-delay:3s"></div>
        <div class="blob w-64 h-64 bg-sky-300 top-1/3 left-1/2" style="animation-delay:6s"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 w-full">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Texte --}}
                <div class="text-white">
                    <div class="fade-in inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Coordination Sous-Provinciale — RDC
                    </div>
                    <h1 class="hero-title fade-in delay-1 text-3xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-4 sm:mb-6">
                        Gestion de<br>
                        <span class="text-sky-300">présence</span><br>
                        biométrique
                    </h1>
                    <p class="hero-subtitle fade-in delay-2 text-base sm:text-lg text-blue-100 leading-relaxed mb-6 sm:mb-8 max-w-lg">
                        Pointage biométrique par reconnaissance faciale. Session automatique chaque jour : arrivée de 07h59 à 11h59, départ de 15h59 à 23h59. Sécurisé, rapide et fiable.
                    </p>
                    <div class="fade-in delay-3 flex flex-col sm:flex-row gap-3">
                        @auth
                            <a href="{{ $dashboardUrl }}"
                               class="inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-semibold px-6 py-3.5 rounded-xl shadow-lg hover:shadow-xl hover:bg-blue-50 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                </svg>
                                Accéder à l'espace personnel
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-semibold px-6 py-3.5 rounded-xl shadow-lg hover:shadow-xl hover:bg-blue-50 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                </svg>
                                Accéder à l'espace personnel
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Carte illustration --}}
                <div class="fade-in delay-4 hidden lg:block">
                    <div class="bg-white/10 backdrop-blur border border-white/20 rounded-3xl p-8 space-y-4">
                        {{-- Carte mini tableau de bord --}}
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">Tableau de bord</p>
                                <p class="text-blue-200 text-xs">Statistiques du jour</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            @foreach([['🟢','Présents','24','success'],['🟡','Retards','3','warning'],['🔴','Absents','5','danger'],['📋','Total','32','primary']] as [$icon,$label,$val,$color])
                            <div class="bg-white/15 rounded-2xl p-4 border border-white/10">
                                <p class="text-2xl font-bold text-white">{{ $val }}</p>
                                <p class="text-blue-200 text-xs mt-1">{{ $icon }} {{ $label }}</p>
                            </div>
                            @endforeach
                        </div>

                        <div class="bg-white/15 rounded-2xl p-4 border border-white/10">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                                <p class="text-white text-xs font-medium">Session ouverte automatiquement à 07h59</p>
                            </div>
                            <div class="space-y-2">
                                @foreach(['Agent Kabongo – 07:42 ✅ Présent','Agent Mbala – 08:15 ✅ Présent','Agent Dupont – 09:12 ⚠️ Retard'] as $row)
                                <div class="text-blue-200 text-xs bg-white/10 rounded-lg px-3 py-1.5">{{ $row }}</div>
                                @endforeach
                            </div>
                        </div>
                        <div class="bg-white/15 rounded-2xl p-4 border border-white/10">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                                <p class="text-white text-xs font-medium">Départ & heures supplémentaires</p>
                            </div>
                            <div class="space-y-2">
                                <div class="text-blue-200 text-xs bg-white/10 rounded-lg px-3 py-1.5">Agent Kabongo – 16:30 ✅ Départ normal</div>
                                <div class="text-blue-200 text-xs bg-white/10 rounded-lg px-3 py-1.5">Agent Mbala – 18:45 ⏱️ +1h45 sup.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Fonctionnalités ──────────────────────────────────── --}}
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-14">
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-800 mb-4">Comment ça fonctionne ?</h2>
                <p class="text-slate-500 max-w-xl mx-auto">Un flux simple et sécurisé pour gérer la présence de tous les agents.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach([
                    ['bg-blue-50','text-blue-600','M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z','Session automatique','La session s\'ouvre automatiquement à 07h59 et se clôture à 23h59 chaque jour. Aucune intervention manuelle requise.'],
                    ['bg-violet-50','text-violet-600','M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z','Reconnaissance faciale','L\'agent se connecte et laisse la caméra identifier son visage pour pointer son arrivée ou son départ.'],
                    ['bg-green-50','text-green-600','m4.5 12.75 6 6 9-13.5','Arrivée : présent ou retard','Pointage d\'arrivée de 07h59 à 11h59. Avant 08h59 = présent, après 08h59 = retard. L\'heure exacte est enregistrée automatiquement.'],
                    ['bg-amber-50','text-amber-600','M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9','Départ & heures sup.','Pointage de départ de 15h59 à 23h59. Avant 16h59 = départ normal, après 16h59 = heures supplémentaires calculées automatiquement.'],
                    ['bg-red-50','text-red-600','M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z','Rapports PDF','Générez des rapports journaliers ou mensuels par bureau avec statuts, heures d\'arrivée, de départ et heures supplémentaires.'],
                    ['bg-slate-50','text-slate-600','M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z','Gestion des agents','Administration complète des utilisateurs, bureaux, services, annonces et paramètres système.'],
                ] as [$bg, $color, $path, $title, $desc])
                <div class="card-hover {{ $bg }} rounded-2xl p-6 border border-slate-100">
                    <div class="w-11 h-11 {{ $bg }} rounded-xl flex items-center justify-center mb-4 border border-current/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 {{ $color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">{{ $title }}</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Mot du Ministre ──────────────────────────────────── --}}
    <section class="py-16 sm:py-20 bg-gradient-to-b from-slate-50 to-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-2">Mot du Ministre</h2>
                <div class="w-16 h-1 bg-blue-600 rounded-full mx-auto"></div>
            </div>
            <div class="flex flex-col md:flex-row items-center gap-6 sm:gap-10">
                <div class="flex-shrink-0">
                    <div class="relative">
                        <img src="/leministre.jpg" alt="Le Ministre" class="w-40 h-40 sm:w-48 sm:h-48 rounded-2xl object-cover object-center shadow-xl border-4 border-white ring-1 ring-slate-200">
                        <div class="absolute -bottom-3 -right-3 w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed italic mb-4">
                        « La formation professionnelle est un pilier essentiel pour le développement de notre nation. Ce système de pointage biométrique illustre notre engagement envers la modernisation et la transparence dans la gestion de nos établissements. »
                    </p>
                    <p class="font-semibold text-slate-800 text-sm sm:text-base">Ministre de la Formation Professionnelle et Métiers</p>
                    <p class="text-slate-500 text-xs sm:text-sm">République Démocratique du Congo</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── CTA final ───────────────────────────────────────── --}}
    <section class="gradient-bg py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-cover bg-center" style="background-image: url('/affiche_ministère.jfif');"></div>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Prêt à pointer ?</h2>
            <p class="text-blue-100 mb-8">Connectez-vous pour accéder à votre espace personnel.</p>
            @auth
                <a href="{{ $dashboardUrl }}"
                   class="inline-flex items-center gap-2 bg-white text-blue-700 font-semibold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl hover:bg-blue-50 transition-all text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    Accéder à l'espace personnel
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-white text-blue-700 font-semibold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl hover:bg-blue-50 transition-all text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    Accéder à l'espace personnel
                </a>
            @endauth
        </div>
    </section>

    {{-- ── Footer ──────────────────────────────────────────── --}}
    <footer class="bg-slate-900 text-slate-400 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
            <div class="flex items-center gap-3">
                <img src="/logo3.png" alt="Logo" class="w-8 h-8 object-contain">
                <img src="/Drapeaux_rdc.webp" alt="Drapeau RDC" class="w-10 h-7 object-cover rounded">
                <span class="text-slate-300 font-medium">{{ config('app.name') }}</span>
            </div>
            <p class="text-center sm:text-right">Coordination Sous-Provinciale · République Démocratique du Congo · {{ date('Y') }}</p>
        </div>
    </footer>

</body>
</html>
