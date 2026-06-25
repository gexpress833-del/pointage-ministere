<!DOCTYPE html>
<html lang="fr" class="m-0 p-0 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mon profil – {{ config('app.name') }}</title>
    <x-vite-tailwind />
    @include('presence.partials.portal-theme')
    <style>
        .portal-field-ro {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid rgba(51, 65, 85, 0.65);
            padding: 0.625rem 0.75rem;
            background: rgba(15, 23, 42, 0.55);
            color: #e2e8f0;
            font-size: 0.9375rem;
            line-height: 1.4;
        }
        .portal-input-editable {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid rgba(96, 165, 250, 0.35);
            padding: 0.625rem 0.75rem;
            background: rgba(15, 23, 42, 0.92);
            color: #f8fafc;
            font-size: 1rem;
            line-height: 1.4;
        }
        .portal-input-editable:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.45);
            border-color: rgba(96, 165, 250, 0.55);
        }
        .portal-input-editable::placeholder {
            color: #64748b;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen min-h-dvh m-0 antialiased overflow-x-hidden">

    <x-presence.agent-header :user="$user" current="profile" />

    @php
        $profilePhotoUrl = $user->photo_reference
            ? route('users.photo-reference', $user).'?v='.(string) (optional($user->updated_at)->timestamp ?? time())
            : null;
    @endphp

    <main class="flex-1 max-w-4xl mx-auto w-full px-3 sm:px-6 pt-2 pb-8 space-y-4 safe-pb">
        <div class="glass rounded-2xl p-4 sm:p-5">
            <div class="flex justify-center mb-3">
                @if($profilePhotoUrl)
                    <img src="{{ $profilePhotoUrl }}"
                         alt="Photo de profil"
                         class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover object-center border-2 border-blue-300/45 shadow-md shadow-blue-900/30">
                @else
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center font-bold text-2xl uppercase text-white border-2 border-blue-300/35">
                        {{ mb_substr($user->getDisplayName(), 0, 2) }}
                    </div>
                @endif
            </div>
            <p class="text-center text-[11px] sm:text-xs text-slate-300 font-medium">Photo de reference utilisee pour le pointage</p>
            <p class="text-center text-[11px] text-amber-300/95 mt-1">Modification reservee a l'administrateur</p>
            <h1 class="text-xs font-semibold text-slate-400 uppercase tracking-[0.18em]">Profil</h1>
            <p class="text-white text-lg font-bold mt-0.5">Mes informations</p>
            <p class="text-slate-400 text-sm mt-1">Seuls le numéro de téléphone et l’adresse de résidence peuvent être modifiés.</p>
        </div>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-100" role="alert">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('presence.profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="glass rounded-2xl p-4 sm:p-5 space-y-4">
                <h2 class="text-sm font-semibold text-white">Identité et affectation</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Nom complet</label>
                        <div class="portal-field-ro">{{ $user->nom ?? $user->name ?? '—' }}</div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Matricule</label>
                        <div class="portal-field-ro font-mono text-sm">{{ $user->matricule ?? '—' }}</div>
                    </div>
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Adresse e-mail</label>
                        <div class="portal-field-ro break-all">{{ $user->email }}</div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Fonction</label>
                        <div class="portal-field-ro">
                            @php
                                $roleLabels = [
                                    \App\Models\User::ROLE_ADMIN => 'Administrateur',
                                    \App\Models\User::ROLE_COORDINATEUR => 'Coordinateur',
                                    \App\Models\User::ROLE_CHEF_BUREAU => 'Chef de bureau',
                                    \App\Models\User::ROLE_AGENT => 'Agent',
                                ];
                            @endphp
                            {{ $roleLabels[$user->role] ?? $user->role }}
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Photo de référence</label>
                        <div class="portal-field-ro">
                            @if($user->photo_reference)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="inline-flex items-center gap-2 font-medium" style="color:#4ade80;">
                                        <span class="w-2 h-2 rounded-full" style="background-color:#4ade80;"></span>
                                        Configuree (admin)
                                    </span>
                                    <img src="{{ $profilePhotoUrl }}" alt="Photo de reference" class="w-9 h-9 rounded-full object-cover object-center border border-blue-300/35">
                                </div>
                            @else
                                <span class="inline-flex items-center gap-2 font-medium" style="color:#f87171;">
                                    <span class="w-2 h-2 rounded-full" style="background-color:#f87171;"></span>
                                    Non configuree (contacter l'administrateur)
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Bureau</label>
                        <div class="portal-field-ro">{{ $user->bureau?->nom_bureau ?? '—' }}</div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Service</label>
                        <div class="portal-field-ro">{{ $user->service?->nom_service ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="glass rounded-2xl p-4 sm:p-5 space-y-4 border border-blue-500/20">
                <h2 class="text-sm font-semibold text-blue-100">Coordonnées modifiables</h2>
                <div class="space-y-1.5">
                    <label for="telephone" class="text-[11px] font-medium text-slate-300">Téléphone</label>
                    <input type="tel" name="telephone" id="telephone" value="{{ old('telephone', $user->telephone) }}"
                           autocomplete="tel" inputmode="tel"
                           class="portal-input-editable"
                           placeholder="Ex. +243 …">
                </div>
                <div class="space-y-1.5">
                    <label for="adresse_residence" class="text-[11px] font-medium text-slate-300">Adresse de résidence</label>
                    <textarea name="adresse_residence" id="adresse_residence" rows="4"
                              class="portal-input-editable resize-y min-h-[6rem]"
                              placeholder="Avenue, quartier, ville…">{{ old('adresse_residence', $user->adresse_residence) }}</textarea>
                </div>
            </div>

            <button type="submit"
                    class="w-full sm:w-auto min-h-[48px] px-6 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-semibold text-sm shadow-lg transition touch-manipulation">
                Enregistrer les modifications
            </button>
        </form>
    </main>
</body>
</html>
