<style>
    /* Thème sombre — styles ciblés (sans écraser tous les composants Filament) */
    body.fi-body,
    html {
        background: #0b1120 !important;
        color: #f8fafc !important;
    }

    .fi-body {
        background:
            radial-gradient(circle at top left, rgba(37, 99, 235, 0.15), transparent 28%),
            radial-gradient(circle at top right, rgba(14, 165, 233, 0.10), transparent 24%),
            linear-gradient(180deg, #0b1120 0%, #0f172a 100%) !important;
    }

    .fi-sidebar,
    .fi-sidebar-nav {
        background: #0f172a !important;
        border-color: rgba(148, 163, 184, 0.16) !important;
    }

    .fi-sidebar-header,
    .fi-topbar nav,
    .fi-topbar {
        background: rgba(15, 23, 42, 0.92) !important;
        border-color: rgba(148, 163, 184, 0.16) !important;
    }

    .fi-section,
    .fi-wi-widget,
    .fi-ta,
    .fi-modal-window,
    .fi-dropdown-panel {
        background: rgba(17, 24, 39, 0.88) !important;
        border: 1px solid rgba(148, 163, 184, 0.16) !important;
        border-radius: 16px !important;
        color: #f8fafc !important;
    }

    .fi-ta-header-cell,
    .fi-ta-cell,
    .fi-ta-row {
        background: rgba(15, 23, 42, 0.6) !important;
        border-color: rgba(148, 163, 184, 0.16) !important;
        color: #f8fafc !important;
    }

    .fi-dropdown-list-item {
        color: #f8fafc !important;
    }

    .fi-dropdown-list-item:hover {
        background: rgba(30, 41, 59, 0.5) !important;
    }

    .fi-input,
    .fi-select-input,
    .fi-textarea {
        background: rgba(15, 23, 42, 0.82) !important;
        border: 1px solid rgba(148, 163, 184, 0.16) !important;
        color: #f8fafc !important;
    }

    .fi-btn-color-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        border: none !important;
        color: #fff !important;
    }

    .fi-sidebar-item-active > .fi-sidebar-item-button,
    .fi-sidebar-item-button:hover {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.22), rgba(14, 165, 233, 0.12)) !important;
        color: #fff !important;
    }

    .fi-logo,
    .fi-page-header-heading,
    h1, h2, h3 {
        color: #f8fafc !important;
    }

    .fi-sidebar-group-label,
    .fi-sidebar-item-description,
    .fi-fo-field-wrp-label {
        color: #94a3b8 !important;
    }

    /* Bouton profil (DM) — ne pas le rendre transparent / carré */
    .fi-user-menu-trigger {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 44px !important;
        min-height: 44px !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 9999px !important;
        background: transparent !important;
        cursor: pointer !important;
        touch-action: manipulation !important;
        -webkit-tap-highlight-color: transparent !important;
    }

    .fi-user-menu-trigger:hover,
    .fi-user-menu-trigger:focus-visible {
        background: rgba(255, 255, 255, 0.08) !important;
        outline: none !important;
    }

    .fi-user-menu-trigger .fi-avatar,
    .fi-user-menu-trigger img {
        border-radius: 9999px !important;
    }

    /* Boutons topbar / sidebar mobile */
    .fi-topbar .fi-icon-btn,
    .fi-layout-sidebar-toggle-btn,
    .fi-sidebar-close-collapse-sidebar-btn {
        min-width: 44px !important;
        min-height: 44px !important;
        touch-action: manipulation !important;
    }

    /* Toggle menu mobile : Filament alterne ☰ / X via Alpine (x-show) — ne pas forcer display */
    .fi-topbar-open-sidebar-btn,
    .fi-topbar-close-sidebar-btn {
        flex-shrink: 0;
        color: #e2e8f0 !important;
    }

    .fi-topbar-open-sidebar-btn .fi-icon-btn,
    .fi-topbar-close-sidebar-btn .fi-icon-btn,
    .fi-topbar-open-sidebar-btn button,
    .fi-topbar-close-sidebar-btn button {
        color: #e2e8f0 !important;
        transition: opacity 0.15s ease, transform 0.15s ease;
    }

    .fi-topbar-close-sidebar-btn .fi-icon-btn:active,
    .fi-topbar-open-sidebar-btn .fi-icon-btn:active {
        transform: scale(0.94);
    }

    /* Overlay sidebar mobile natif Filament */
    .fi-sidebar-close-overlay {
        z-index: 25 !important;
    }

    @media (max-width: 1023px) {
        /* Topbar au-dessus du sidebar : le bouton ☰/X reste cliquable */
        .fi-topbar-ctn {
            z-index: 40 !important;
        }

        .fi-sidebar {
            z-index: 30 !important;
        }

        /* Même emplacement : un seul bouton visible à la fois */
        .fi-topbar-open-sidebar-btn,
        .fi-topbar-close-sidebar-btn {
            width: 44px;
            min-width: 44px;
        }
    }

    @media (max-width: 1024px) {
        .fi-sidebar {
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.45) !important;
        }

        .fi-topbar {
            padding-top: env(safe-area-inset-top, 0px) !important;
        }
    }

    @media (max-width: 640px) {
        .fi-page-header-heading {
            font-size: 1.1rem !important;
        }

        .fi-sidebar-item-label {
            font-size: 0.875rem !important;
        }
    }
</style>
