<link rel="icon" type="image/png" href="{{ route('brand-logo') }}">
@include('partials.pwa-head')
@include('partials.spinner')
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
        border-color: rgba(59, 130, 246, 0.15) !important;
    }

    /* Sidebar items: ensure visible text */
    .fi-sidebar-item-button {
        color: #cbd5e1 !important;
    }

    .fi-sidebar-item-button:hover {
        color: #ffffff !important;
    }

    .fi-sidebar-item-label {
        color: #cbd5e1 !important;
        font-weight: 500 !important;
    }

    .fi-sidebar-item-active .fi-sidebar-item-label {
        color: #ffffff !important;
        font-weight: 600 !important;
    }

    .fi-sidebar-group-label {
        color: #64748b !important;
        text-transform: uppercase !important;
        font-size: 0.7rem !important;
        letter-spacing: 0.05em !important;
    }

    .fi-sidebar-header,
    .fi-topbar nav,
    .fi-topbar {
        background: rgba(15, 23, 42, 0.97) !important;
        border-color: rgba(59, 130, 246, 0.25) !important;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.35) !important;
    }

    /* Topbar text + icons: ensure visible (targeted, not global *) */
    .fi-topbar {
        color: #e2e8f0 !important;
    }

    .fi-topbar .fi-brand-name,
    .fi-topbar .fi-logo-text {
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    .fi-topbar .fi-icon-btn {
        color: #cbd5e1 !important;
    }

    .fi-topbar .fi-icon-btn:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }

    .fi-topbar .fi-icon-btn svg {
        color: inherit !important;
    }

    /* Search bar in topbar: visible */
    .fi-topbar .fi-input-wrp,
    .fi-topbar .fi-input {
        background: rgba(30, 41, 59, 0.6) !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        color: #e2e8f0 !important;
    }

    .fi-topbar .fi-input::placeholder {
        color: #94a3b8 !important;
    }

    /* Topbar buttons: keep their own colors (don't override) */
    .fi-topbar .fi-btn-color-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #fff !important;
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
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.25), rgba(14, 165, 233, 0.15)) !important;
        color: #fff !important;
        box-shadow: inset 3px 0 0 #3b82f6 !important;
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

    /* Formulaire de connexion Filament */
    .fi-auth-card {
        background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%) !important;
        border: 1px solid rgba(59, 130, 246, 0.3) !important;
        border-radius: 20px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 40px rgba(59, 130, 246, 0.1) !important;
        color: #ffffff !important;
    }

    .fi-auth-card-heading,
    .fi-auth-card h1,
    .fi-auth-card h2,
    .fi-auth-card h3 {
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
    }

    .fi-auth-card-subheading,
    .fi-auth-card p {
        color: #cbd5e1 !important;
    }

    .fi-auth-form {
        padding: 2rem !important;
        color: #ffffff !important;
    }

    .fi-auth-form .fi-input,
    .fi-auth-form .fi-select-input,
    .fi-auth-form .fi-textarea {
        background: rgba(15, 23, 42, 0.8) !important;
        border: 1px solid rgba(59, 130, 246, 0.3) !important;
        color: #ffffff !important;
        border-radius: 12px !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.95rem !important;
    }

    .fi-auth-form .fi-input:focus,
    .fi-auth-form .fi-select-input:focus,
    .fi-auth-form .fi-textarea:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
    }

    .fi-auth-form .fi-input::placeholder,
    .fi-auth-form .fi-select-input::placeholder,
    .fi-auth-form .fi-textarea::placeholder {
        color: #94a3b8 !important;
    }

    .fi-auth-form .fi-btn {
        border-radius: 12px !important;
        padding: 0.75rem 1.5rem !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
    }

    .fi-auth-form .fi-btn-color-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        border: none !important;
        color: #fff !important;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4) !important;
    }

    .fi-auth-form .fi-btn-color-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5) !important;
    }

    .fi-auth-form .fi-fo-field-wrp-label,
    .fi-auth-form label {
        color: #e2e8f0 !important;
        font-weight: 500 !important;
        margin-bottom: 0.5rem !important;
    }

    .fi-auth-form .fi-checkbox {
        background: rgba(15, 23, 42, 0.8) !important;
        border: 1px solid rgba(59, 130, 246, 0.3) !important;
        border-radius: 6px !important;
    }

    .fi-auth-form .fi-checkbox:checked {
        background: #3b82f6 !important;
        border-color: #3b82f6 !important;
    }

    .fi-auth-form .fi-checkbox-label,
    .fi-auth-form .fi-fo-field-wrp-description {
        color: #cbd5e1 !important;
    }

    /* Assurer que le body a un fond visible */
    body.fi-body {
        background: #0b1120 !important;
    }

    @media (max-width: 640px) {
        .fi-auth-card {
            margin: 1rem !important;
            border-radius: 16px !important;
        }

        .fi-auth-form {
            padding: 1.5rem !important;
        }

        .fi-auth-form .fi-input,
        .fi-auth-form .fi-select-input,
        .fi-auth-form .fi-textarea {
            font-size: 0.9rem !important;
            padding: 0.7rem 0.9rem !important;
        }

        .fi-auth-form .fi-btn {
            padding: 0.7rem 1.25rem !important;
            font-size: 0.9rem !important;
        }
    }

    /* === Responsive tables Filament : scroll horizontal sur mobile === */
    .fi-ta-ctn,
    .fi-ta-content,
    .fi-ta-container,
    .fi-ta-table-ctn,
    .fi-ta-table-container,
    .fi-ta-wrp,
    .fi-ta-scroll-ctn {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        width: 100% !important;
        max-width: 100% !important;
        display: block !important;
    }

    /* Scrollbar visible pour les tables Filament */
    .fi-ta-ctn::-webkit-scrollbar,
    .fi-ta-content::-webkit-scrollbar,
    .fi-ta-container::-webkit-scrollbar,
    .fi-ta-wrp::-webkit-scrollbar,
    .fi-ta-scroll-ctn::-webkit-scrollbar {
        height: 8px !important;
        -webkit-appearance: none !important;
    }

    .fi-ta-ctn::-webkit-scrollbar-track,
    .fi-ta-content::-webkit-scrollbar-track,
    .fi-ta-container::-webkit-scrollbar-track,
    .fi-ta-wrp::-webkit-scrollbar-track,
    .fi-ta-scroll-ctn::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5) !important;
        border-radius: 4px !important;
    }

    .fi-ta-ctn::-webkit-scrollbar-thumb,
    .fi-ta-content::-webkit-scrollbar-thumb,
    .fi-ta-container::-webkit-scrollbar-thumb,
    .fi-ta-wrp::-webkit-scrollbar-thumb,
    .fi-ta-scroll-ctn::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.5) !important;
        border-radius: 4px !important;
    }

    .fi-ta-ctn::-webkit-scrollbar-thumb:hover,
    .fi-ta-content::-webkit-scrollbar-thumb:hover,
    .fi-ta-container::-webkit-scrollbar-thumb:hover,
    .fi-ta-wrp::-webkit-scrollbar-thumb:hover,
    .fi-ta-scroll-ctn::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.7) !important;
    }

    .fi-ta-ctn,
    .fi-ta-content,
    .fi-ta-container,
    .fi-ta-wrp,
    .fi-ta-scroll-ctn {
        scrollbar-width: auto !important;
        scrollbar-color: rgba(59, 130, 246, 0.5) rgba(15, 23, 42, 0.5) !important;
    }

    .fi-ta {
        min-width: 500px !important;
        width: 100% !important;
        table-layout: auto !important;
    }

    .fi-ta-header-cell,
    .fi-ta-cell {
        white-space: nowrap !important;
    }

    @media (max-width: 640px) {
        .fi-ta-header-cell,
        .fi-ta-cell {
            padding: 0.5rem 0.625rem !important;
            font-size: 0.8rem !important;
        }

        .fi-ta-header-cell .fi-ta-header-cell-label {
            font-size: 0.75rem !important;
        }

        /* Form Filament: stacked layout on mobile */
        .fi-fo-grid-cols-2,
        .fi-fo-grid-cols-3 {
            grid-template-columns: 1fr !important;
        }

        /* Page header: stack heading + actions on mobile */
        .fi-page-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.75rem !important;
        }

        .fi-page-header-heading {
            font-size: 1.1rem !important;
        }

        /* Section padding tighter on mobile */
        .fi-section-content {
            padding: 0.75rem !important;
        }

        /* Notifications/Alerts */
        .fi-notification {
            max-width: calc(100vw - 2rem) !important;
        }

        /* Breadcrumbs hidden on very small screens */
        .fi-breadcrumbs {
            display: none !important;
        }

        /* Relation manager tabs scrollable */
        .fi-relation-manager-tabs {
            overflow-x: auto !important;
        }

        /* Table actions (Créer + Rechercher) stack on mobile */
        .fi-ta-header-toolbar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem !important;
        }

        .fi-ta-header-toolbar > * {
            width: 100% !important;
            justify-content: stretch !important;
        }

        .fi-ta-header-toolbar .fi-input-wrp {
            width: 100% !important;
        }

        .fi-ta-header-toolbar input,
        .fi-ta-header-toolbar .fi-btn {
            width: 100% !important;
            justify-content: center !important;
        }
    }

    @media (max-width: 380px) {
        .fi-page-header-heading {
            font-size: 1rem !important;
        }

        .fi-btn {
            font-size: 0.8rem !important;
            padding: 0.5rem 0.75rem !important;
        }
    }

    /* === Tablet improvements === */
    @media (min-width: 641px) and (max-width: 1023px) {
        .fi-ta {
            min-width: 600px !important;
        }
    }

    /* === Touch targets: ensure 44px minimum === */
    .fi-btn,
    .fi-icon-btn,
    .fi-topbar-open-sidebar-btn button,
    .fi-topbar-close-sidebar-btn button {
        min-height: 44px !important;
        min-width: 44px !important;
        touch-action: manipulation !important;
    }

    /* === Modal / empty-state centered on mobile === */
    @media (max-width: 640px) {
        .fi-modal-window {
            max-width: calc(100vw - 1rem) !important;
            margin: 0.5rem !important;
        }

        .fi-empty-state {
            padding: 1.5rem 1rem !important;
        }

        .fi-empty-state-heading {
            font-size: 1rem !important;
        }

        /* Widget sections: compact padding on mobile */
        .fi-wi-widget .fi-section-content {
            padding: 0.75rem !important;
        }

        /* Form: DateTimePicker stacked, full width */
        .fi-fo-component,
        .fi-fo-field-wrp {
            width: 100% !important;
        }

        /* Textarea: smaller height on mobile */
        .fi-textarea {
            min-height: 120px !important;
        }

        /* Cards in widgets: break long words */
        .fi-wi-widget li,
        .fi-wi-widget p,
        .fi-wi-widget div {
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function addPasswordToggle(input) {
            if (input.dataset.passwordToggle === '1') return;
            input.dataset.passwordToggle = '1';

            const wrapper = input.parentElement;
            if (!wrapper) return;

            wrapper.style.position = 'relative';
            input.style.paddingRight = '2.75rem';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.setAttribute('aria-label', 'Afficher le mot de passe');
            btn.setAttribute('tabindex', '-1');
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>';
            btn.style.cssText = 'position:absolute;right:0.6rem;top:50%;transform:translateY(-50%);width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:transparent;border:none;color:#94a3b8;cursor:pointer;z-index:10;';

            btn.addEventListener('click', function () {
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.setAttribute('aria-label', 'Masquer le mot de passe');
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0A3 3 0 1 0 9.88 9.88m3.65 3.65a3 3 0 0 0 4.243 0 3 3 0 0 0 0-4.243" /></svg>';
                } else {
                    input.type = 'password';
                    btn.setAttribute('aria-label', 'Afficher le mot de passe');
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>';
                }
            });

            wrapper.appendChild(btn);
        }

        function initToggles() {
            document.querySelectorAll('input[type="password"]').forEach(function (input) {
                if (input.closest('.fi-auth-form')) {
                    addPasswordToggle(input);
                }
            });
        }

        initToggles();

        const observer = new MutationObserver(initToggles);
        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>
