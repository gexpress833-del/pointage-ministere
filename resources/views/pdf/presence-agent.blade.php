<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport individuel - {{ $user->nom }}</title>
    <style>
        @page { margin: 18mm 14mm 16mm 14mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
        }
        .band {
            background: #1e40af;
            color: #fff;
            padding: 14px 18px;
            margin: 0 0 14px 0;
            border-radius: 8px;
        }
        .band h1 { margin: 0 0 4px 0; font-size: 13pt; font-weight: bold; }
        .band .sub { margin: 0; font-size: 10pt; opacity: 0.95; }
        .band .meta { margin-top: 10px; font-size: 8.5pt; opacity: 0.88; }
        .agent-info {
            display: table;
            width: 100%;
            margin-bottom: 14px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .agent-info .cell {
            display: table-cell;
            width: 25%;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
        }
        .agent-info .cell strong { font-size: 14pt; color: #1e40af; display: block; }
        .agent-info .cell span { font-size: 7.5pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 8.5pt;
        }
        table.data th {
            background: #334155;
            color: #f8fafc;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #1e293b;
        }
        table.data td {
            padding: 7px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }
        table.data .num { text-align: center; width: 28px; }
        table.data .time { font-family: DejaVu Sans Mono, monospace; text-align: center; white-space: nowrap; }
        table.data .statut { text-align: center; text-transform: capitalize; }
        .footer {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 8pt;
            color: #64748b;
        }
        .empty {
            text-align: center;
            padding: 24px;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>
    @include('pdf.partials.official-institutional-header')

    <div class="band">
        <h1>{{ $rapportTitle ?? config('app.name') }}</h1>
        <p class="sub">Rapport individuel de présence</p>
        <p class="meta">
            Agent : <strong>{{ $user->nom ?? $user->name }}</strong>
            &nbsp;·&nbsp; Matricule : <strong>{{ $user->matricule }}</strong>
            &nbsp;·&nbsp; Période : <strong>{{ $startDate }} — {{ $endDate }}</strong>
        </p>
    </div>

    <div class="agent-info">
        <div class="cell">
            <strong>{{ $totalSessions }}</strong>
            <span>Sessions totales</span>
        </div>
        <div class="cell">
            <strong>{{ $presentCount }}</strong>
            <span>Présents</span>
        </div>
        <div class="cell">
            <strong>{{ $retardCount }}</strong>
            <span>Retards</span>
        </div>
        <div class="cell">
            <strong>{{ $absenceCount }}</strong>
            <span>Absences</span>
        </div>
    </div>

    @if ($presences->isEmpty())
        <p class="empty">Aucune présence enregistrée sur cette période.</p>
    @else
    <table class="data">
        <thead>
            <tr>
                <th class="num">N°</th>
                <th>Date</th>
                <th>Heure d'arrivée</th>
                <th>Heure de départ</th>
                <th>Durée</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($presences as $index => $p)
            @php
                $jour = $p->session->date->format('Y-m-d');
                $arr = \Carbon\Carbon::parse($jour.' '.$p->heure_arrivee);
                $dep = $p->heure_depart ? \Carbon\Carbon::parse($jour.' '.$p->heure_depart) : null;
                $duree = '—';
                if ($dep) {
                    $min = max(0, $arr->diffInMinutes($dep));
                    $h = intdiv($min, 60);
                    $m = $min % 60;
                    $duree = $h > 0 ? "{$h} h {$m} min" : "{$m} min";
                }
                $libStatut = match ($p->statut) {
                    'present' => 'Présent',
                    'retard' => 'Retard',
                    'absent' => 'Absent',
                    default => $p->statut,
                };
            @endphp
            <tr>
                <td class="num">{{ $index + 1 }}</td>
                <td>{{ $p->session->date->format('d/m/Y') }}</td>
                <td class="time">{{ \Carbon\Carbon::parse($p->heure_arrivee)->format('H:i') }}</td>
                <td class="time">{{ $p->heure_depart ? \Carbon\Carbon::parse($p->heure_depart)->format('H:i') : '—' }}</td>
                <td class="time">{{ $duree }}</td>
                <td class="statut">{{ $libStatut }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <p class="footer">
        Document généré le {{ now()->translatedFormat('d F Y à H:i') }}
        — {{ $presences->count() }} présence(s) — {{ config('app.name') }}
    </p>
</body>
</html>
