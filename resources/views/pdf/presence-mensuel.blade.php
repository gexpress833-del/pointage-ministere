<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport mensuel — {{ $month }}</title>
    <style>
        @page { margin: 10mm 7mm 12mm 7mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7.5pt;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
        }
        .band {
            background: #1e40af;
            color: #fff;
            padding: 10px 14px;
            margin: 0 0 8px 0;
            border-radius: 6px;
        }
        .band h1 {
            margin: 0 0 2px 0;
            font-size: 11pt;
            font-weight: bold;
        }
        .band .sub {
            margin: 0;
            font-size: 8.5pt;
            opacity: 0.95;
        }
        .band .meta {
            margin-top: 6px;
            font-size: 7pt;
            opacity: 0.88;
        }
        .legend {
            margin: 0 0 8px 0;
            padding: 8px 10px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 6.5pt;
            color: #334155;
            line-height: 1.45;
        }
        .legend strong { color: #0f172a; }
        .legend-row { margin: 0 0 4px 0; }
        .legend-row:last-child { margin-bottom: 0; }
        .legend span { margin-right: 12px; white-space: nowrap; display: inline-block; }
        .sym { display: inline-block; min-width: 14px; text-align: center; font-weight: bold; }

        table.grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #94a3b8;
        }
        table.grid thead th {
            background: #334155;
            color: #f8fafc;
            font-weight: bold;
            padding: 5px 3px;
            border: 1px solid #1e293b;
            font-size: 6.5pt;
            vertical-align: middle;
            word-wrap: break-word;
        }
        /* Les classes .col-* ciblent aussi le tbody : forcer le bandeau d’en-tête ligne 1 */
        table.grid thead tr:first-child > th.col-nom,
        table.grid thead tr:first-child > th.col-mat,
        table.grid thead tr:first-child > th.col-bureau {
            background: #334155 !important;
            color: #f8fafc !important;
        }
        table.grid thead tr.daynums th {
            background: #475569;
            padding: 3px 1px;
            font-size: 6.5pt;
        }
        table.grid thead th.thead-spacer {
            background: #475569 !important;
            color: #cbd5e1 !important;
            padding: 2px 3px;
            font-size: 5pt;
            font-weight: normal;
        }
        table.grid tbody td {
            border: 1px solid #cbd5e1;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            font-size: 6.5pt;
        }
        table.grid tbody td.col-total {
            font-weight: bold;
        }
        table.grid .col-nom {
            width: 14%;
            text-align: left !important;
            padding-left: 5px !important;
            font-size: 7pt;
            font-weight: 600;
            color: #0f172a;
            background: #f8fafc;
        }
        table.grid .col-mat {
            width: 6%;
            font-family: DejaVu Sans Mono, DejaVu Sans, monospace;
            font-size: 6.5pt;
            background: #f8fafc;
        }
        table.grid .col-bureau {
            width: 11%;
            text-align: left !important;
            padding-left: 4px !important;
            font-size: 6pt;
            color: #334155;
            background: #f8fafc;
        }
        table.grid .col-day {
            width: 1.72%;
            max-width: 0;
        }
        table.grid .col-total {
            width: 6%;
            min-width: 3em;
            max-width: 6%;
            font-weight: bold;
            background: #e2e8f0;
            color: #0f172a;
            border-left: 2px solid #64748b !important;
            text-align: center;
            vertical-align: middle;
            padding-left: 2px !important;
            padding-right: 2px !important;
        }
        table.grid thead th.col-total {
            font-size: 5.5pt;
            line-height: 1.2;
            padding: 5px 2px;
            hyphens: none;
        }
        /* Statuts : discrets, style administratif */
        .cell-present {
            background: #d1fae5;
            color: #065f46;
            font-weight: bold;
        }
        .cell-retard {
            background: #fef3c7;
            color: #92400e;
            font-weight: bold;
        }
        .cell-absent {
            background: #fee2e2;
            color: #991b1b;
            font-weight: bold;
        }
        /* Jour sans session ouvert : neutre */
        .cell-neutre {
            background: #f8fafc;
            color: #94a3b8;
        }
        /* Jour civil non encore atteint dans le mois affiché */
        .cell-futur {
            background: #f1f5f9;
            color: #cbd5e1;
            font-weight: normal;
        }
        /* Jour férié (sans session ce jour-là) */
        .cell-ferie {
            background: #e0e7ff;
            color: #3730a3;
            font-weight: bold;
        }
        /* Week-end sans session */
        .cell-weekend {
            background: #f1f5f9;
            color: #64748b;
        }
        table.grid thead tr.dayweek th {
            background: #64748b;
            padding: 2px 1px;
            font-size: 5.5pt;
            font-weight: 600;
        }
        /* Hors mois */
        .cell-na {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }
        .footer {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            font-size: 6.5pt;
            color: #64748b;
        }
    </style>
