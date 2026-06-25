@php
    $variant = $annoncesVariant ?? 'portal';
@endphp
@if(isset($annoncesActives) && $annoncesActives->isNotEmpty())
    <div class="space-y-3" role="region" aria-label="Annonces">
        @foreach ($annoncesActives as $annonce)
            @php
                $p = $annonce->presentation();
                $slot = $variant === 'plain' ? $p['plain'] : $p['portal'];
            @endphp
            @if($variant === 'plain')
                <div class="{{ $slot['card'] }}">
                    <div class="flex items-start gap-3.5">
                        <div class="{{ $slot['iconWrap'] }}">
                            @switch($annonce->niveau)
                                @case(\App\Models\Annonce::NIVEAU_ATTENTION)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $slot['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    @break
                                @case(\App\Models\Annonce::NIVEAU_URGENCE)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $slot['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75l9-8.25Z" />
                                    </svg>
                                    @break
                                @case(\App\Models\Annonce::NIVEAU_RAPPEL)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $slot['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.082A2.02 2.02 0 0 0 22 14.02V10a2 2 0 0 0-2-2h-2.343M11 7H6.343a2 2 0 0 0-2 2v4.02c0 .566.227 1.106.589 1.502.364.396.86.634 1.397.634h.001c.196 0 .393-.02.58-.06M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    @break
                                @default
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $slot['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                            @endswitch
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="inline-flex items-center rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wide {{ match($annonce->niveau) {
                                \App\Models\Annonce::NIVEAU_ATTENTION => 'bg-amber-600 text-white',
                                \App\Models\Annonce::NIVEAU_URGENCE => 'bg-rose-950/35 text-red-500 border border-red-500/45',
                                \App\Models\Annonce::NIVEAU_RAPPEL => 'bg-violet-600 text-white',
                                default => 'bg-sky-600 text-white',
                            } }}">{{ $p['label'] }}</p>
                            <h3 class="{{ $slot['title'] }} mt-2 text-sm leading-6">{{ $annonce->titre }}</h3>
                            <p class="{{ $slot['meta'] }} mt-1 leading-6">
                                {{ $annonce->published_at?->translatedFormat('d M Y à H:i') }}
                                @if($annonce->expires_at)
                                    <span class="opacity-80"> · jusqu'au {{ $annonce->expires_at->translatedFormat('d M Y') }}</span>
                                @endif
                            </p>
                            <div class="{{ $slot['body'] }} mt-2 text-sm leading-7">{{ $annonce->contenu }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="{{ $slot['card'] }}">
                    <div class="flex items-start gap-3 sm:gap-3.5">
                        <div class="{{ $slot['iconWrap'] }} w-9 h-9 sm:w-10 sm:h-10 mt-0.5">
                            @switch($annonce->niveau)
                                @case(\App\Models\Annonce::NIVEAU_ATTENTION)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    @break
                                @case(\App\Models\Annonce::NIVEAU_URGENCE)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75l9-8.25Z" />
                                    </svg>
                                    @break
                                @case(\App\Models\Annonce::NIVEAU_RAPPEL)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.082A2.02 2.02 0 0 0 22 14.02V10a2 2 0 0 0-2-2h-2.343M11 7H6.343a2 2 0 0 0-2 2v4.02c0 .566.227 1.106.589 1.502.364.396.86.634 1.397.634h.001c.196 0 .393-.02.58-.06M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    @break
                                @default
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                            @endswitch
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="inline-flex items-center rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wide {{ match($annonce->niveau) {
                                \App\Models\Annonce::NIVEAU_ATTENTION => 'bg-amber-500/45 text-amber-50',
                                \App\Models\Annonce::NIVEAU_URGENCE => 'bg-rose-950/35 text-red-400 border border-red-500/45',
                                \App\Models\Annonce::NIVEAU_RAPPEL => 'bg-violet-500/45 text-violet-50',
                                default => 'bg-sky-500/45 text-sky-50',
                            } }}">{{ $p['label'] }}</p>
                            <h3 class="{{ $slot['title'] }} mt-2 text-sm sm:text-base leading-6">{{ $annonce->titre }}</h3>
                            <p class="text-xs mt-1.5 leading-6">
                                <span class="{{ $slot['metaStrong'] }}">{{ $annonce->published_at?->translatedFormat('d M Y à H:i') }}</span>
                                @if($annonce->expires_at)
                                    <span class="{{ $slot['metaMuted'] }}"> · valable jusqu'au {{ $annonce->expires_at->translatedFormat('d M Y') }}</span>
                                @endif
                            </p>
                            <div class="{{ $slot['body'] }} mt-2.5 text-sm leading-7">{{ $annonce->contenu }}</div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
