<!DOCTYPE html>
<html lang="fr" class="m-0 p-0 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="icon" type="image/png" href="{{ asset('logo3.png') }}">
    @include('partials.pwa-head')
    <title>Changer le mot de passe — {{ config('app.name') }}</title>
    <x-vite-tailwind />
    <style>
        html, body { margin: 0 !important; padding: 0 !important; }
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 50%, #1e40af 100%);
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
        <div class="relative w-full max-w-md fade-in px-2 sm:px-0">
            <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8 md:p-10">

                <div class="flex flex-col items-center mb-6 sm:mb-8">
                    <img src="{{ asset('logo3.png') }}" alt="Logo" class="w-12 h-12 sm:w-14 sm:h-14 object-contain mb-3 sm:mb-4">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Changer le mot de passe</h1>
                    <p class="text-slate-500 text-sm mt-1 text-center">Pour votre sécurité, vous devez définir un nouveau mot de passe avant de continuer.</p>
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

                <form method="POST" action="{{ route('password.change.submit') }}" class="space-y-4 sm:space-y-5">
                    @csrf
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Nouveau mot de passe</label>
                        <input type="password" id="password" name="password" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm sm:text-base text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition min-h-[48px]"
                            placeholder="••••••••" />
                        <p class="text-xs text-slate-400 mt-1">Min. 8 caractères, majuscules, minuscules et chiffres.</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm sm:text-base text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition min-h-[48px]"
                            placeholder="••••••••" />
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm sm:text-base min-h-[48px]">
                        Changer le mot de passe
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-slate-500 hover:text-red-600 transition">
                            Se déconnecter
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 mt-6">
                <img src="{{ asset('logo3.png') }}" alt="Logo" class="w-6 h-6 object-contain">
                <p class="text-center text-blue-100 text-xs">
                    Coordination Sous-Provinciale · République Démocratique du Congo
                </p>
            </div>
        </div>
    </div>

</body>
</html>
