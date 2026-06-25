@php
    $cfg = config('presence_documents');
    $leftUrl = ! empty($cfg['logo_left']) && file_exists(public_path($cfg['logo_left']))
        ? asset($cfg['logo_left'])
        : null;
    $rightUrl = ! empty($cfg['logo_right']) && file_exists(public_path($cfg['logo_right']))
        ? asset($cfg['logo_right'])
        : null;
@endphp

<div {{ $attributes->class(['max-w-6xl mx-auto w-full min-w-0 px-4 sm:px-6 py-5 sm:py-6']) }}>
    {{-- Mobile : logos côte à côte, texte centré en dessous --}}
    <div class="flex md:hidden flex-col gap-5">
        <div class="flex flex-row items-center justify-center gap-10 sm:gap-14">
            <div class="shrink-0 w-[4.5rem] h-[4.5rem] sm:w-20 sm:h-20 flex items-center justify-center">
                @if($leftUrl)
                    <img src="{{ $leftUrl }}" alt="Emblème institutionnel" class="max-w-full max-h-full w-auto h-auto object-contain rounded-full border border-slate-200 shadow-sm">
                @else
                    <div class="w-full h-full rounded-full border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center text-[0.6rem] text-slate-500 text-center leading-tight px-1">Logo</div>
                @endif
            </div>
            <div class="shrink-0 w-[5rem] h-[5.5rem] sm:w-24 sm:h-28 flex items-center justify-center">
                @if($rightUrl)
                    <img src="{{ $rightUrl }}" alt="Armoiries RDC" class="max-w-full max-h-full w-auto h-auto object-contain">
                @else
                    <div class="w-full h-full border border-dashed border-amber-200 bg-amber-50/80 flex items-center justify-center text-[0.6rem] text-amber-900/70 text-center leading-tight px-1">Armoiries RDC</div>
                @endif
            </div>
        </div>
        <div class="text-center space-y-1.5 px-1">
            <p class="font-serif text-blue-800 font-bold text-sm sm:text-base leading-snug">{{ $cfg['line1'] }}</p>
            <p class="font-serif text-amber-900 font-bold text-[11px] sm:text-xs uppercase tracking-wide leading-snug">{{ $cfg['line2'] }}</p>
            <p class="text-blue-950 font-bold text-[10px] sm:text-[11px] uppercase leading-snug">{{ $cfg['line3'] }}</p>
            <p class="text-blue-700 text-xs sm:text-sm font-medium">{{ $cfg['line4'] }}</p>
            <p class="text-slate-800 text-[10px] sm:text-xs leading-relaxed max-w-xl mx-auto border-t border-slate-200/80 pt-2 mt-2">{{ $cfg['line5'] }}</p>
        </div>
        <div class="h-0 border-b-2 border-slate-900 max-w-md mx-auto w-full"></div>
    </div>

    {{-- Desktop / tablette : trois colonnes --}}
    <div class="hidden md:flex flex-row items-stretch justify-between gap-6 lg:gap-10">
        <div class="shrink-0 w-24 lg:w-28 flex items-center justify-center self-center">
            @if($leftUrl)
                <img src="{{ $leftUrl }}" alt="Emblème institutionnel" class="max-w-[5.5rem] lg:max-w-[6.5rem] max-h-24 lg:max-h-28 w-auto h-auto object-contain rounded-full border border-slate-200 shadow-sm">
            @else
                <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-full border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center text-xs text-slate-500 text-center p-2">Logo</div>
            @endif
        </div>
        <div class="flex-1 min-w-0 text-center flex flex-col justify-center space-y-1 lg:space-y-1.5 py-1">
            <p class="font-serif text-blue-800 font-bold text-base lg:text-lg">{{ $cfg['line1'] }}</p>
            <p class="font-serif text-amber-900 font-bold text-xs lg:text-sm uppercase tracking-wide">{{ $cfg['line2'] }}</p>
            <p class="text-blue-950 font-bold text-[11px] lg:text-xs uppercase leading-snug">{{ $cfg['line3'] }}</p>
            <p class="text-blue-700 text-sm">{{ $cfg['line4'] }}</p>
            <p class="text-slate-800 text-[10px] lg:text-xs leading-relaxed max-w-3xl mx-auto mt-1">{{ $cfg['line5'] }}</p>
        </div>
        <div class="shrink-0 w-24 lg:w-28 flex items-center justify-center self-center">
            @if($rightUrl)
                <img src="{{ $rightUrl }}" alt="Armoiries RDC" class="max-w-[5.5rem] lg:max-w-[6.5rem] max-h-28 w-auto h-auto object-contain">
            @else
                <div class="w-[5.5rem] h-24 lg:w-28 lg:h-28 border border-dashed border-amber-200 bg-amber-50/80 flex items-center justify-center text-xs text-amber-900/70 text-center p-2">Armoiries RDC</div>
            @endif
        </div>
    </div>
    <div class="hidden md:block h-0 border-b-2 border-slate-900 mt-5 lg:mt-6"></div>
</div>
