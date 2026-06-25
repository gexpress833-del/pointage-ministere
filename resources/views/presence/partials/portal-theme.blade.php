{{-- Styles partagés : tableau de bord + historique --}}
<style>
    html { color-scheme: dark; }
    * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
    a, button { touch-action: manipulation; }
    body {
        margin: 0;
        min-height: 100dvh;
        background:
            radial-gradient(circle at top left, rgba(37, 99, 235, 0.20), transparent 30%),
            radial-gradient(circle at top right, rgba(14, 165, 233, 0.12), transparent 25%),
            linear-gradient(180deg, #0b1120 0%, #0f172a 100%);
        color: #e2e8f0;
    }
    .glass {
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.2);
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
    }
    .portal-select {
        width: 100%;
        font-size: 0.875rem;
        line-height: 1.25rem;
        border: 1px solid rgba(51, 65, 85, 0.9);
        border-radius: 0.75rem;
        padding: 0.625rem 2.25rem 0.625rem 0.75rem;
        background-color: rgb(15 23 42 / 0.92);
        color: #f1f5f9;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.65rem center;
        background-size: 1rem;
    }
    .portal-select:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.45);
        border-color: rgba(96, 165, 250, 0.5);
    }
    .safe-pb {
        padding-bottom: max(2rem, calc(1rem + env(safe-area-inset-bottom, 0px)));
    }
    .portal-select option {
        background-color: #0f172a;
        color: #e2e8f0;
    }
    /* Navigation : masque la barre de défilement tout en gardant le swipe */
    .presence-nav {
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.35) transparent;
        -webkit-overflow-scrolling: touch;
    }
    .presence-nav::-webkit-scrollbar {
        height: 4px;
    }
    .presence-nav::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.35);
        border-radius: 4px;
    }
    @media (max-width: 640px) {
        .portal-select {
            font-size: 1rem;
        }
        /* Évite le défilement horizontal sans casser position:fixed / grilles */
        html, body {
            overflow-x: hidden;
        }
        main, .glass, .presence-nav, img, video, canvas, table, pre {
            max-width: 100%;
        }
        .glass, main {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .glass {
            overflow: hidden;
        }
    }
</style>
