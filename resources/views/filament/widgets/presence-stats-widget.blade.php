<x-filament-widgets::widget>
    <style>
        .ps-card {
            background: #111827;
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 18px;
            overflow: hidden;
        }
        .ps-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 20px 10px;
        }
        .ps-title {
            margin: 0;
            color: #f8fafc;
            font-size: 15px;
            font-weight: 700;
        }
        .ps-subtitle {
            margin: 4px 0 0;
            color: #94a3b8;
            font-size: 12px;
        }
        .ps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            padding: 14px 16px 16px;
        }
        .ps-item {
            border-radius: 16px;
            padding: 16px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            background: #0f172a;
        }
        .ps-label {
            margin: 0 0 8px;
            color: #cbd5e1;
            font-size: 12px;
            font-weight: 600;
        }
        .ps-value {
            margin: 0;
            color: #fff;
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }
        .ps-desc {
            margin: 8px 0 0;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.35;
        }
        .ps-item--blue { box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.12); }
        .ps-item--green { box-shadow: inset 0 0 0 1px rgba(34, 197, 94, 0.12); }
        .ps-item--amber { box-shadow: inset 0 0 0 1px rgba(245, 158, 11, 0.12); }
        .ps-item--red { box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.12); }
        .ps-item--blue .ps-value { color: #93c5fd; }
        .ps-item--green .ps-value { color: #86efac; }
        .ps-item--amber .ps-value { color: #fcd34d; }
        .ps-item--red .ps-value { color: #fca5a5; }
    </style>

    <div class="ps-card">
        <div class="ps-header">
            <div>
                <p class="ps-title">Vue d'ensemble de la présence</p>
                <p class="ps-subtitle">{{ $sessionLabel }}</p>
            </div>
        </div>

        <div class="ps-grid">
            @foreach($cards as $card)
                <div class="ps-item ps-item--{{ $card['tone'] }}">
                    <p class="ps-label">{{ $card['label'] }}</p>
                    <p class="ps-value">{{ $card['value'] }}</p>
                    <p class="ps-desc">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
