<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Annonces publiques
        </x-slot>
        <x-slot name="description">
            Visibles par tous les utilisateurs sur le portail présence. Chaque carte reprend le type défini à la publication (couleurs : information, attention, urgence, rappel).
        </x-slot>

        @if ($annonces->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune annonce publiée pour le moment.</p>
        @else
            <ul class="space-y-3 sm:space-y-4">
                @foreach ($annonces as $annonce)
                    @php $p = $annonce->presentation(); $f = $p['filament']; @endphp
                    <li class="{{ $f['card'] }} break-words">
                        <span class="{{ $f['badge'] }}">{{ $p['label'] }}</span>
                        <p class="{{ $f['title'] }} text-sm sm:text-base break-words">{{ $annonce->titre }}</p>
                        <p class="{{ $f['meta'] }} text-xs">
                            Publié le {{ $annonce->published_at?->translatedFormat('d M Y à H:i') }}
                            @if ($annonce->expires_at)
                                <span class="opacity-90"> · jusqu'au {{ $annonce->expires_at->translatedFormat('d M Y à H:i') }}</span>
                            @endif
                        </p>
                        <div class="{{ $f['body'] }} text-sm break-words overflow-hidden">
                            {!! nl2br(e($annonce->contenu)) !!}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