</head>
<body>
    @include('pdf.partials.official-institutional-header')
    @php
        $titre = $rapportTitle ?? config('presence.report_title', config('app.name'));
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $monthNum, 1)->daysInMonth;
        $joursSession = $joursAvecSession ?? [];
        $calendarByDay = $calendarByDay ?? [];
    @endphp

    <div class="band">
        <h1>{{ $titre }}</h1>
        <p class="sub">Registre mensuel de présence</p>
        <p class="meta">Période : <strong>{{ $month }}</strong> &nbsp;·&nbsp; Généré le {{ now()->translatedFormat('d F Y à H:i') }} &nbsp;·&nbsp; {{ config('app.name') }}</p>
    </div>

    <div class="legend">
        <div class="legend-row">
            <strong>Légende :</strong>
            <span><span class="sym" style="color:#065f46">P</span> Présent à l'heure</span>
            <span><span class="sym" style="color:#92400e">R</span> Retard</span>
            <span><span class="sym" style="color:#991b1b">A</span> Absence (session ouverte)</span>
            <span><span class="sym" style="color:#94a3b8">·</span> Jour ouvré sans session</span>
            <span><span class="sym" style="color:#64748b">·</span> Week-end sans session</span>
        </div>
        <div class="legend-row">
            <span><span class="sym" style="color:#3730a3">F</span> Jour férié (sans session)</span>
            <span><span class="sym" style="color:#cbd5e1">–</span> Jour non encore écoulé</span>
            <span style="margin-right:0">Fériés RDC (config.) · Détail horaire : <strong>rapports journaliers</strong>.</span>
        </div>
    </div>

    <table class="grid">
        <thead>
            {{-- Pas de rowspan sur Nom / Matricule / Bureau : certains moteurs PDF masquent la 2e cellule. --}}
            <tr>
                <th class="col-nom">Nom</th>
                <th class="col-mat">Matricule</th>
                <th class="col-bureau">Bureau / service</th>
                {{-- Toujours 31 colonnes-jours (jours hors mois en grisé) : le colspan doit être 31, pas daysInMonth, sinon le tableau est désaligné (ex. avril 30 jours). --}}
                <th colspan="31" style="text-align:center;letter-spacing:0.02em;">Jours du mois</th>
                <th class="col-total" rowspan="3">Jours<br/>pointés</th>
            </tr>
            <tr class="daynums">
                <th class="col-nom thead-spacer">&nbsp;</th>
                <th class="col-mat thead-spacer">&nbsp;</th>
                <th class="col-bureau thead-spacer">&nbsp;</th>
                @for ($d = 1; $d <= 31; $d++)
                    @if ($d <= $daysInMonth)
                        <th class="col-day">{{ $d }}</th>
                    @else
                        <th class="col-day cell-na"></th>
                    @endif
                @endfor
            </tr>
            <tr class="dayweek">
                <th class="col-nom thead-spacer">&nbsp;</th>
                <th class="col-mat thead-spacer">&nbsp;</th>
                <th class="col-bureau thead-spacer">&nbsp;</th>
                @for ($d = 1; $d <= 31; $d++)
                    @if ($d <= $daysInMonth && isset($calendarByDay[$d]))
                        <th class="col-day">{{ $calendarByDay[$d]['weekday_abbr'] ?? '' }}</th>
                    @else
                        <th class="col-day cell-na"></th>
                    @endif
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            @php
                $userPresencesByDay = [];
                foreach ($sessions as $s) {
                    foreach ($s->presences as $p) {
                        if ($p->user_id === $user->id) {
                            $userPresencesByDay[(int) $s->date->format('j')] = $p->statut;
                        }
                    }
                }
                $count = count($userPresencesByDay);
            @endphp
            <tr>
                <td class="col-nom">{{ $user->nom ?? $user->name }}</td>
                <td class="col-mat">{{ $user->matricule }}</td>
                <td class="col-bureau">{{ $user->bureau?->nom_bureau ?? '—' }}</td>
                @for ($d = 1; $d <= 31; $d++)
                    @if ($d > $daysInMonth)
                        <td class="col-day cell-na"></td>
                    @else
                        @php
                            $statut = $userPresencesByDay[$d] ?? null;
                            $sessionCeJour = !empty($joursSession[$d]);
                            $cal = $calendarByDay[$d] ?? [];
                        @endphp
                        @if ($sessionCeJour)
                            @if ($statut === 'present')
                                <td class="col-day cell-present">P</td>
                            @elseif ($statut === 'retard')
                                <td class="col-day cell-retard">R</td>
                            @else
                                <td class="col-day cell-absent">A</td>
                            @endif
                        @elseif (!empty($cal['future']))
                            <td class="col-day cell-futur">–</td>
                        @elseif (!empty($cal['ferie']))
                            <td class="col-day cell-ferie" title="{{ $cal['ferie'] }}">F</td>
                        @elseif (!empty($cal['weekend']))
                            <td class="col-day cell-weekend">·</td>
                        @else
                            <td class="col-day cell-neutre">·</td>
                        @endif
                    @endif
                @endfor
                <td class="col-total">{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">
        Document confidentiel — usage interne. Total agents listés : {{ $users->count() }}.
        @if($sessions->isEmpty())
            <strong>Aucune session</strong> enregistrée sur cette période : la grille indique uniquement les jours sans session.
        @endif
    </p>
</body>
</html>
