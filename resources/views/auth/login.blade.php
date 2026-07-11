<!DOCTYPE html>
<html lang="fr" class="m-0 p-0 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="icon" type="image/png" href="/logo3.png">
    @include('partials.pwa-head')
    @include('partials.spinner')
    <title>Connexion — {{ config('app.name') }}</title>
    <x-vite-tailwind />
    <style>
        html, body { margin: 0 !important; padding: 0 !important; }
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 50%, #1e40af 100%);
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
        .fade-in { animation: fadeIn .6s ease both; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 380px) {
            .gradient-bg { padding-top: env(safe-area-inset-top, 0px); }
        }
        @media (max-height: 640px) {
            .fade-in { padding-top: 1rem; padding-bottom: 1rem; }
        }
    </style>
</head>
<body class="m-0 p-0 bg-slate-50 min-h-screen min-h-dvh overflow-x-hidden antialiased">

    <div class="gradient-bg relative min-h-screen flex items-center justify-center overflow-hidden px-4">
        <div class="blob w-96 h-96 bg-blue-300 top-0 -left-20"></div>
        <div class="blob w-80 h-80 bg-indigo-400 bottom-0 right-0" style="animation-delay:3s"></div>

        <div class="relative w-full max-w-md fade-in px-2 sm:px-0">
            <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8 md:p-10">

                <div class="flex flex-col items-center mb-6 sm:mb-8">
                    <div class="flex items-center gap-3 mb-3 sm:mb-4">
                        <img src="/logo3.png" alt="Logo" class="w-12 h-12 sm:w-14 sm:h-14 object-contain">
                        <img src="/Drapeaux_rdc.webp" alt="Drapeau RDC" class="w-10 h-7 sm:w-12 sm:h-8 object-cover rounded">
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Connexion</h1>
                    <p class="text-slate-500 text-sm mt-1">Accédez à votre espace personnel</p>
                </div>

                @if (session('status'))
                    <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" class="space-y-4 sm:space-y-5">
                    @csrf
                    <div>
                        <label for="login" class="block text-sm font-medium text-slate-700 mb-1.5">Email ou téléphone</label>
                        <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm sm:text-base text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition min-h-[48px]"
                            placeholder="vous@exemple.cd ou +243..." />
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Mot de passe</label>
                        <input type="password" id="password" name="password" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm sm:text-base text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition min-h-[48px]"
                            placeholder="••••••••" />
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="remember" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20" />
                            <label for="remember" class="text-sm text-slate-600">Se souvenir de moi</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-700 transition">
                            Mot de passe oublié ?
                        </a>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm sm:text-base min-h-[48px]">
                        <span class="btn-label">Se connecter</span>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-blue-600 transition">
                        ← Retour à l'accueil
                    </a>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 mt-6">
                <img src="/logo3.png" alt="Logo" class="w-6 h-6 object-contain">
                <p class="text-center text-blue-100 text-xs">
                    Coordination Sous-Provinciale · République Démocratique du Congo
                </p>
            </div>
        </div>
    </div>

</body>
</html>
