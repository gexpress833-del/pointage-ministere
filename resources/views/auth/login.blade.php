<!DOCTYPE html>
<html lang="fr" class="m-0 p-0 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
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
    </style>
</head>
<body class="m-0 p-0 bg-slate-50 min-h-screen min-h-dvh overflow-x-hidden antialiased">

    <div class="gradient-bg relative min-h-screen flex items-center justify-center overflow-hidden px-4">
        <div class="blob w-96 h-96 bg-blue-300 top-0 -left-20"></div>
        <div class="blob w-80 h-80 bg-indigo-400 bottom-0 right-0" style="animation-delay:3s"></div>

        <div class="relative w-full max-w-md fade-in">
            <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10">

                <div class="flex flex-col items-center mb-8">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">Connexion</h1>
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

                <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="login" class="block text-sm font-medium text-slate-700 mb-1.5">Email ou téléphone</label>
                        <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                            placeholder="vous@exemple.cd ou +243..." />
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Mot de passe</label>
                        <input type="password" id="password" name="password" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                            placeholder="••••••••" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="remember" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20" />
                        <label for="remember" class="text-sm text-slate-600">Se souvenir de moi</label>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm">
                        Se connecter
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-blue-600 transition">
                        ← Retour à l'accueil
                    </a>
                </div>
            </div>

            <p class="text-center text-blue-100 text-xs mt-6">
                Coordination Sous-Provinciale · République Démocratique du Congo
            </p>
        </div>
    </div>

</body>
</html>
