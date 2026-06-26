@php
    /** @var \App\Models\User $record */
    $hasPhoto = filled($record->photo_reference);
    $initials = mb_strtoupper(mb_substr($record->nom ?? $record->name ?? '?', 0, 2));
@endphp

<div style="display:flex;align-items:center;">
    @if($hasPhoto)
        <img
            src="{{ route('users.photo-reference', $record) }}"
            alt="Photo de {{ $record->getDisplayName() }}"
            style="width:40px;height:40px;border-radius:999px;object-fit:cover;border:1px solid rgba(148,163,184,.25);display:block;"
        >
    @else
        <div style="width:40px;height:40px;border-radius:999px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">
            {{ $initials }}
        </div>
    @endif
</div>
