<!DOCTYPE html>
<html lang="fr" class="m-0 p-0 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ config('app.name') }}</title>
    <x-vite-tailwind />
    <style>
        /* Évite la bande blanche (marges agent utilisateur) au-dessus de la barre fixe */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
        }

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
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <span class="font-semibold text-slate-800 text-sm sm:text-base">{{ config('app.name') }}</span>
            </div>
            <a href="{{ route('filament.admin.auth.login') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                Se connecter
            </a>
        </div>
    </nav>

    {{-- En-tête institutionnel (Mines / coordination — aligné PDF) --}}
    <section class="pt-16 bg-white border-b border-slate-200 shadow-sm">
        <x-official-institutional-header />
    </section>

    {{-- ── Hero (sans min-h-screen + items-center : évite le vide bleu au-dessus du contenu) --}}
    <section class="gradient-bg relative pt-8 sm:pt-10 pb-10 sm:pb-14 overflow-hidden">
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
                    <h1 class="fade-in delay-1 text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                        Gestion de<br>
                        <span class="text-sky-300">présence</span><br>
                        biométrique
                    </h1>
                    <p class="fade-in delay-2 text-lg text-blue-100 leading-relaxed mb-8 max-w-lg">
                        Système de pointage par reconnaissance faciale. Sécurisé, rapide et fiable pour tous les agents et bureaux.
                    </p>
                    <div class="fade-in delay-3 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('filament.admin.auth.login') }}"
                           class="inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-semibold px-6 py-3.5 rounded-xl shadow-lg hover:shadow-xl hover:bg-blue-50 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            Accéder à l'espace personnel
                        </a>
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
                                <p class="text-white text-xs font-medium">Session ouverte aujourd'hui</p>
                            </div>
                            <div class="space-y-2">
                                @foreach(['Agent Kabongo – 07:42 ✅','Agent Mbala – 08:15 ✅','Agent Dupont – 08:47 ⚠️'] as $row)
                                <div class="text-blue-200 text-xs bg-white/10 rounded-lg px-3 py-1.5">{{ $row }}</div>
                                @endforeach
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

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    ['bg-blue-50','text-blue-600','M2.25 6.375c0-1.035.84-1.875 1.875-1.875h16.5c1.035 0 1.875.84 1.875 1.875v3.026a.75.75 0 0 1-.375.65 2.249 2.249 0 0 0 0 3.898.75.75 0 0 1 .375.65v3.026c0 1.035-.84 1.875-1.875 1.875H4.125A1.875 1.875 0 0 1 2.25 17.625v-3.026a.75.75 0 0 1 .374-.65 2.249 2.249 0 0 0 0-3.898.75.75 0 0 1-.374-.65V6.375Z','Ouverture de session','L\'administrateur ouvre une session de présence chaque matin depuis le tableau de bord.'],
                    ['bg-violet-50','text-violet-600','M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z','Reconnaissance faciale','L\'agent se connecte et laisse la caméra identifier son visage automatiquement.'],
                    ['bg-green-50','text-green-600','m4.5 12.75 6 6 9-13.5','Signature automatique','Dès que le visage est reconnu, la présence est enregistrée avec l\'heure exacte d\'arrivée.'],
                    ['bg-amber-50','text-amber-600','M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z','Statut en temps réel','Présent ou retardataire selon l\'heure limite configurée par l\'administration.'],
                    ['bg-red-50','text-red-600','M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z','Rapports PDF','Générez des rapports journaliers ou mensuels par bureau en un seul clic.'],
                    ['bg-slate-50','text-slate-600','M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z','Gestion des agents','Administration complète des utilisateurs, bureaux, services et paramètres système.'],
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

    {{-- ── CTA final ───────────────────────────────────────── --}}
    <section class="gradient-bg py-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Prêt à pointer ?</h2>
            <p class="text-blue-100 mb-8">Connectez-vous pour accéder à votre espace personnel.</p>
            <a href="{{ route('filament.admin.auth.login') }}"
               class="inline-flex items-center gap-2 bg-white text-blue-700 font-semibold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl hover:bg-blue-50 transition-all text-base">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                Accéder à l'espace personnel
            </a>
        </div>
    </section>

    {{-- ── Footer ──────────────────────────────────────────── --}}
    <footer class="bg-slate-900 text-slate-400 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-blue-600 rounded flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <span class="text-slate-300 font-medium">{{ config('app.name') }}</span>
            </div>
            <p>Coordination Sous-Provinciale · République Démocratique du Congo · {{ date('Y') }}</p>
        </div>
    </footer>

</body>
</html>
