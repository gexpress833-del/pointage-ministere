<x-filament-widgets::widget>
    <style>
        .qa-card {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            overflow: hidden;
        }
        .qa-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .qa-user {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .qa-avatar {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex: 0 0 42px;
        }
        .qa-name {
            margin: 0;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.2;
        }
        .qa-role {
            margin: 2px 0 0;
            color: #9ca3af;
            font-size: 12px;
            line-height: 1.2;
        }
        .qa-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .qa-badge--open {
            background: rgba(34, 197, 94, 0.14);
            color: #86efac;
        }
        .qa-badge--closed {
            background: rgba(148, 163, 184, 0.14);
            color: #cbd5e1;
        }
        .qa-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: currentColor;
        }
        .qa-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            padding: 16px;
        }
        .qa-action,
        .qa-action-button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: #0f172a;
            text-decoration: none;
            transition: .15s ease;
            min-height: 74px;
        }
        .qa-action:hover,
        .qa-action-button:hover {
            transform: translateY(-1px);
            border-color: rgba(255, 255, 255, 0.16);
            background: #162033;
        }
        .qa-action-button {
            cursor: pointer;
            text-align: left;
        }
        .qa-action[aria-disabled="true"] {
            opacity: .55;
            pointer-events: none;
        }
        .qa-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 40px;
        }
        .qa-icon svg {
            width: 18px;
            height: 18px;
            display: block;
        }
        .qa-text {
            min-width: 0;
        }
        .qa-title {
            margin: 0;
            color: #f8fafc;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.2;
        }
        .qa-subtitle {
            margin: 4px 0 0;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.2;
        }
        .qa-green { background: rgba(34, 197, 94, 0.14); color: #86efac; }
        .qa-blue { background: rgba(59, 130, 246, 0.14); color: #93c5fd; }
        .qa-violet { background: rgba(139, 92, 246, 0.14); color: #c4b5fd; }
        .qa-teal { background: rgba(20, 184, 166, 0.14); color: #99f6e4; }
        .qa-amber { background: rgba(245, 158, 11, 0.14); color: #fcd34d; }
        .qa-red { background: rgba(239, 68, 68, 0.14); color: #fca5a5; }
        @media (max-width: 640px) {
            .qa-header { align-items: flex-start; flex-direction: column; }
        }
    </style>

    <div class="qa-card">
        <div class="qa-header">
            <div class="qa-user">
                <div class="qa-avatar">{{ mb_substr($user?->getDisplayName() ?? '?', 0, 2) }}</div>
                <div style="min-width: 0;">
                    <p class="qa-name">{{ $user?->getDisplayName() }}</p>
                    <p class="qa-role">
                        @switch($user?->role)
                            @case('administrateur') Administrateur @break
                            @case('coordinateur') Coordinateur @break
                            @case('chef_bureau') Chef de bureau @break
                            @case('agent') Agent · {{ $user?->bureau?->nom_bureau ?? '—' }} @break
                            @default {{ $user?->role }}
                        @endswitch
                    </p>
                </div>
            </div>

            <span class="qa-badge {{ $session ? 'qa-badge--open' : 'qa-badge--closed' }}">
                <span class="qa-dot"></span>
                {{ $session ? 'Session ouverte' : 'Pas de session' }}
            </span>
        </div>

        <div class="qa-grid">
            @if(in_array($user?->role, ['agent', 'chef_bureau']))
                <a href="{{ url('/presence') }}" class="qa-action">
                    <span class="qa-icon qa-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776 12 3l8.25 6.776v9.474A1.75 1.75 0 0 1 18.5 21h-13A1.75 1.75 0 0 1 3.75 19.25V9.776Z" />
                        </svg>
                    </span>
                    <span class="qa-text">
                        <p class="qa-title">Portail agent</p>
                        <p class="qa-subtitle">Tableau de bord</p>
                    </span>
                </a>

                <a href="{{ url('/presence/sign') }}" class="qa-action" @if(!$canSign) aria-disabled="true" @endif>
                    <span class="qa-icon qa-green">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                    <span class="qa-text">
                        <p class="qa-title">Signer ma présence</p>
                        <p class="qa-subtitle">{{ $canSign ? 'Session disponible' : ($dejaSigne ? 'Déjà signé aujourd\'hui' : 'Session fermée') }}</p>
                    </span>
                </a>

                <a href="{{ route('presence.historique') }}" class="qa-action">
                    <span class="qa-icon qa-teal">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                    <span class="qa-text">
                        <p class="qa-title">Mon historique</p>
                        <p class="qa-subtitle">Présences par mois</p>
                    </span>
                </a>
            @endif

            @if(in_array($user?->role, ['administrateur', 'coordinateur', 'chef_bureau']))
                <a href="{{ \App\Filament\Resources\SessionsPresence\SessionPresenceResource::getUrl('index') }}" class="qa-action">
                    <span class="qa-icon qa-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </span>
                    <span class="qa-text">
                        <p class="qa-title">Sessions de présence</p>
                        <p class="qa-subtitle">{{ $session ? 'Gérer la session du jour' : 'Ouvrir ou consulter les sessions' }}</p>
                    </span>
                </a>

                <a href="{{ \App\Filament\Resources\Presences\PresenceResource::getUrl('index') }}" class="qa-action">
                    <span class="qa-icon qa-violet">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                        </svg>
                    </span>
                    <span class="qa-text">
                        <p class="qa-title">Présences signées</p>
                        <p class="qa-subtitle">Consulter les signatures</p>
                    </span>
                </a>
            @endif

            @if($canViewReports)
                <a href="{{ \App\Filament\Resources\SessionsPresence\SessionPresenceResource::getUrl('index') }}" class="qa-action">
                    <span class="qa-icon qa-amber">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </span>
                    <span class="qa-text">
                        <p class="qa-title">Rapports PDF</p>
                        <p class="qa-subtitle">Exports journaliers et mensuels</p>
                    </span>
                </a>
            @endif

            <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                @csrf
                <button type="submit" class="qa-action-button">
                    <span class="qa-icon qa-red">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </span>
                    <span class="qa-text">
                        <p class="qa-title">Déconnexion</p>
                        <p class="qa-subtitle">Quitter la session</p>
                    </span>
                </button>
            </form>
        </div>
    </div>
</x-filament-widgets::widget>
