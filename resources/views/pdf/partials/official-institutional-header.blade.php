@php
    $cfg = config('presence_documents');
    $leftRel = $cfg['logo_left'] ?? null;
    $rightRel = $cfg['logo_right'] ?? null;
    $leftAbs = $leftRel && is_string($leftRel) && file_exists(public_path($leftRel))
        ? str_replace('\\', '/', public_path($leftRel))
        : null;
    $rightAbs = $rightRel && is_string($rightRel) && file_exists(public_path($rightRel))
        ? str_replace('\\', '/', public_path($rightRel))
        : null;
@endphp
<table class="official-doc-header" cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;margin:0 0 10px 0;">
    <tr>
        <td style="width:17%;vertical-align:middle;text-align:center;padding:4px 6px 8px 0;">
            @if($leftAbs)
                <img src="{{ $leftAbs }}" alt="" style="max-width:68px;max-height:68px;width:auto;height:auto;display:inline-block;object-fit:contain;">
            @else
                <table cellspacing="0" cellpadding="0" style="margin:0 auto;width:64px;height:64px;border-radius:50%;border:1px solid #cbd5e1;background:#f8fafc;"><tr><td style="font-size:5.5pt;color:#64748b;text-align:center;vertical-align:middle;line-height:1.15;padding:4px;">Logo<br/>institutionnel</td></tr></table>
            @endif
        </td>
        <td style="width:66%;vertical-align:middle;text-align:center;padding:4px 8px 8px 8px;">
            <div style="font-family:'DejaVu Serif',DejaVu Sans,serif;font-size:9.5pt;color:#1e40af;font-weight:bold;margin:0 0 2px 0;">{{ $cfg['line1'] }}</div>
            <div style="font-family:'DejaVu Serif',DejaVu Sans,serif;font-size:8.2pt;color:#9a3412;font-weight:bold;margin:0 0 3px 0;letter-spacing:0.02em;">{{ $cfg['line2'] }}</div>
            <div style="font-family:DejaVu Sans,sans-serif;font-size:7.3pt;color:#1e3a8a;font-weight:bold;margin:0 0 2px 0;line-height:1.2;">{{ $cfg['line3'] }}</div>
            <div style="font-family:DejaVu Sans,sans-serif;font-size:6.8pt;color:#1d4ed8;margin:0 0 4px 0;">{{ $cfg['line4'] }}</div>
            <div style="font-family:DejaVu Sans,sans-serif;font-size:6pt;color:#0f172a;line-height:1.25;margin:0;">{{ $cfg['line5'] }}</div>
        </td>
        <td style="width:17%;vertical-align:middle;text-align:center;padding:4px 0 8px 6px;">
            @if($rightAbs)
                <img src="{{ $rightAbs }}" alt="" style="max-width:72px;max-height:76px;width:auto;height:auto;display:inline-block;object-fit:contain;">
            @else
                <table cellspacing="0" cellpadding="0" style="margin:0 auto;width:70px;height:76px;border:1px solid #cbd5e1;background:#fffef8;"><tr><td style="font-size:5.5pt;color:#92400e;text-align:center;vertical-align:middle;line-height:1.1;padding:4px;">Armoiries<br/>RDC</td></tr></table>
            @endif
        </td>
    </tr>
</table>
<div style="height:0;border-bottom:2px solid #0f172a;margin:0 0 12px 0;width:100%;"></div>
