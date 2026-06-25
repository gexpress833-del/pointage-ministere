{{-- Préfère Vite (prod / npm run dev) ; repli CDN uniquement si aucun build ni serveur Vite --}}
@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css'])
@else
    <script src="https://cdn.tailwindcss.com"></script>
@endif
