<x-filament-widgets::widget>
    <style>
        .bas-card {
            background: #111827;
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 18px;
            overflow: hidden;
        }
        .bas-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 20px 10px;
        }
        .bas-title {
            margin: 0;
            color: #f8fafc;
            font-size: 15px;
            font-weight: 700;
        }
        .bas-subtitle {
            margin: 4px 0 0;
            color: #94a3b8;
            font-size: 12px;
        }
        .bas-table {
            width: 100%;
            border-collapse: collapse;
            padding: 0 16px 16px;
        }
        .bas-table th {
            text-align: left;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 16px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
        .bas-table td {
            padding: 10px 16px;
            color: #e2e8f0;
            font-size: 13px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.06);
        }
        .bas-table tr:last-child td {
            border-bottom: none;
        }
        .bas-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }
        .bas-badge--green { background: rgba(34, 197, 94, 0.15); color: #86efac; }
        .bas-badge--amber { background: rgba(245, 158, 11, 0.15); color: #fcd34d; }
        .bas-badge--blue { background: rgba(59, 130, 246, 0.15); color: #93c5fd; }
        .bas-empty {
            padding: 24px 16px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }
    </style>

    <div class="bas-card">
        <div class="bas-header">
            <div>
                <p class="bas-title">Statistiques des agents du bureau</p>
                <p class="bas-subtitle">{{ $moisLabel }}</p>
            </div>
        </div>

        @if($agents->isEmpty())
            <div class="bas-empty">Aucun agent dans votre bureau pour ce mois.</div>
        @else
            <table class="bas-table">
                <thead>
                    <tr>
                        <th>Agent</th>
                        <th>Matricule</th>
                        <th style="text-align:center">Présents</th>
                        <th style="text-align:center">Retards</th>
                        <th style="text-align:center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agents as $agent)
                        <tr>
                            <td>{{ $agent->nom ?? $agent->name }}</td>
                            <td>{{ $agent->matricule }}</td>
                            <td style="text-align:center">
                                <span class="bas-badge bas-badge--green">{{ $agent->presences_present_count }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="bas-badge bas-badge--amber">{{ $agent->presences_retard_count }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="bas-badge bas-badge--blue">{{ $agent->presences_total_count }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-filament-widgets::widget>
